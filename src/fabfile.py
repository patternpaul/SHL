from fabric.api import *
import os
import sys
from fabric.contrib.project import rsync_project

env.forward_agent = True


def buildAndTest():
    local('composer install')
    local('./vendor/bin/phpunit -d memory_limit=200M')
    local('php artisan twig:clean')
    local('php artisan route:cache')
    local('php artisan config:clear')

def deploy():
    buildAndTest()


    dirs = ["/usr/share/nginx/html/blue/public", "/usr/share/nginx/html/green/public"]

    sudo('supervisorctl stop artisan_job:*')


    command = run('readlink /usr/share/nginx/html/prd')
    prd_dir = command.stdout
    print 'THE PRD DIR IS %s' % prd_dir

    dirs.remove(prd_dir)
    stg_dir = dirs[0]

    print 'THE STG DIR IS %s' % stg_dir


    prd_work_dir = os.path.dirname(prd_dir)
    stg_work_dir = os.path.dirname(stg_dir)


    print 'THE PRD WORK DIR IS %s' % prd_work_dir
    print 'THE STG WORK DIR IS %s' % stg_work_dir


    print 'CLEANING OUT %s' %  stg_work_dir
    sudo('rm -fr %s' % stg_work_dir)


    sudo('mkdir %s' % stg_work_dir)
    sudo('chown -R %s:%s %s' % (env.user, env.user, stg_work_dir))

    rsync_project(
                    remote_dir=stg_work_dir,
                    local_dir="./",
                    exclude=(".gitignore", ".env", "node_modules", "vendor", ".git"),
                    ssh_opts="-o StrictHostKeyChecking=no"
                    )


    run('cp ~/.env %s/.env' % stg_work_dir)

    with cd('%s' % stg_work_dir):
        run('composer install --no-dev')
        run('composer dumpautoload -o')

        run('php artisan route:cache')
        run('php artisan config:cache')
        run('php artisan optimize --force')



    sudo('chown -R nginx:nginx %s' % stg_work_dir)

    sudo('chown -R nginx:nginx %s/storage' % stg_work_dir)
    #sudo('chown -R nginx:nginx %s/storage/logs' % stg_work_dir)
    #sudo('chown nginx:nginx %s/storage/logs/laravel.log' % stg_work_dir)


    sudo('ln -sfn %s %s' % (stg_work_dir, '/usr/share/prd-app'))
    sudo('ln -sfn %s %s' % (stg_dir, '/usr/share/nginx/html/prd'))
    sudo('ln -sfn %s %s' % (prd_dir, '/usr/share/nginx/html/stg'))


    sudo('service nginx restart')
    sudo('service php-fpm restart')

    sudo('supervisorctl start artisan_job:*')
    with cd('%s' % stg_work_dir):
        sudo('php artisan queue:restart')
        sudo('php artisan deploycachebust')
