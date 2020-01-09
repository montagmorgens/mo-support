'use strict';

const config = require('../config');
const cssnano = require('cssnano');
const gulp = require('gulp');
const handleErrors = require('../lib/handleErrors');
const path = require('path');
const postcss = require('gulp-postcss');
const sass = require('gulp-sass');
const touch = require('gulp-touch-fd');

const paths = {
  src: path.join(config.root.src, config.tasks.css.src, '**/*.{' + config.tasks.css.extensions.join(',') + '}'),
  top: path.join(config.root.src, config.tasks.css.src),
  dest: path.join(config.root.build, config.tasks.css.dest)
};

const postcssPlugins = [
  cssnano({ preset: ['advanced', config.tasks.css.cssnano] })
];

const cssTask = function (done) {

  return gulp.src(paths.src)
    .pipe(sass())
    .on('error', handleErrors)
    .pipe(postcss(postcssPlugins))
    .on('error', handleErrors)
    .pipe(gulp.dest(paths.dest))
    .pipe(touch());
};

gulp.task('css', cssTask);
module.exports = cssTask;
