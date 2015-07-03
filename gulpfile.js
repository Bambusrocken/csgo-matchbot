var elixir = require('laravel-elixir');

// Elixir Extensions Variables
var gulp = require('gulp');
var del = require('del'); // 'del' extension requires "npm install --save-dev del"

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

// Elixir Extension: Remove
elixir.extend("remove", function(path) {
    gulp.task("remove", function() {
        del(path);
    });
    return this.queueTask("remove");
});

elixir(function(mix) {
    mix.less('app.less', 'resources/css');

    mix.styles([
        'libs/bootstrap.min.css',
        'app.css',
        'libs/select2.min.css',
    ]);

    mix.scripts([
        'libs/jquery-2.1.3.min.js',
        'libs/bootstrap.min.js',
        'libs/select2.min.js',
    ]);

    mix.version(['css/all.css', 'js/all.js']);

    mix.copy('./resources/fonts/**', 'public/build/fonts');

    mix.remove(['public/css', 'public/js']);
});
