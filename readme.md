## Laravel PHP Framework
1
[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, queueing, and caching.

Laravel is accessible, yet powerful, providing powerful tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.

## Official Documentation

Documentation for the framework can be found on the [Laravel website](http://laravel.com/docs).

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

### License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### Gulp

Next, you'll want to pull in Gulp as a global NPM package:

npm install --global gulp
### Laravel Elixir

The only remaining step is to install Elixir! Within a fresh installation of Laravel, you'll find a package.json file in the root. Think of this like your composer.json file, except it defines Node dependencies instead of PHP. You may install the dependencies it references by running:

npm install
If you are developing on a Windows system or you are running your VM on a Windows host system, you may need to run the npm install command with the --no-bin-links switch enabled:

npm install --no-bin-links

### Running Elixir

Elixir is built on top of Gulp, so to run your Elixir tasks you only need to run the gulp command in your terminal. Adding the --production flag to the command will instruct Elixir to minify your CSS and JavaScript files:

// Run all tasks...
gulp

// Run all tasks and minify all CSS and JavaScript...
gulp --production
Watching Assets For Changes

Since it is inconvenient to run the gulp command on your terminal after every change to your assets, you may use the gulp watch command. This command will continue running in your terminal and watch your assets for any changes. When changes occur, new files will automatically be compiled:

gulp watch
