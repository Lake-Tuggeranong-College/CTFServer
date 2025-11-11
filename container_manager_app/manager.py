#!/usr/bin/env python3
import os
import time
import subprocess
import threading
import traceback
from datetime import datetime, timedelta, timezone
import pymysql
# Requires: pip install pymysql pymysqlreplication docker
from pymysqlreplication import BinLogStreamReader
from pymysqlreplication.row_event import WriteRowsEvent, UpdateRowsEvent, DeleteRowsEvent
import docker # Requires docker-py to be installed

# =========================
# CONFIG
# =========================
# Database connection details for the manager's operational queries
DB = {'host':'CTF-MySQL','user':'devuser','passwd':'devpass','port':3306,'db':'CyberCity'}
# Binlog connection details (must match DB)
BINLOG_CONN = {'host':DB['host'],'port':DB['port'],'user':DB['user'],'passwd':DB['passwd']} 

TABLE_CONTAINERS = "DockerContainers"   # The table being monitored
TABLE_CHALLENGES = "Challenges"         # Used to fetch the Docker image name

# Define the port range for exposed services
BASE_PORT, MAX_PORT = 17001, 17999

# --- HARD LIFETIME LIMIT (default 10 minutes) ---
def _read_time_limit_minutes() -> int:
    try:
        # Default to 15 minutes, based on the docker-compose environment variable snippet
        v = int(os.getenv("CLEANUP_DURATION_MINUTES", "15"))
        return max(1, v)
    except Exception:
        return 15

TIME_LIMIT_MINUTES = _read_time_limit_minutes()

# Structure: ID -> {challengeID, userID, port, delete_time}
active_containers = {}  
# Column names detected from the DB, used for reliable binlog parsing
TABLE_COLS = []         
# Cache for challenge image names
IMAGE_CACHE = {}              

# Initialize Docker client (requires /var/run/docker.sock mount)
try:
    DOCKER_CLIENT = docker.from_env()
except Exception as e:
    print(f"FATAL: Could not connect to Docker daemon. Ensure /var/run/docker.sock is mounted. Error: {e}", flush=True)
    DOCKER_CLIENT = None

# =========================
# UTILS
# =========================

def log(message: str):
    """Prints timestamped log messages."""
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    print(f"[{timestamp}] [DB2Docker] {message}", flush=True)

def now_utc() -> datetime:
    """Returns the current UTC time with timezone info."""
    return datetime.now(timezone.utc)

def load_cols():
    """Detects the column order of the DockerContainers table for binlog index mapping."""
    global TABLE_COLS
    try:
        with pymysql.connect(**DB) as conn:
            with conn.cursor() as cursor:
                sql = f"SHOW COLUMNS FROM {TABLE_CONTAINERS}"
                cursor.execute(sql)
                TABLE_COLS = [row[0] for row in cursor.fetchall()]
        log(f"Detected DockerContainers columns: {TABLE_COLS}")
    except Exception as e:
        log(f"Failed to load table columns: {e}. Retrying in 5s...")
        time.sleep(5)
        load_cols() # Retry until successful

def get_challenge_image(challenge_id: int) -> str | None:
    """Fetches the Docker image name from the Challenges table."""
    if challenge_id in IMAGE_CACHE and IMAGE_CACHE[challenge_id].get("timestamp", 0) > time.time() - 300:
        return IMAGE_CACHE[challenge_id].get("image")

    try:
        with pymysql.connect(**DB) as conn:
            with conn.cursor() as cursor:
                sql = f"SELECT Image FROM {TABLE_CHALLENGES} WHERE ID = %s"
                cursor.execute(sql, (challenge_id,))
                result = cursor.fetchone()
                image = str(result[0]) if result and result[0] else None
                
                IMAGE_CACHE[challenge_id] = {"image": image, "timestamp": time.time()}
                return image
    except Exception as e:
        log(f"Error fetching challenge image for ID {challenge_id}: {e}")
        return None

# =========================
# PORT MANAGEMENT
# =========================

