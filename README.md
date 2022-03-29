# Proj'ISEN

Proj'ISEN is a web application to manage project's attribution.

C++ 17 / Symfony 5.4 / PHP 8.1

## Setup
- Install [Symfony](https://symfony.com/doc/5.4/setup.html) 5.4
- Go to /projisen and run :
```bash
composer install
```
- Generate the database with the following commands :
```bash
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```
- Compile C++ files with g++
- To start creating your user's accounts, enter in the database (staff table) :
```
id | username | roles | password | first_name | last_name | is_admin
1 | admin | ["ROLE_ADMIN"] | $2y$13$9FjZxA4DePSp7CsSInJ4neQekOOEYUONc.o4kyd2MylbBdD.mHQEq | Super | Admin | 1
```
- Then you can log in with the id : admin and pwd : admin

## Run the application

- Go to /projisen and run :
```bash
symfony server:start
```
*Note : You can add -d to run the command as a background task.*