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
    ], 
        'public/assets/css'
    );

    //Minifying and merge scripts
    mix.scripts([
        'jquery-1.11.3.min.js',
        'json2.min.js',
        'mobile-detect.min.js',
        'architekt.js',
        //modules here
        'reserved/locale.js',
        'reserved/widget.js',
        //Application source
        'app.js',
    ], 
        'public/assets/js/all.js'
    );
});
