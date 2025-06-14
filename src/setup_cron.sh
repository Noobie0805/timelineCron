#!/bin/bash
# This script should set up a CRON job to run cron.php every 5 minutes.
# You need to implement the CRON setup logic here.


here="$(cd "$(dirname "$0")" && pwd)"
php_bin="$(command -v php)"

cron_line="*/5 * * * * $php_bin $here/cron.php"

( crontab -l 2>/dev/null | grep -vF "cron.php" ; echo "$cron_line" ) | crontab -
echo "CRON job added: $cron_line"
