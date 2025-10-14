import os
import time
from datetime import datetime, timedelta

import docker
import mysql.connector
from mysql.connector import Error

# --- Configuration from Environment Variables ---
DB_HOST = os.getenv("DB_HOST", "CTF-MySQL")
DB_NAME = os.getenv("DB_NAME", "CyberCity")
DB_USER = os.getenv("DB_USER", "devuser")
DB_PASS = os.getenv("DB_PASS", "devpass")
CHALLENGE_IMAGE_BASE = os.getenv("CHALLENGE_IMAGE_BASE", "ctf-challenge")
CLEANUP_DURATION_MINUTES = int(os.getenv("CLEANUP_DURATION_MINUTES", 15))
POLL_INTERVAL_SECONDS = int(os.getenv("POLL_INTERVAL_SECONDS", 10))
CONTAINER_INTERNAL_PORT = int(os.getenv("CONTAINER_INTERNAL_PORT", 80))
# Ensure this matches the network name used in docker-compose.yml
NETWORK_NAME = os.getenv("NETWORK_NAME", "ctf_public")

def get_db_connection():
    """Establishes and returns a database connection."""
    try:
        conn = mysql.connector.connect(
            host=DB_HOST,
            database=DB_NAME,
            user=DB_USER,
            password=DB_PASS
        )
        return conn
    except Error as e:
        print(f"Error connecting to MySQL: {e}")
        return None

def get_new_challenges(conn):
    """
    Retrieves rows from DockerContainers where the container has not yet been launched.
    (We use 'timeInitialised IS NULL' as the launch flag).
    """
    cursor = conn.cursor(dictionary=True)
    # Query: Selects the required fields including the new 'userID'. Launch flag is 'timeInitialised IS NULL'.
    query = "SELECT id, challengeID, port, userID FROM DockerContainers WHERE timeInitialised IS NULL" 
    cursor.execute(query)
    challenges = cursor.fetchall()
    cursor.close()
    return challenges

def update_challenge_status(conn, row_id):
    """Updates the row after successful container launch, recording the initialization time only."""
    cursor = conn.cursor()
    # Updated: Only sets timeInitialised, as dockerIdentifier is no longer stored in the DB
    query = "UPDATE DockerContainers SET timeInitialised = NOW() WHERE id = %s"
    cursor.execute(query, (row_id,))
    conn.commit()
    cursor.close()

def launch_container(docker_client, challenge_id, host_port, row_id, user_id, db_conn):
    """Launches a new Docker container for the given challenge."""
    image_name = f"{CHALLENGE_IMAGE_BASE}-{challenge_id}"
    # Use the database row ID for guaranteed uniqueness in the container name
    container_name = f"ctf-challenge-id-{row_id}-{challenge_id}" 

    print(f"Attempting to launch container {container_name} from image {image_name} on port {host_port} for user {user_id}")

    try:
        # Pull or verify the image is local
        try:
            docker_client.images.get(image_name)
        except docker.errors.ImageNotFound:
            print(f"Image {image_name} not found locally. Attempting to pull...")
            docker_client.images.pull(image_name)

        # Container Configuration
        container = docker_client.containers.run(
            image=image_name,
            detach=True,
            name=container_name,
            # Map the dynamic host_port to the container's internal port
            ports={f'{CONTAINER_INTERNAL_PORT}/tcp': host_port},
            network=NETWORK_NAME,
            # Use labels to link the running container back to the database record and user
            labels={
                "ctf.challenge.id": str(row_id),
                "ctf.user.id": str(user_id)
            },
            environment={
                "CHALLENGE_PORT": CONTAINER_INTERNAL_PORT,
                "USER_ID": str(user_id)
            }
        )

        print(f"Successfully launched container {container.name} (ID: {container.short_id})")

        # Update database with initialization time only
        update_challenge_status(db_conn, row_id)
        
    except docker.errors.APIError as e:
        print(f"Docker API error launching container {container_name}: {e}")
    except Exception as e:
        print(f"An unexpected error occurred during container launch: {e}")

