ln -sfn /vagrant/public /usr/share/nginx/html/prd
touch /vagrant/.env
cp /vagrant/.env /vagrant/.envBAK
cp /vagrant/.env.example /vagrant/.env
chown nginx:nginx -R /vagrant
chown nginx:nginx -R /usr/share/nginx/html/prd
setenforce Permissive
supervisorctl stop artisan_job:*
service nginx restart
cd /vagrant
php artisan migrate
php artisan consumedata
php artisan refreshredis