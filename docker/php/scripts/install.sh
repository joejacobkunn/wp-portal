#!/bin/bash

echo "Initializing W&P_Connect"

echo "Set directory permissions"
chown -R www:www-data storage/
chown -R www:www-data bootstrap/
chmod -R 775 storage/
chmod -R 755 bootstrap/

echo "Installing dependencies"
composer install

echo "Running migrations"
php artisan migrate
php artisan key:generate


echo "Set symlinks"
php artisan storage:link

echo "Setup OpenEdge driver"
/bin/bash /var/www/html/docker/includes/drivers/openedge/install.sh

echo "Running Permission seeder"
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=ModuleSeeder
php artisan db:seed --class=SXAccountSeeder


echo "Setup SSH Tunnel"
/bin/bash /var/www/html/docker/includes/tunnel/install.sh

echo "Completed W&P_Connect setup"

