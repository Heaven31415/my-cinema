# My Cinema

![tests](https://github.com/Heaven31415/my-cinema/actions/workflows/tests.yml/badge.svg)

`My Cinema` is an REST API for managing a cinema created with PHP and Symfony

## Demo

Live demo is available [here](https://heaven31415-my-cinema.herokuapp.com/api/v1)

OpenAPI documentation can be found [here](https://heaven31415-my-cinema.herokuapp.com/api/doc)

## Project overview

- 3 services
- 4 entities
- 4 controllers
- 16 endpoints with docs in OpenAPI format
- 70 unit and integration tests (128 assertions)
- CI support via Github Actions
- Live version deployed on Heroku

## Local setup and development

---

### Requirements

- [PHP 8.1.20 or 8.2.7 (CI is run against both versions)](https://www.php.net/)
- [Composer 2.5.5](https://getcomposer.org/)
- [Symfony CLI 5.5.6](https://symfony.com/download)
- [PostgreSQL 15.0](https://www.postgresql.org/)

### Setup instructions

1. Download project: `$ git clone https://github.com/Heaven31415/my-cinema.git`

2. Change to its directory: `$ cd my-cinema`

3. Create local `.env` files:
    - `$ cp .env .env.local`
    - `$ cp .env.test .env.test.local`

4. Configure the value of `DATABASE_URL` environment variable in `.env.local` and `.env.test.local`
files (its value should be identical in both files) so you can connect to your PostgreSQL server

5. Run `$ bin/setup` bash script to install project dependencies and setup main and test databases

### Run development server

`$ symfony serve`

### Run tests

`$ bin/phpunit`

### View API docs

Start development server, open your browser and visit [this link](https://localhost:8000/api/doc) to see the docs

---

## Heroku deployment

- Add a new commit to the master branch
- `git push heroku master`
- `heroku run php bin/console doctrine:migrations:migrate --no-interaction`