def get_available_port() -> int | None:
    """Finds the next unused port in the defined range."""
    used_ports = set()
    try:
        with pymysql.connect(**DB) as conn:
            with conn.cursor(pymysql.cursors.DictCursor) as cursor:
                # Query for all actively assigned ports
                sql = f"SELECT port FROM {TABLE_CONTAINERS} WHERE port IS NOT NULL AND port != 0"
                cursor.execute(sql)
                for row in cursor.fetchall():
                    port = row.get('port')
                    if port is not None:
                         used_ports.add(int(port))
    except Exception as e:
        log(f"Error querying used ports: {e}")
        return None

    # Find the first available port
    for port in range(BASE_PORT, MAX_PORT + 1):
        if port not in used_ports:
            return port

    log(f"WARNING: No available ports found in the range {BASE_PORT}-{MAX_PORT}")
    return None

def update_container_port(row_id: int, new_port: int) -> bool:
    """Updates the port field in the DockerContainers table for the given row_id."""
    try:
        with pymysql.connect(**DB) as conn:
            with conn.cursor() as cursor:
                sql = f"UPDATE {TABLE_CONTAINERS} SET port = %s WHERE ID = %s"
                rows_affected = cursor.execute(sql, (new_port, row_id))
            conn.commit()
            if rows_affected > 0:
                log(f"DB Update SUCCESS: Port for ID {row_id} set to {new_port}")
                return True
            else:
                log(f"DB Update WARNING: Port update for ID {row_id} affected 0 rows.")
                return False
    except Exception as e:
        log(f"DB Update ERROR: Failed to update port for ID {row_id}: {e}")
        return False

# =========================
# DOCKER LIFECYCLE
# =========================

def spawn_container(challenge_id: int, user_id: int, port: int) -> bool:
    """Spawns a new Docker container using the docker-py client."""
    if not DOCKER_CLIENT:
        log("FATAL: Docker client not initialized. Cannot spawn container.")
        return False

    image_name = get_challenge_image(challenge_id)
    if not image_name:
        log(f"ERROR: Could not find image name for challenge ID {challenge_id}. Skipping spawn.")
        return False
    
    container_name = f"user{user_id}-challenge{challenge_id}"
    host_port = port
    container_ssh_port = 22 # Assumed internal SSH port

    log(f"Attempting to spawn container {container_name} ({image_name}) on host port {host_port}")

    try:
        # Use DOCKER_CLIENT.containers.run for simpler lifecycle management
        DOCKER_CLIENT.containers.run(
            image=image_name,
            name=container_name,
            detach=True,        # Run in background
            remove=True,        # Automatically remove when stopped
            ports={f'{container_ssh_port}/tcp': host_port}, # Map container port to host port
            # Add resource limits if necessary (e.g., mem_limit='512m', cpu_period=100000, cpu_quota=20000)
        )
        log(f"Docker SUCCESS: Started container {container_name} on port {port}")
        return True
    except docker.errors.ImageNotFound:
        log(f"Docker ERROR: Image '{image_name}' not found locally or on registry.")
        return False
    except docker.errors.ContainerError as e:
        log(f"Docker ERROR: Container failed to start: {e}")
        return False
    except Exception as e:
        log(f"Docker UNEXPECTED ERROR during spawn: {e}")
        return False

def remove_container(user_id: int, challenge_id: int, row_id: int):
    """Stops/Removes the Docker container and cleans up the database entry."""
    container_name = f"user{user_id}-challenge{challenge_id}"
    log(f"Attempting to stop and remove container {container_name} (DB ID: {row_id})")
    
    try:
        # 1. Stop/Remove Docker Container
        try:
            container = DOCKER_CLIENT.containers.get(container_name)
            container.stop()
            log(f"Docker SUCCESS: Stopped container {container_name}")
        except docker.errors.NotFound:
            log(f"Docker WARNING: Container {container_name} not found, likely already removed.")
        
        # 2. Delete row from DB
        with pymysql.connect(**DB) as conn:
            with conn.cursor() as cursor:
                sql = f"DELETE FROM {TABLE_CONTAINERS} WHERE ID = %s"
                cursor.execute(sql, (row_id,))
            conn.commit()
            log(f"DB SUCCESS: Deleted DB entry for ID {row_id}.")
            
        # 3. Remove from active_containers cache
        if row_id in active_containers:
            del active_containers[row_id]
            
    except Exception as e:
        log(f"ERROR: Failed to clean up container {container_name} or DB entry {row_id}: {e}")

