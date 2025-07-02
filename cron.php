#!/usr/bin/env bash
# Adds a cron entry to run cron.php every hour.
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
CRON_CMD="0 * * * * php $SCRIPT_DIR/cron.php >/dev/null 2>&1"
( crontab -l 2>/dev/null | grep -v "cron.php" ; echo "$CRON_CMD" ) | crontab -

echo "Cron job installed: $CRON_CMD"
