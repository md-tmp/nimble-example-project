web: heroku-php-apache2 public/
worker: trap '' SIGTERM; chromedriver & php artisan queue:work & wait -n; kill -SIGTERM -$$; wait