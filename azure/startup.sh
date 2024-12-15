cp /home/site/wwwroot/azure/nginx/default /etc/nginx/sites-available/default

pkill -o -USR2 php-fpm

apt-get install -y librabbitmq-dev
printf "\n" | pecl install amqp
echo "extension=amqp.so" | tee /usr/local/etc/php/conf.d/amqp.ini

service nginx reload

cd /home/site/wwwroot

php bin/console cache:clear
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction

php bin/console messenger:setup-transports