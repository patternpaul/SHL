ln -sfn /vagrant/public /usr/share/nginx/html/prd
touch /vagrant/.env
cp /vagrant/.env /vagrant/.envBAK
cp /vagrant/.env.example /vagrant/.env
chown nginx:nginx -R /vagrant
chown nginx:nginx -R /usr/share/nginx/html/prd
setenforce Permissive
service nginx restart
cd /vagrant
composer install
composer dumpautoload -o
php artisan migrate
php artisan consumedata
php artisan refreshredis
php artisan route:cache
php artisan config:cache
php artisan optimize --force
php artisan createuser --email=admin@shl-wpg.ca --password=password