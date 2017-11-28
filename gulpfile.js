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

    var data = require('./bundles.json');

    for (var bundle in data.styles) {
        var styles = data.styles[bundle];

        if (!styles.files || !styles.dir || !styles.output) {
            continue;
        }

        mix.styles(styles.files, styles.dir + '/' + styles.output, styles.dir);
    }

    for (var bundle in data.scripts) {
        var scripts = data.scripts[bundle];

        if (!scripts.files || !scripts.dir || !scripts.output) {
            continue;
        }

        mix.scripts(scripts.files, scripts.dir + '/' + scripts.output, scripts.dir);
    }

});