#!/usr/bin/env bash
#
# Automated setup for the PHP Task‑Planner demo
# -----------------------------------------------------------
# * Creates required data files      (tasks.txt etc.)
# * Runs setup_cron.sh               (hourly reminders)
# * Starts Mailpit in a NEW terminal (port 1025 / 8025)
# * Optionally starts PHP dev server (localhost:8000)
# -----------------------------------------------------------

set -e

echo -e "\n[✔] Starting project setup..."

# ---------- 1. Ensure JSON text files ----------
declare -a DATA_FILES=("tasks.txt" "subscribers.txt" "pending_subscriptions.txt")
for f in "${DATA_FILES[@]}"; do
    if [[ ! -f "$f" ]]; then
        echo "  • Creating $f"
        [[ "$f" == "tasks.txt" ]] && echo "[]"  > "$f"
        [[ "$f" == "subscribers.txt" ]] && echo "[]"  > "$f"
        [[ "$f" == "pending_subscriptions.txt" ]] && echo "{}" > "$f"
    fi
done

# ---------- 2. temp_codes directory ----------
if [[ ! -d "temp_codes" ]]; then
    echo "  • Creating temp_codes/"
    mkdir -p temp_codes
fi

# ---------- 3. Run cron installer ----------
if [[ -x "./setup_cron.sh" ]]; then
    echo -e "\n[✔] Running setup_cron.sh..."
    ./setup_cron.sh
else
    echo "[✘] setup_cron.sh not found or not executable"
fi

# ---------- 4. Start Mailpit (SMTP 1025 / UI 8025) ----------
echo -e "\n[✔] Launching Mailpit …"
if ! command -v mailpit &>/dev/null; then
    echo "[✘] Mailpit executable not found!  Install from https://github.com/axllent/mailpit"
else
    # Attempt to open in a new terminal tab/window for GNOME / generic X‑term
    if command -v gnome-terminal &>/dev/null; then
        gnome-terminal -- bash -c "echo '[Mailpit] Press Ctrl‑C to stop'; mailpit; exec bash" &
        echo "  → Mailpit started in a new GNOME Terminal window."
    elif command -v x-terminal-emulator &>/dev/null; then
        x-terminal-emulator -e bash -c "echo '[Mailpit] Press Ctrl‑C to stop'; mailpit; exec bash" &
        echo "  → Mailpit started in a new terminal window."
    else
        # Fallback: run in background & log output
        nohup mailpit > mailpit.log 2>&1 &
        echo "  → Mailpit running in background (see mailpit.log)."
    fi
    echo "  • SMTP  : localhost:1025"
    echo "  • Web UI: http://localhost:8025"
fi

# ---------- 5. Test mail() ----------
echo -e "\n[✔] Testing mail() function …"
if [[ -f "testmail.php" ]]; then
    php testmail.php || true   # don't stop script if it fails
else
    echo "  (testmail.php not found — skipping mail test)"
fi

# ---------- 6. Optionally launch PHP server ----------
read -r -p $'\nStart local PHP server on http://localhost:8000 ? (y/n): ' start_srv
if [[ "$start_srv" =~ ^[Yy]$ ]]; then
    echo -e "\n[✔] Starting PHP server (Ctrl‑C to stop)…"
    php -S localhost:8000
else
    echo -e "[ℹ] You can start it later with:  php -S localhost:8000"
fi

echo -e "\n[✔] Setup completed.\n"
