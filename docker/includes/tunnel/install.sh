#!/bin/bash

# Load app env
source /var/www/html/docker/includes/utils/get_app_env.sh

#Set up SRO SSH config file
mkdir -p /root/.ssh
host=$(get_app_env "AUTO_SSH_LOCAL_HOST")
host_name=$(get_app_env "AUTO_SSH_LOCAL_HOSTNAME")
user=$(get_app_env "AUTO_SSH_USERNAME")
identity_path=$(echo $(get_app_env "AUTO_SSH_PRIVATE_KEY") | sed 's/\//\\\//g')
sed "s/%HOST%/$host/g;s/%HOST_NAME%/$host_name/g;s/%USER%/$user/g;s/%FILE_PATH%/$identity_path/g" "/var/www/html/docker/includes/tunnel/etc/config" > "/root/.ssh/config"

#Inititiate tunnel via autossh
remote_host=$(get_app_env "AUTO_SSH_REMOTE_HOST")
remote_port=$(get_app_env "AUTO_SSH_REMOTE_PORT")
sro_db_port=$(get_app_env "SRO_DB_PORT")
autossh -M 0 -o "ExitOnForwardFailure=yes" -o "ServerAliveInterval=30" -o "ServerAliveCountMax=3" -N -f -L $sro_db_port:$remote_host:$remote_port $host
