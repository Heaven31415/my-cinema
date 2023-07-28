# My Cinema

![tests](https://github.com/Heaven31415/my-cinema/actions/workflows/tests.yml/badge.svg)

`My Cinema` is an REST API for managing a cinema created with PHP 8, Symfony 6 and FOSRestBundle 3

## JetBrains support

This project is supported by JetBrains via its [OpenSourceSupport](https://jb.gg/OpenSourceSupport) initiative

## Demo

Live demo is available [here](https://heaven31415-my-cinema.herokuapp.com/api/v1)

OpenAPI documentation can be found [here](https://heaven31415-my-cinema.herokuapp.com/api/doc)

## Project overview

- CRUDs for Genres, Halls, Movies and Shows: 4 services, 4 entities and 4 controllers
- 16 endpoints with documentation in OpenAPI format via [NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle)
- 67 unit and integration tests (166 assertions)
- Authentication via [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle) and authorization support **(WIP)**
- Sensible data fixtures implemented with [ZenstruckFoundryBundle](https://github.com/zenstruck/foundry)
- Live version API and documentation, deployed on Heroku
- GitHub Actions CI support
- Local docker development support **(WIP)**

## Local setup and development

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

## Heroku deployment

- Add a new commit to the master branch
- `git push heroku master`
- `heroku run php bin/console doctrine:migrations:migrate --no-interaction`
