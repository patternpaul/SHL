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
    mix.sass('app.scss');
});




var gulp = require('gulp');

var shell = require('gulp-shell')

var run = require('gulp-run');

var notify = require('gulp-notify');

var phpunit = require('gulp-phpunit');

var exec = require('gulp-exec');

gulp.task('phpunit-main', function() {

    /*
     gulp.src('phpunit.xml')
     .pipe(phpunit('', {debug: false, notify: true, testSuite: './tests/main/'}))
     .on('error', notify.onError({
     title: 'PHPUnit Failed',
     message: 'One or more tests failed.'
     }))
     .pipe(notify({
     title: 'PHPUnit Passed',
     message: 'All tests passed!'
     }));
     */

    var reportOptions = {
        err: false, // default = true, false means don't write err
        stderr: true, // default = true, false means don't write stderr
        stdout: true // default = true, false means don't write stdout
    }
    var options = {
        continueOnError: false, // default = false, true means don't emit error event
        pipeStdout: false // default = false, true means stdout is written to file.contents
    };


    gulp.src('')
        .pipe(exec('./vendor/bin/phpunit', options))
        .on('error', notify.onError({
            title: 'PHPUnit Failed',
            message: 'One or more tests failed.'
        }))
        .pipe(exec.reporter(reportOptions));

});

gulp.task('watch', function() {
    gulp.watch(['tests/**/*.php', 'App/**/*.php', 'resources/**/*'], ['phpunit-main', 'twig-clean','routes-clean', 'config-clean']);
});

gulp.task('watch-bak', function() {
    gulp.watch(['tests/**/*.php', 'App/**/*.php', 'resources/**/*'], ['phpunit-main', 'twig-clean','routes-clean', 'config-clean']);
});

gulp.task('watch-regression', function() {
    gulp.watch(['tests/**/*.php', 'App/**/*.php', 'resources/**/*'], ['phpunit-regression']);
});

gulp.task('twig-clean', shell.task([
    'php artisan twig:clean'
], {ignoreErrors: true}))

gulp.task('routes-clean', shell.task([
    'php artisan route:cache'
], {ignoreErrors: true}))

gulp.task('config-clean', shell.task([
    'php artisan config:clear'
], {ignoreErrors: true}))




gulp.task('prep-watch', function() {
    gulp.watch(['resources/**/*', 'App/**/*.php', 'App/Http/routes.php'], ['twig-clean','routes-clean','config-clean']);
});
