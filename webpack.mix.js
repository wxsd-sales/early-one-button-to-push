// noinspection JSUnresolvedFunction

const mix = require('laravel-mix')

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .sass('resources/sass/app.scss', 'public/css')
    .js('resources/js/app.js', 'public/js').vue()
    .browserSync({ proxy: 'localhost', open: false })

require('laravel-mix-eslint')
mix.eslint({ fix: true, extensions: ['js', 'vue'] })

if (!mix.inProduction()) {
    mix.extract()
    mix.sourceMaps()
}
if (mix.inProduction()) {
    mix.version()
}
