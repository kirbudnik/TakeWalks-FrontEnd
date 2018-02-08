var sassWatch = ['sass/**/*.scss'];

module.exports = function (grunt) {
  require('load-grunt-tasks')(grunt);

  grunt.initConfig({
    postcss: {
      options: {
        map: true,
        parser: require('postcss-scss'),
        processors: [
          require('precss')(),
          require('pixrem')(),
          require('autoprefixer')({browsers: 'last 10 versions'}),
          require('cssnano')({zindex: false})
        ]
      },
      dist: {
        src: 'webroot/css/*.css'
      }
    },

    sass: {
      dist: {
        files: {
          'webroot/css/app.css': 'sass/app.scss'
        }
      }
    },

    watch: {
      css: {
        files: sassWatch,
        tasks: ['sass', 'postcss'],
        options: {
          livereload: false
        }
      },
    },

    webfont: {
      icons: {
        src: 'svg/*.svg',
        dest: 'webroot/fonts/webfont',
        destCss: 'sass/icons/',
        autoHint: false,
        options: {
          stylesheet: 'scss',
          relativeFontPath: '../fonts/webfont',
          templateOptions: {
            baseClass: 'icon',
            classPrefix: 'icon-'
          }
        }
      }
    }
  });

  grunt.registerTask('build', ['webfont', 'sass', 'postcss']);
  grunt.registerTask('dev', ['webfont','sass', 'postcss', 'watch']);
};