module.exports = function(grunt) {
  grunt.initConfig({

    includereplace: {
      options: {},
      dist: {
        src: '*.html',
        dest: 'webroot/'
      }
    },

    sass: {
      dist: {
        files: {
          'webroot/css/app.css': 'source/sass/app.scss',
        }
      }
    },

    watch: {
      css: {
        files: ['source/sass/*', 'source/less/*', 'webroot/css/app.css'],
        tasks: ['sass', 'less', 'autoprefixer', 'cssmin'],
      },
      includereplace: {
        files: ['*.html', 'includes/*.html'],
        tasks: ['includereplace']
      }
    },
    
    autoprefixer: {
      sourcemap: {
        options: {
          map: true
        },
        src: 'webroot/css/app.css',
        dest: 'webroot/css/app-prefixed.css'
      }
    },

    less: {
      dist: {
        options: {
          paths: ['source/less'],
          plugins: [
            new (require('less-plugin-clean-css'))()
          ],
        },
        files: {
          'webroot/css/app-less.css' : [ 'source/less/app.less' ]
        }
      }
    },

    cssmin: {
      target: {
        files: [{
          expand: true,
          cwd: 'webroot/css',
          src: ['*.css', '!*.min.css'],
          dest: 'webroot/css',
          ext: '.min.css'
        }]
      },
      options: {
        sourceMap: true
      }
    }
  });
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-include-replace');
  grunt.loadNpmTasks('grunt-autoprefixer');
  grunt.registerTask('build', ['sass', 'less', 'autoprefixer', 'cssmin', 'includereplace']);
}