# W&P Connect

## Notes
docker cp ~/PROGRESS_OE_11.7_LNX_64_SQLCLIENTACCESS.tar.gz app:/tmp

https://blog.zedfox.us/installing-openedge-sql-client-access-odbc-drivers-ubuntu/

ssh tunnel : https://tutonics.com/2012/06/using-publicprivate-key-pair-for-auto.html

## Installation

1. Clone project directory
    ```python
    #clone the repo in your work directory
    git clone git@github.com:joejacobkunn/wp-portal.git

    #open project directory
    cd wp-portal
    
    #checkout to dev branch
    git checkout dev
    ```

2. Create .env file by duplicating .env.example
    ```python
    #Set.env values for database & email connectivity

    #Database
    DB_CONNECTION=mysql
    DB_HOST=wp_db 
    DB_PORT=3306
    DB_DATABASE=wp_portal
    DB_USERNAME=root
    DB_PASSWORD=root_password

    #mail
    MAIL_MAILER=smtp
    MAIL_HOST=wp_mail
    MAIL_PORT=1025
    MAIL_USERNAME=null
    MAIL_PASSWORD=null
    MAIL_ENCRYPTION=null
    MAIL_FROM_ADDRESS="hello@wandpmanagement.com"
    MAIL_FROM_NAME="${APP_NAME}"
    ```

3. Setup docker containers
    ```python
    #build docker containers
    docker compose up -d --build

    ```

4. Execute the script to finish installation
    ```python
    docker exec -it wp_app /usr/scripts/install.sh

    ```

5. Uninstalling docker and cleaning up files
    ```python
    sudo hwclock -s
    docker stop $(docker ps -a -q)
    docker rm $(docker ps -a -q)
    docker rmi $ (docker images -a -q)
    docker volume rm $(docker volume ls -q)
    docker network rm `docker network ls -q`
    ```


