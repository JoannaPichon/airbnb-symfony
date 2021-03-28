# airbnb-symfony
## (school project, in public for classroom)

### How to use this project
```console
// clone the repository in the current directory
$ git clone https://github.com/JoannaPichon/airbnb-symfony.git

// install all dependencies with composer
$ composer install

// create database from the .env (user: root, no password and dbname: "first-project-symfony")
$ php bin/console doctrine:database:create

// create the file for migration
$ php bin/console make:migration

// execute the migration
$ php bin/console doctrine:migrations:migrate

// insert fake datas from fixture without confirmation request
$ php bin/console doctrine:fixtures:load -q
```

/!\ Warning:<br>
There is some differences with the teacher's file;<br>
login -> pseudo<br>
hash -> password<br>
and other somes minor differences like variables name, functions..., pay attention
