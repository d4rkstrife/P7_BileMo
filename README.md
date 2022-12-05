# P7_BileMo
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/82aed2e2460d491888f95b91ae002cd0)](https://www.codacy.com/gh/d4rkstrife/P7_Bilemo/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=d4rkstrife/P7_Bilemo&amp;utm_campaign=Badge_Grade)
Create a web service exposing an API

Project installation Prerequisite :

Php : 8.1.*
Symfony : 6.0.*
Composer
GIT installation :

GIT (https://git-scm.com/downloads) When GIT is installed, go in the folder of your choice and then execute this command:
- git clone https://github.com/d4rkstrife/P7_Bilemo.git
  The project will be automatically copied in the folder.

In .env file, modifiate

- DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7
  with your own database informations.
- JWT_PASSPHRASE= with your own secret pass phrase.


Then you have to install all dependencies the app needed to run: 

- composer install
- symfony console lexik:jwt:generate-keypair

  To create the database and tables, run:

- symfony console doctrine:database:create
- symfony console doctrine:migrations:migrate
  You can generate fake datas with fixture, run:

- symfony console doctrine:fixtures:load
  Then to launch the application, run

- symfony server:start --port=3000

Some fake resellers are created by the fixtures. You can use them or create a new user. The password is "Password1!"

go to localhost:3000/api/doc to see the full documentation of the app.

