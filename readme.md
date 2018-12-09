# LaravelWebConsole

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![StyleCI][ico-styleci]][link-styleci]

Laravel Web Console is a package for Laravel applications that allow user to connect to the server via browser. 

![Screenshot](screenshot.png)

## Features
* Enable / Disable custom login
* Multi-account support
* Home dir selection
* Home dir selection for multiple accounts
* Custom password hashing



## Installation

Via Composer

``` bash
$ composer require alkhachatryan/laravel-web-console
```

## Configuration

Publish the config file

```bash
php artisan vendor:publish --tag=webconsole
```

Edit the /config/webconsole.php file, create your credentials in .env file.

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

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/alkhachatryan/laravel-web-console.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/alkhachatryan/laravel-web-console.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/161024221/shield

[link-packagist]: https://packagist.org/packages/alkhachatryan/laravel-web-console
[link-downloads]: https://packagist.org/packages/alkhachatryan/laravel-web-console
[link-styleci]: https://github.styleci.io/repos/161024221
[link-author]: https://github.com/alkhachatryan
[link-contributors]: ../../contributors]
