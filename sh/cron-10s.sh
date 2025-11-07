#!/bin/bash
for a in 1 2 3 4 5 6
do
php  /var/www/agt/kohana/index.php --uri=cron/jackpotsfinish &
sleep 10
done
exit 0
