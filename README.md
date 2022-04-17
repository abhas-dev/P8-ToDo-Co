# ToDo & Co

TodoList is an MVP application developed with symfony 3.1 and PHP 5.5.9. The startup
whose core business is an application to manage its daily tasks.
The company has succeeded in raising funds and now wants to improve the quality of the application
and reduce its technical debt.

## Objectives:

- Write unit and functional tests :white_check_mark:
- Reduce the technical debt of the application (PHP 8.1, Symfony 5.4) :white_check_mark:
- Fix bugs :white_check_mark:
- Add features :white_check_mark:
- Perform a quality and performance audit with Blackfire :white_check_mark:

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/2d957a63a27f4d9c8787d85e77877e1f)](https://www.codacy.com/gh/ahassaine-dev/P8-ToDo-Co/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ahassaine-dev/P8-ToDo-Co&amp;utm_campaign=Badge_Grade)

## Getting Started

### Requirements

- Docker
- Docker-compose

### Installing the project

Clone the project

```bash
git clone git@github.com:ahassaine-dev/P8-ToDo-Co.git
```

Run the docker-compose

```bash
docker-compose up -d --build
```

Log into the PHP container

```bash
docker exec -it www_docker_sf bash
```

### Install packages
```
composer install
```

Configure database connexion(no password required)
```yaml
DATABASE_URL="mysql://root:@db_docker_sf:3306/todo?serverVersion=5.7"
```

Create database and load fixtures
```bash
composer prepare
```

*Application is available at http://127.0.0.1:8080 \
*Database port mapped to: 3306 

### Ready to use with

This docker-compose provides you :

- PHP:8.1-apache-bullseye
  - Apache
  - Composer
  - Symfony CLI
  - and some other php extentions
- mysql

### Run tests
Coverage is generated in web/coverage folder.
```bash
composer test
```
