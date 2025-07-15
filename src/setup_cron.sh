#!/bin/bash

# Automatically sets up a CRON job to run cron.php every hour

CRON_JOB="0 * * * * php $(pwd)/cron.php >/dev/null 2>&1"

# First, remove old cron jobs for cron.php to avoid duplicates
crontab -l | grep -v 'cron.php' > mycron || true
echo "$CRON_JOB" >> mycron
crontab mycron
rm mycron

echo "CRON job set up to run cron.php every hour."
