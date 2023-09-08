#!/bin/bash

#Remove existing path
rm -rf /usr/wrk /usr/wrk_oemgmt/ /usr/oemgmt /usr/dlc/
expect_script="/var/www/html/docker/includes/drivers/openedge/execute.sh"
expect "$expect_script"

#Setup OpenEdge ODBC driver
cd /usr/dlc/odbc/lib
if ldd pgoe27.so | grep "27.so" | grep -q "not found"; then
    cp libpgicu27.so /usr/lib
fi

#Set up ODBCINST
cp /var/www/html/docker/includes/drivers/openedge/src/etc/odbcinst.ini /etc/odbcinst.ini