# =========================
# THREADS
# =========================

def time_tracker():
    """Periodically checks active containers for time-out."""
    while True:
        time.sleep(60)
        log("Running time-out sweep...")
        
        now = now_utc()
        # Iterate over a copy of items to allow deletion from the original dict
        for rid, info in list(active_containers.items()):
            if now >= info["delete_time"]:
                remove_container(
                    user_id=info["userID"], 
                    challenge_id=info["challengeID"], 
                    row_id=rid
                )
        log("Time-out sweep complete.")


def process_binlog_event():
    """Monitors the MySQL binlog for container start requests."""
    stream = None
    try:
        log("Starting binlog monitoring...")
        stream = BinLogStreamReader(
            connection_settings=BINLOG_CONN, 
            server_id=100,
            only_events=[WriteRowsEvent, UpdateRowsEvent, DeleteRowsEvent],
            blocking=True,
            resume_stream=True,
            only_tables=[TABLE_CONTAINERS]
        )
        
        # --- INITIAL LOAD ---
        # Load existing containers from DB on startup
        with pymysql.connect(**DB) as conn:
            with conn.cursor(pymysql.cursors.DictCursor) as cursor:
                sql = f"SELECT ID, challengeID, userID, port, timeInitialised FROM {TABLE_CONTAINERS}"
                cursor.execute(sql)
                for row in cursor.fetchall():
                    row_id = row['id'] 
                    time_init = row.get('timeInitialised')
                    port = row.get('port')
                   
                    if time_init and port and row_id not in active_containers:
                        time_init_utc = time_init.replace(tzinfo=timezone.utc)
                        delete_time = time_init_utc + timedelta(minutes=TIME_LIMIT_MINUTES)
                        
                        active_containers[row_id] = {
                            "challengeID": row['challengeID'],
                            "userID": row['userID'],
                            "port": port,
                            "delete_time": delete_time
                        }
                        log(f"Loaded existing container: ID {row_id}, Challenge {row['challengeID']}, Port {port}")


        # --- BINLOG LOOP ---
        for event in stream:
            
            # --- DeleteRowsEvent (CLEANUP) ---
            if isinstance(event, DeleteRowsEvent) and event.table == TABLE_CONTAINERS:
                 for row in event.rows:
                    row_data = row['values']
                    row_id = row_data.get('ID') or row_data.get(TABLE_COLS.index('ID')) # Safest way to get the deleted ID
                    if row_id in active_containers:
                        log(f"Detected DB deletion for ID {row_id}. Cleaning up...")
                        # The web app or time tracker might have already triggered remove_container,
                        # but we ensure the cache is cleaned regardless.
                        del active_containers[row_id]
                        
            # --- WriteRowsEvent (INSERT/UPDATE) ---
            if isinstance(event, WriteRowsEvent) and event.table == TABLE_CONTAINERS:
                for row in event.rows:
                    row_data = row['values']
                    
                    # --- Robust Data Extraction (Handles string, int keys, and missing data) ---
                    try:
                        # Prioritize string keys, then try integer keys if column list is loaded
                        def get_val(name):
                            val = row_data.get(name) or row_data.get(name.lower())
                            if val is None and TABLE_COLS:
                                try:
                                    return row_data.get(TABLE_COLS.index(name))
                                except (ValueError, KeyError):
                                    pass
                            return val

                        row_id              = get_val('UNKNOWN_COL0')         
                        challenge_id        = get_val('UNKNOWN_COL3')
                        user_id             = get_val('UNKNOWN_COL2')
                        port                = get_val('port')
                        time_initialised    = get_val('UNKNOWN_COL1')

                    except Exception as e:
                        log(f"BINLOG PARSE ERROR: Skipping row due to parsing issue: {e}")
                        continue
                    print(row_data, row_id, challenge_id, user_id, port, time_initialised)
                    if not (row_id and challenge_id and user_id):
                        log(f"WARNING: Skipping row ID={row_id}, challengeID={challenge_id}, userID={user_id}. Missing critical data.")
                        continue
                    
                    is_tracked = (row_id in active_containers)

                    # CASE 1: New request with port not assigned (INSERT)
                    if not is_tracked and (port is None or port == 0):
                        log(f"Action: ASSIGN PORT for ID {row_id}")
                        assigned_port = get_available_port()
                        
                        if assigned_port:
                            # 1a. Update the database. This will generate a new UPDATE binlog event.
                            update_container_port(row_id, assigned_port)
                            
                            # 1b. Prefill cache to prevent re-assignment and prepare for the UPDATE event.
                            if time_initialised:
                                active_containers[row_id] = {
                                    "challengeID": challenge_id,
                                    "userID": user_id,
                                    "port": assigned_port,
                                    "delete_time": time_initialised.replace(tzinfo=timezone.utc) + timedelta(minutes=TIME_LIMIT_MINUTES)
                                }
                                log(f"Action: PREFILL CACHE for ID {row_id} (Port {assigned_port})")
                            else:
                                log(f"WARNING: timeInitialised is missing for ID {row_id}. Cannot prefill cache yet.")
                        else:
                            log(f"ERROR: No available ports for ID {row_id}.")
                        time_init_utc = time_initialised.replace(tzinfo=timezone.utc)
                        delete_time = time_init_utc + timedelta(minutes=TIME_LIMIT_MINUTES)
                        active_containers[row_id] = {
                            "challengeID": challenge_id,
                            "userID": user_id,
                            "port": port,
                            "delete_time": delete_time
                        }
                        spawn_container(
                            challenge_id=challenge_id,
                            user_id=user_id,
                            port=port
                            )
                        log("spawned")
                        continue

                    # CASE 2: Row has a valid port (UPDATE from Case 1 or direct INSERT)
                    log(f"Processing existing port for ID {row_id}: Port {port}, TimeInitialised {time_initialised}, Tracked: {is_tracked}")
                    if port and time_initialised:
                        log(f"Action: TRACK & SPAWN CHECK for ID {row_id} (Port {port})")
                        time_init_utc = time_initialised.replace(tzinfo=timezone.utc)
                        delete_time = time_init_utc + timedelta(minutes=TIME_LIMIT_MINUTES)
                        
                        # 2a. Update/Add to active_containers cache (Tracking for cleanup)
                        active_containers[row_id] = {
                            "challengeID": challenge_id,
                            "userID": user_id,
                            "port": port,
                            "delete_time": delete_time
                        }

                        # 2b. DOCKER SPAWN: Only spawn if it was NOT tracked before (i.e., this event completed the data)
                        if not is_tracked:
                            log(f"Action: SPAWN DOCKER for ID {row_id} (Port {port})")
                            spawn_container(
                                challenge_id=challenge_id,
                                user_id=user_id,
                                port=port
                            )
            
            # Note: DeleteRowsEvent is handled above, but can also be caught here
            if isinstance(event, DeleteRowsEvent) and event.table == TABLE_CONTAINERS:
                for row in event.rows:
                    # In a DeleteRowsEvent, 'values' contains the *deleted* row data
                    row_id = row['values'].get('ID')
                    if row_id in active_containers:
                        remove_container(active_containers[row_id]['userID'], active_containers[row_id]['challengeID'], row_id)


    except KeyboardInterrupt:
        log("Stopping binlog monitoring.")
    except Exception as e:
        log(f"Fatal binlog loop error: {e}\n{traceback.format_exc()}")
    finally:
        if stream: stream.close()

# =========================
# BOOT
# =========================
if __name__ == "__main__":
    if not DOCKER_CLIENT:
        print("Exiting due to Docker client initialization failure.", flush=True)
        exit(1)
        
    log(f"TIME_LIMIT_MINUTES = {TIME_LIMIT_MINUTES}")
    load_cols()  # detect container table column order for binlog mapping
    # Start the background time tracker for cleanup
    threading.Thread(target=time_tracker, daemon=True).start() 
    # Start the main binlog monitor loop
    process_binlog_event()

# End of container_manager_app/manager.py