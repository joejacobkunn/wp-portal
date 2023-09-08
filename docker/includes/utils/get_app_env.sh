
#!/bin/bash

get_app_env() {
  local env_file="/var/www/html/.env"
  local env_variable="$1"

  # Check if the .env file exists
  if [ ! -f "$env_file" ]; then
    echo "Error: .env file not found at $env_file"
    return 1
  fi

  # Use grep to find the line containing the desired variable and value
  # Then use cut to extract the value (assuming the format is VAR_NAME=value)
  local value=$(grep "$env_variable=" "$env_file" | cut -d '=' -f 2)

  if [ -z "$value" ]; then
    echo "Error: $env_variable not found in $env_file"
    return 1
  fi

  echo "$value"
}