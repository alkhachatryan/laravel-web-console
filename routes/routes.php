<?php

Route::post('laravelwebconsole/execute', 'Alkhachatryan\LaravelWebConsole\LaravelWebConsole@requestHandler')
    ->name('laravel.webconsole.execute');
