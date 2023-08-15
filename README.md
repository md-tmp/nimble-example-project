# Nimble Google Scraper

https://nimble-google-scraper-89b08cba0f2c.herokuapp.com/

`main` branch is auto-deployed to Heroku after tests pass.

## Installing Dependencies

Composer and NPM are used to manage dependencies.

```
composer install
npm install
```

[Download ChromeDriver](https://googlechromelabs.github.io/chrome-for-testing/)

ChromeDriver is used for scraping Google with a real web browser.

## Local Development

Run Local Asset Server (Provides Hot Reloading):

```
npm run dev
```

Run Laravel Built-In Dev Server:

```
php artisan serve
```

Run ChromeDriver on port 9515 (default).
```
./chromedriver
```

Run the Laravel background worker:
```
php artisan queue:work
```

## Testing Background Worker

Keywords can be individually queued for scraping using the following command.
```
php artisan app:keyword-command {keyword}
```

When using this command Keywords are assigned to the first user in the database.

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

The Queue is currently database powered. To maximize performance Redis can be used instead (configured in Laravel .env).

## Google CAPTCHAs

Occasionally Google may present CAPTCHAs.

An imperfect solution is implemented by relying on using a real browser, human-like behavior, and rate-limiting requests with random pauses.

Currently, when we receive a CAPTCHA we pause for a random time between 30 minutes and 1 hour before trying again.

Implementing a proxy server is recommended for production use.

With Heroku, the worker dyno can be restarted via the CLI to get a new IP address and instantly unblock the queue.

## API

Authenticate by passing in your API Key as a Bearer token in the Authorization header.

### Keywords List Endpoint
`GET /api/v1/keywords`

cURL Example:
```
curl --location 'https://nimble-google-scraper-89b08cba0f2c.herokuapp.com/api/v1/keywords' \
--header 'Authorization: Bearer {API_KEY_HERE}'
```

### Keyword Details (Reports) Endpoint
`GET /api/v1/keywords/{id}`

Specify a Keyword ID in the URL Path.

cURL Example:
```
curl --location 'https://nimble-google-scraper-89b08cba0f2c.herokuapp.com/api/v1/keywords/{ID_HERE}' \
--header 'Authorization: Bearer {API_KEY_HERE}'
```

### Import Keywords Endpoint
`POST /api/v1/keywords`

POST a CSV file as import_file.

cURL Example:
```
curl --location 'https://nimble-google-scraper-89b08cba0f2c.herokuapp.com/api/v1/keywords' \
--header 'Authorization: Bearer {API_KEY_HERE}' \
--form 'import_file=@"/path/to/local/file.csv"'
```