def cleanup_containers(docker_client, db_conn):
    """
    Stops and removes containers that have exceeded the CLEANUP_DURATION_MINUTES
    by checking the DB timestamp linked via container labels.
    """
    
    # 1. Identify all running challenge containers using a label filter
    try:
        # Filter containers by the label we apply during launch
        running_challenges = docker_client.containers.list(filters={"label": "ctf.challenge.id"})
        print (running_challenges)
    except docker.errors.APIError as e:
        print(f"Error listing running containers for cleanup: {e}")
        return

    cleanup_count = 0
    
    for container in running_challenges:
        # 2. Extract the DB row ID from the container label
        row_id_str = container.labels.get("ctf.challenge.id")
        
        # This check should theoretically not fail if the filter worked, but serves as a safeguard
        if not row_id_str:
            print(f"Warning: Container {container.name} lacks ctf.challenge.id label. Skipping cleanup check.")
            continue
            
        try:
            row_id = int(row_id_str)
        except ValueError:
            print(f"Warning: Invalid row ID in label for container {container.name}. Skipping.")
            continue

        # 3. Query DB for timeInitialised based on row ID
        cursor = db_conn.cursor(dictionary=True)
        query = "SELECT timeInitialised FROM DockerContainers WHERE id = %s"
        cursor.execute(query, (row_id,))
        record = cursor.fetchone()
        cursor.close()

        if not record or not record.get('timeInitialised'):
            # DB record is missing or timestamp is null (e.g., failed launch or DB corruption)
            print(f"DB record (ID: {row_id}) not found or missing timestamp. Force removing container {container.name} to clean up orphaned resource.")
            try:
                container.stop(timeout=5)
                container.remove(v=True, force=True) 
                if record:
                    # If record exists but is NULL, also delete it
                    cursor = db_conn.cursor()
                    delete_query = "DELETE FROM DockerContainers WHERE id = %s"
                    cursor.execute(delete_query, (row_id,))
                    db_conn.commit()
                    cursor.close()
                cleanup_count += 1
            except docker.errors.APIError as e:
                 print(f"Error force removing orphaned container {container.name}: {e}")
            continue

        # Convert datetime object to a timezone-naive datetime for subtraction
        time_initialised = record['timeInitialised']
        if time_initialised.tzinfo is not None:
             time_initialised = time_initialised.replace(tzinfo=None)
        
        # 4. Check time elapsed
        time_elapsed = datetime.now() - time_initialised
        cleanup_threshold = timedelta(minutes=CLEANUP_DURATION_MINUTES)
        
        if time_elapsed > cleanup_threshold:
            print(f"Container {container.name} (DB ID: {row_id}) expired after {time_elapsed}. Initiating removal.")
            
            try:
                # 5. Delete container and DB record
                container.stop(timeout=5)
                container.remove(v=True, force=True) 
                
                cursor = db_conn.cursor()
                delete_query = "DELETE FROM DockerContainers WHERE id = %s"
                cursor.execute(delete_query, (row_id,))
                db_conn.commit()
                cursor.close()
                
                cleanup_count += 1
            
            except docker.errors.APIError as e:
                print(f"Docker API error during cleanup of {container.name}: {e}")
            except Exception as e:
                print(f"An unexpected error occurred during cleanup: {e}")

    if cleanup_count > 0:
        print(f"Cleaned up {cleanup_count} old challenge instances.")
    else:
        print("No containers requiring cleanup found.")

def main_loop():
    """The main loop for the container manager service."""
    try:
        # Uses the Docker socket to connect to the daemon
        docker_client = docker.from_env()
        print("Docker client initialized successfully.")
    except Exception as e:
        print(f"Could not initialize Docker client: {e}. Exiting.")
        return

    while True:
        db_conn = get_db_connection()
        if db_conn:
            try:
                print("--- Starting Challenge Provisioning Cycle ---")
                
                # Task 1: Launch new containers
                new_challenges = get_new_challenges(db_conn)
                print(f"Found {len(new_challenges)} new challenges to provision.")
                for challenge in new_challenges:
                    # Pass the new 'userID' field to launch_container
                    launch_container(
                        docker_client, 
                        challenge['challengeID'], 
                        challenge['port'], 
                        challenge['id'],
                        challenge['userID'], 
                        db_conn
                    )

                # Task 2: Clean up old containers
                print("--- Starting Cleanup Cycle ---")
                cleanup_containers(docker_client, db_conn)
                
            except Error as e:
                print(f"Database operation error during main loop: {e}")
            finally:
                db_conn.close()
        else:
            print("Failed to connect to database. Retrying...")

        time.sleep(POLL_INTERVAL_SECONDS)

if __name__ == "__main__":
    print("CTF Dynamic Container Manager starting...")
    main_loop()
