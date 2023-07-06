#!/bin/bash

#Remvoe existing path
rm -rf /usr/wrk /usr/wrk_oemgmt/ /usr/oemgmt /usr/dlc/
expect_script="/var/www/html/docker/drivers/openedge/execute.sh"
expect "$expect_script"
