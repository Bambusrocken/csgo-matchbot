# CS:GO Match Bot
A CS:GO match management bot. Powered by Laravel. Licensed under the GNU GPL v3.

## Minimum Requirements
##### PHP 5.5 compiled with ZTS (Zend Thread Safety)
##### PHP Installation and Web Server that respect the minimum requirements of the Laravel Framework version 5.1
##### PHP Extension PThreads (http://php.net/manual/en/book.pthreads.php) (Installation Instructions here: http://php.net/manual/en/pthreads.installation.php)

## Installing the BOT
##### Since this BOT is still not complete, this is a rough draft of how the system will work once finished. Things may change without prior notice.
##### As this is a fully automated system with extreme web integration, a web server capable of running Laravel with "Pretty URLs" is required. Apache 2.4 or Nginx work, as long as you copy your .htaccess file correctly in the "public" directory. There's a .htaccess.[web-server-name] file for each server, rename accordingly.
###### Copy the BOT to the web server path you want it to run in;
###### Rename the .env.example file to .env;
###### Edit the .env file with your own information;
###### Open a command line in the folder of the bot and run the command "php artisan migrate"
###### Profit, profit, profit.

## Running the BOT
##### Since this BOT is still not complete, this is a rough draft of how the system will work once finished. Things may change without prior notice.
###### Running the bot should be as simple as opening a command line on the bot's folder and running the command "php artisan bot:run" and starting your web server.

## Laravel PHP Framework

[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, queueing, and caching.

Laravel is accessible, yet powerful, providing powerful tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.

##### Official Documentation

Documentation for the framework can be found on the [Laravel website](http://laravel.com/docs).

##### Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

##### Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

##### License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)