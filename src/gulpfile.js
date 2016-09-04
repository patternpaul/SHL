

var gulp = require('gulp');

var shell = require('gulp-shell')

var run = require('gulp-run');

var notify = require('gulp-notify');

var phpunit = require('gulp-phpunit');

var exec = require('gulp-exec');

gulp.task('phpunit-main', function() {

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


gulp.task('watch-clean', function() {
    gulp.watch(['tests/**/*.php', 'App/**/*.php', 'resources/**/*'], ['twig-clean','routes-clean', 'config-clean']);
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

