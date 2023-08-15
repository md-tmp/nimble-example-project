web: heroku-php-apache2 public/
worker: trap '' SIGTERM; chromedriver & (wait 10 && php artisan queue:work) & wait -n; kill -SIGTERM -$$; wait