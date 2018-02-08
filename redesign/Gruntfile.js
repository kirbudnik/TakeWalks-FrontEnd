module.exports = function(grunt) {
  grunt.initConfig({

    includereplace: {
      options: {},
      dist: {
        src: '*.html',
        dest: 'build/'
      }
    },

    sass: {
      dist: {
        files: {
          'build/css/app.css': 'source/sass/app.scss',
        }
      }
    },

    watch: {
      css: {
        files: ['source/sass/*', 'source/less/*', 'build/css/app.css'],
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
        src: 'build/css/app.css',
        dest: 'build/css/app-prefixed.css'
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
          'build/css/app-less.css' : [ 'source/less/app.less' ]
        }
      }
    },

    cssmin: {
      target: {
        files: [{
          expand: true,
          cwd: 'build/css',
          src: ['*.css', '!*.min.css'],
          dest: 'build/css',
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