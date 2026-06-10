#!/bin/bash
clear

REAL_USER="kevin.smith"
REAL_PASS="Bubbles"

echo "=== CHEESE VAULT EMPLOYEE TERMINAL v3.7 ==="
echo "Internal Use Only — Unauthorized Access Prohibited"
echo ""

# --- Username prompt ---
read -p "Username: " USER_INPUT

# --- Password prompt (masked) ---
read_password() {
    prompt="$1"
    password=""
    charcount=0

    printf "%s" "$prompt"
    while IFS= read -r -s -n1 char; do
        if [[ $char == "" ]]; then
            printf "\n"
            break
        fi
        if [[ $char == $'\177' ]]; then
            if [ $charcount -gt 0 ]; then
                charcount=$((charcount-1))
                password="${password%?}"
                printf "\b \b"
            fi
        else
            charcount=$((charcount+1))
            password+="$char"
            printf "*"
        fi
    done
    REPLY="$password"
}

read_password "Password: "

# --- Validation ---
if [[ "$USER_INPUT" != "$REAL_USER" || "$REPLY" != "$REAL_PASS" ]]; then
    echo ""
    echo "Login failed: Invalid employee credentials."
    echo "Please locate the correct username and password."
    echo ""
    exit 0
fi

echo ""
echo "Authenticating..."
sleep 1
echo "Login successful."
sleep 1

# Hand off to next script
exec /usr/local/bin/01_workstation.sh
