#!/bin/bash
center() {
    local termwidth=$(tput cols)
    local padding=$(( (termwidth - ${#1}) / 2 ))
    printf "%${padding}s%s\n" "" "$1"
}
clear

center "=== US CHEESEVAULT MAINFRAME SECURITY INTERFACE ==="
center "Firmware v4.2.9 — DairyTech Microsystems"
center "© 1983–2026 All Rights Reserved"
echo ""
sleep 2
#!/bin/bash
clear

center "==============================================================="
center "        *** UNITED STATES SECURE INFORMATION SYSTEM ***        "
center "==============================================================="
center "This system is the property of the United States Government."
center "Unauthorized access is strictly prohibited."
center ""
center "All activities on this system are monitored, recorded, and subject"
center "to audit. By accessing this system, you consent to such monitoring."
center ""
center "Violations may constitute offenses under:"
center " - Federal Information Security Compliance Directive 47‑B"
center " - National Cyber Infrastructure Protection Mandate §12"
center " - Interagency Data Integrity Protocol 5.3"
center ""
center "Unauthorized use may result in administrative action, civil penalties,"
center "criminal prosecution, or all of the above."
center ""
center "IF YOU ARE NOT AN AUTHORIZED USER, DISCONNECT IMMEDIATELY."
center "==============================================================="
echo ""
sleep 1
exec /usr/local/bin/fake_login.sh

