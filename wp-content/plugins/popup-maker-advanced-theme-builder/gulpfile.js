var gulp = require('gulp'),
    $fn = require('gulp-load-plugins')({ camelize: true }),
    plumberErrorHandler = {
        errorHandler: $fn.notify.onError({
            title: 'Gulp',
            message: 'Error: <%= error.message %>'
        })
    },
    pkg = require('./package.json');

gulp.task('js:admin', function() {
    return gulp.src(['assets/js/src/admin/plugins/**/*.js', 'assets/js/src/admin/general.js'])
        .pipe($fn.plumber(plumberErrorHandler))
        .pipe($fn.jshint())
        .pipe($fn.jshint.reporter('fail'))
        .pipe($fn.concat('admin.js'))
        .pipe(gulp.dest('assets/js'))
        .pipe($fn.uglify())
        .pipe($fn.rename({extname: '.min.js'}))
        .pipe(gulp.dest('assets/js'))
        .pipe($fn.livereload());
});

gulp.task('js:site', function() {
    return gulp.src(['assets/js/src/site/plugins/**/*.js', 'assets/js/src/site/general.js'])
        .pipe($fn.plumber(plumberErrorHandler))
        .pipe($fn.jshint())
        .pipe($fn.jshint.reporter('fail'))
        .pipe($fn.concat('site.js'))
        .pipe(gulp.dest('assets/js'))
        .pipe($fn.uglify())
        .pipe($fn.rename({extname: '.min.js'}))
        .pipe(gulp.dest('assets/js'))
        .pipe($fn.livereload());
});

gulp.task('js', ['js:admin', 'js:site']);

gulp.task('langpack:dep', function () {
    return gulp.src('deprecated/**/*.php')
        .pipe($fn.plumber(plumberErrorHandler))
        .pipe($fn.sort())
        .pipe($fn.wpPot( {
            domain: pkg.name,
            bugReport: 'https://wppopupmaker.com/support',
            team: 'WP Popup Maker <support@wppopupmaker.com>'
        } ))
        .pipe(gulp.dest('deprecated/languages'));
});

gulp.task('langpack', ['langpack:dep'], function () {
    return gulp.src(['inludes/**/*.php', '*.php'])
        .pipe($fn.plumber(plumberErrorHandler))
        .pipe($fn.sort())
        .pipe($fn.wpPot( {
            domain: pkg.name,
            bugReport: 'https://wppopupmaker.com/support',
            team: 'WP Popup Maker <support@wppopupmaker.com>'
        } ))
        .pipe(gulp.dest('languages'));
});

gulp.task('watch', function () {
    $fn.livereload.listen();
    gulp.watch('assets/js/src/admin/**/*.js', ['js:admin']);
    gulp.watch('assets/js/src/site/**/*.js', ['js:site']);
    gulp.watch('**/*.php', ['langpack']);
});

gulp.task('build', ['js', 'langpack'], function () {
    return gulp.src(['./**/*.*', '!./dist/**', '!./node_modules/**', '!./gulpfile.js', '!./package.json', '!./assets/js/src/**'])
        .pipe($fn.zip(pkg.name+'_v'+pkg.version+'.zip'))
        .pipe(gulp.dest('release'));
});

gulp.task('default', ['js', 'langpack', 'watch']);
