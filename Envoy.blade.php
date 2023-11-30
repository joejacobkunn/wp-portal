@servers(['staging' => 'administrator@192.168.1.167', 'production' => 'administrator@192.168.1.168'])

//setup paths and git

@setup
    $repository = 'git@github.com:joejacobkunn/wp-portal.git';
    $release = date('YmdHis');
    $environment = isset($env) ? $env : "staging";
    $app_dir = ($environment == 'production') ? '/var/www/production/wp-portal' : '/var/www/staging/wp-portal';
    $confirm = false;
@endsetup

//define deploy script here in the story

@story('deploy', ['on' => $environment, 'confirm' => $confirm])
    git
    composer
    artisan
    queue
    permissions
    live
@endstory

//define each part of the story below

//task : GIT (pull from git branch)

@task('git')
    cd {{ $app_dir }}

    @if ($branch)
        echo 'Pulling branch '.$branch;
        git pull origin {{ $branch }}
    @elseif($env == 'production')
        echo 'Pulling branch main';
        git pull origin main
    @else
        echo 'Pulling branch dev';
        git pull origin dev
    @endif
@endtask

//task : COMPOSER (run composer tasks)

@task('composer')
    echo 'Running composer...';
    cd {{ $app_dir }}
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
    composer dump-autoload
    sudo chown -R www-data:www-data storage
@endtask

//task : artisan (run laravel tasks)

@task('artisan')
    echo 'Running artisan commands...';
    cd {{ $app_dir }}
    php artisan down

    php artisan cache:clear
    php artisan route:cache
    php artisan view:cache
    php artisan config:cache

    php artisan migrate --force

    php artisan db:seed --class=RolePermissionSeeder --force
    php artisan db:seed --class=ModuleSeeder --force

    @if ($seed)
        php artisan db:seed --class={{ $seed }} --force
    @endif
@endtask

//task : queue

@task('queue')
    echo 'Restarting queues';
    cd {{ $app_dir }}
    php artisan queue:restart --no-interaction
@endtask

@task('permissions')
    echo 'Setting permissions';
    sudo chmod -R 755 {{ $app_dir }}/storage
    sudo chmod -R 755 {{ $app_dir }}/bootstrap/cache
@endtask

@task('live')
    cd {{ $app_dir }}
    php artisan up
@endtask
