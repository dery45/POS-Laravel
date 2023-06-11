<p align="center">
    <h1 align="center">POS System Using Laravel</h1>
</p>


## Installation

### Requirements

For system requirements you [Check Laravel Requirement](https://laravel.com/docs/9.x/deployment#server-requirements)

### Clone the repository from github.

    git clone https://github.com/angkosal/laravel-pos.git [YourDirectoryName]

The command installs the project in a directory named `YourDirectoryName`. You can choose a different
directory name if you want.

### Install dependencies

Laravel utilizes [Composer](https://getcomposer.org/) to manage its dependencies. So, before using Laravel, make sure you have Composer installed on your machine.

    cd YourDirectoryName
    composer install

### Install printer driver

    composer require mike42/escpos-php
    setup XAMPP extension:intl


### Config file

Rename or copy `.env.example` file to `.env` 1.`php artisan key:generate` to generate app key.

1. Set your database credentials in your `.env` file
1. Set your `APP_URL` in your `.env` file.

### Database

1. Migrate database table `php artisan migrate`
1. `php artisan db:seed`, this will initialize settings and create and admin user for you [email: admin@gmail.com  - password: admin123]

### Install Node Dependencies

1. `npm install` to install node dependencies
1. `npm run dev` for development or `npm run build` for production

### Create storage link

`php artisan storage:link`
`php artisan route:clear`
`php artisan route:cache`

### Run Server

1. `php artisan serve` or Laravel Homestead
2. Visit `localhost:8000` in your browser. Email: `admin@gmail.com`, Password: `admin123`.

### Setup backend API

1. install python latest
2. `py -m pip install --user virtualenv` installing venv
3. move to directory POS-Laravel/Backend
4. `py -m venv venv` Creating a virtual environment
5. `venv\Scripts\activate` activate the virtual environtment
6. `pip install -r requirements.txt` install all packages
7. setup app_runner.py


### Using App Runner

1. dir1 = XAMPP Folder PATH
2. dir2 = POS-Laravel PATH
3. dir4 = POS-Laravel/Backend/venv/Scripts PATH
4. run the program / create shortcut

### Screenshots

#### Product list

![Product list](https://raw.githubusercontent.com/angkosal/laravel-pos/master/screenshots/products_list.png)

#### Create order

![Create order](https://raw.githubusercontent.com/angkosal/laravel-pos/master/screenshots/pos.png)

#### Order list

![Order list](https://raw.githubusercontent.com/angkosal/laravel-pos/master/screenshots/order_list.png)

#### Customer list

![Customer list](https://raw.githubusercontent.com/angkosal/laravel-pos/master/screenshots/customer_list.png)


