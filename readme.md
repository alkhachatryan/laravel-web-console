# LaravelWebConsole

[![Latest Version on Packagist][ico-version]][link-packagist]
[![StyleCI][ico-styleci]][link-styleci]
![TESTED OS](https://img.shields.io/badge/Tested%20OS-Linux-brightgreen.svg)

Laravel Web Console is a package for Laravel applications that allow user to connect to the server via browser. 

![Screenshot](screenshot.png)

## What is this package useful for?

Despite the fact that cloud hosting is now growing up and many people use VPS / Dedicated Server hosting, most people still use Sharing hosting without SSH connection. Using this package you can execute shell commands from your browser. Using Laravel Middleware features you can protect your system from danger from outside.

## Features

* Enable / Disable custom login
* Multi-account support
* Home dir selection
* Home dir selection for multiple accounts
* Custom password hashing

## Supported Laravel Versions
* 5.7.*
* 5.8.*

## Installation

Manually:

- Download the last release: https://github.com/alkhachatryan/laravel-web-console/releases/latest
- Upload the compressed file to the server.
- Unzip the files into /vendor/alkhachatryan/laravel-web-console  (Without version number)
- Add maintance for this package into composer autoloaders
  -- In /vendor/composer/autoload_namespaces.php add in the array this line:
  ```php 
   'Alkhachatryan\\LaravelWebConsole\\' => array($vendorDir . '/alkhachatryan/laravel-web-console/src'),
  ```
  -- In /vendor/composer/autoload_psr4.php add in the array this line:
  ```php 
   'Alkhachatryan\\LaravelWebConsole\\' => array($vendorDir . '/alkhachatryan/laravel-web-console/src'),
  ```
- Update the /config/app.php and add the service provider into providers array
  ```php 
  Alkhachatryan\LaravelWebConsole\LaravelWebConsoleServiceProvider::class,
  ```
- Remove the cache: delete the following files:
  /bootstrap/cache/packages.php
  /bootstrap/cache/services.php

Or Via Composer:

``` bash
$ composer require alkhachatryan/laravel-web-console
```




## Configuration

Publish the config file

- Copy /vendor/alkhachatryan/laravel-web-console/config file to your /config folder

  OR via command line: 
  ```bash
  php artisan vendor:publish --tag=webconsole
  ```

- Edit the /config/laravelwebconsole.php file, create your credentials in .env file.

  ```php
  // Single-user credentials (REQUIRED)
      'user' => [
          'name' => env('CONSOLE_USER_NAME', 'root'),
          'password' => env('CONSOLE_USER_PASSWORD', 'root')
      ],
  ```

!!! ATTENTION !!!!
These user credentials ARE NOT your server user credentials.
You can type here everything you want.
This method of custom login is a small addition in the protection.
Anyway you can disable it. Set no_login value TRUE

```php
// Disable login (don't ask for credentials, be careful)
    'no_login' => true,
    ]
```

## Usage
```php
use Alkhachatryan\LaravelWebConsole\LaravelWebConsole;

class HomeController extends Controller
{
    public function index() {
       return LaravelWebConsole::show();
    }
}
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.


## Security

If you discover any security related issues, please email info@khachatryan.org instead of using the issue tracker.

## Credits

- [Alexey Khachatryan][link-author]

## Open source tools included

- jQuery JavaScript Library: https://github.com/jquery/jquery
- jQuery Terminal Emulator: https://github.com/jcubic/jquery.terminal
- jQuery Mouse Wheel Plugin: https://github.com/brandonaaron/jquery-mousewheel
- PHP JSON-RPC 2.0 Server/Client Implementation: https://github.com/sergeyfast/eazy-jsonrpc
- Normalize.css: https://github.com/necolas/normalize.css
- Nickola/Web-console https://github.com/nickola/web-console

## License

MIT. Please see the [license file](license) for more information.

[ico-version]: https://img.shields.io/packagist/v/alkhachatryan/laravel-web-console.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/161024221/shield
[link-packagist]: https://packagist.org/packages/alkhachatryan/laravel-web-console
[link-styleci]: https://github.styleci.io/repos/161024221
[link-author]: https://github.com/alkhachatryan
[link-contributors]: ../../contributors]
