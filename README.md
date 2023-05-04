# My Cinema

`My Cinema` is an REST API created with PHP and Symfony for managing a cinema.

### Requirements

- [PHP 8.1.0 or higher](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [Symfony CLI](https://symfony.com/download)

### Set up project locally

Download project: `$ git clone https://github.com/Heaven31415/my-cinema.git`

Change to its directory: `$ cd my-cinema`

Install dependencies: `$ composer install`

Create local .env files:
- `$ cp .env .env.local`
- `$ cp .env.test .env.test.local`

Configure the value of `DATABASE_URL` environment variable in `.env.local` and `.env.test.local` 
files (its value should be identical in both files)

Set up main database with:
- `$ php bin/console doctrine:database:create`
- `$ php bin/console doctrine:migrations:migrate --no-interaction`
- `$ php bin/console doctrine:fixtures:load --no-interaction --group=dev`

Set up test database with:
- `$ php bin/console --env=test doctrine:database:create`
- `$ php bin/console --env=test doctrine:migrations:migrate --no-interaction`
- `$ php bin/console --env=test doctrine:fixtures:load --no-interaction --group=test`

### Run local development server

`$ symfony serve`

### Run tests locally

`$ php bin/phpunit`

### View documentation locally

Visit https://localhost:8000/api/doc to see the documentation