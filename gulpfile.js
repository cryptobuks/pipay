var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    //Sass
    mix.sass([
        'app.scss',
    ], 'public/assets/css');

    mix.sass([
        'pi_payment.scss'
    ], 'public/assets/css/pi_payment.css');

    //Merge dependencies
    //translate to CDN service later.
    mix.scripts([
        'jquery-1.11.3.min.js',
        'json2.min.js',
        'socket.io-1.3.5.js',
    ], 'public/assets/js/depend.js');


    //later CDN too
    mix.scripts([
        'architekt.js',
    ], 'public/assets/js/architekt.js');

    //Merge modules
    //CDN too? i dunno
    mix.scripts([
        'reserved/clipboard.js',
        'reserved/comparator.js',
        'reserved/customWidget.js',
        'reserved/dataTable.js',
        'reserved/formatter.js',
        'reserved/http.js',
        'reserved/locale.js',
        'reserved/printer.js',
        'reserved/validator.js',
        'reserved/widget.js',
        //Application source
    ], 'public/assets/js/architekt_modules.js');

    //Application source
    mix.scripts([
        'app.js',
    ], 'public/assets/js/app.js');

});
