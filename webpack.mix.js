let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .setPublicPath('public/build')
    .setResourceRoot('/build/')
    .js('resources/assets/js/app.js', 'js')
    .sass('resources/assets/sass/style.scss', 'css')
    .js('resources/assets/js/admin.js', 'js')
    .sass('resources/assets/sass/admin.scss', 'css')
    .version();
