#!/bin/bash
php  /var/www/agt/kohana/index.php --uri=cron/moonloosebets &
sleep 30
php  /var/www/agt/kohana/index.php --uri=cron/moonloosebets &
exit 0
