#!/bin/bash

#Remove existing path
rm -rf /usr/wrk /usr/wrk_oemgmt/ /usr/oemgmt /usr/dlc/
expect_script="/var/www/html/docker/drivers/openedge/execute.sh"
expect "$expect_script"

#Setup OpenEdge ODBC driver
cd /usr/dlc/odbc/lib
if ldd pgoe27.so | grep "27.so" | grep -q "not found"; then
    cp libpgicu27.so /usr/lib
fi

#Set up ODBCINST
cp /var/www/html/docker/drivers/openedge/src/etc/odbcinst.ini /etc/odbcinst.ini

#Set up SRO SSH config file
cp /var/www/html/docker/drivers/openedge/src/etc/config ~/.ssh/config

#Inititiate tunnel via autossh
autossh -M 0 -o "ServerAliveInterval 30" -o "ServerAliveCountMax 3" -N -f -L 3308:weingartz-dev-02.cx1bpgdldz9l.us-east-1.rds.amazonaws.com:3306 sro-staging
