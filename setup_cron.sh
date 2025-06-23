#!/bin/bash

# Setup CRON job for Task Scheduler
# This script sets up a CRON job to run cron.php every hour

# Get the absolute path to the current directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CRON_PHP_PATH="$SCRIPT_DIR/cron.php"

# Check if cron.php exists
if [ ! -f "$CRON_PHP_PATH" ]; then
    echo "Error: cron.php not found at $CRON_PHP_PATH"
    exit 1
fi

# Create the cron job entry
CRON_JOB="0 * * * * /usr/bin/php $CRON_PHP_PATH"

# Check if the cron job already exists
if crontab -l 2>/dev/null | grep -q "$CRON_PHP_PATH"; then
    echo "CRON job for Task Scheduler already exists."
    echo "Current crontab:"
    crontab -l | grep "$CRON_PHP_PATH"
else
    # Add the cron job
    (crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
    
    if [ $? -eq 0 ]; then
        echo "CRON job successfully added!"
        echo "The task reminder system will now run every hour."
        echo "Cron job: $CRON_JOB"
    else
        echo "Error: Failed to add CRON job."
        exit 1
    fi
fi

# Display current crontab
echo ""
echo "Current crontab entries:"
crontab -l

echo ""
echo "Setup complete! The system will send task reminders every hour to all subscribers."
echo "To remove this cron job later, run: crontab -e and delete the line containing '$CRON_PHP_PATH'"