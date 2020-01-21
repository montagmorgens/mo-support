'use strict';

const changed = require( 'gulp-changed' );
const config = require( '../config' );
const gulp = require( 'gulp' );
const imagemin = require( 'gulp-imagemin' );
const path = require( 'path' );

const paths = {
  src: path.join( config.root.src, config.tasks.images.src, '**/*.{' + config.tasks.images.extensions.join( ',' ) + '}' ),
  dest: path.join( config.root.build, config.tasks.images.dest )
};

const imagesTask = function() {
  return gulp.src( paths.src )
    .pipe( changed( paths.dest ) )
    .pipe(
      imagemin(
        [
          imagemin.svgo( {
            plugins: [ {
              removeViewBox: false
            } ]
          } )
        ]
      )
    )
    .pipe( gulp.dest( paths.dest ) );
};

gulp.task( 'images', imagesTask );
module.exports = imagesTask;
