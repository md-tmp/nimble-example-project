# Nimble Google Scraper

https://nimble-google-scraper-89b08cba0f2c.herokuapp.com/

`main` branch is auto-deployed to Heroku after tests pass.

## Installing Dependencies

Composer and NPM are used to manage dependencies.

```
composer install
npm install
```

## Local Development

Run Local Asset Server (Provides Hot Reloading):

```
npm run dev
```

Run Laravel Built-In Dev Server:

```
php artisan serve
```

## Pre-Commit Hooks

Optional: Enable Pre-Commit Hooks to test & lint code pre-commit locally.

`php ./vendor/bin/grumphp git:pre-commit`

## Automated Tests

PHPUnit is triggered by GitHub Actions on every commit.

PHPUnit can be triggered locally.

`php artisan test`

## Database Configuration

Laravel supports multiple databases out of the box.

Deployed environments use PostgreSQL.

Tests run on GitHub Actions with SQLite.

PostgreSQL is suggested for local development.