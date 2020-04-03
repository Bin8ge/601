#!/bin/bash
base_path='/var/log/nginx'
log_path=$(date -d yesterday +"%Y%m")
day=$(date -d yesterday +"%d")
#mkdir -p $base_path/$log_path
#mv $base_path/access.log $base_path/$log_path/access_$day.log
echo $base_path/$log_path/access_$day.log
#kill -USR1 `cat /var/run/nginx.pid`
