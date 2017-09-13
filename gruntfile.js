module.exports = function(grunt) {

  //Initializing the configuration object
    grunt.initConfig({

    concat: {
      options: {
        // separator: ';',
      },
      css: {
        src: [
          './public/assets/css/*.css',
          '!./public/assets/css/application.css',
        ],
        dest: './public/assets/css/application.css',
      },
      js_widgets: {
        src: [
          // './app/modules/*/widgets/*.js',
          './public/assets/js/widgets/*.widget.js',
        ],
        dest: './public/assets/js/widgets.js',
      },
      js_vendor: {
        src: [
          './public/assets/js/vendor/hogan-3.0.2.min.js',
          './public/assets/js/vendor/underscore.min.js',
          './public/assets/js/vendor/*.js',
        ],
        dest: './public/assets/js/vendor.min.js',
      },
    },
    shell: {
        publish: {
          command: './make.sh'
        },        
        compile: {
          command: 'php artisan compile'
        }
    },
    watch: {
        html: {
          files: [
            //watched files
            './app/views/*.blade.php',
            ],   
          options: {
            livereload: true                        //reloads the browser
          }
        },
        css: {
          files: [
            //watched files
            './public/assets/css/*.css',
            '!./public/assets/css/application.css',
            ],   
          tasks: ['concat:css'],     //tasks to run
          options: {
            livereload: true                        //reloads the browser
          }
        },
        js_widgets: {
          files: [
            //watched files
            './app/modules/*/widgets/*.js',
            './public/assets/js/widgets/*.widget.js',
            ],   
          tasks: ['concat:js_widgets', 'shell:compile'],     //tasks to run
          options: {
            livereload: false                        //reloads the browser
          }
        },         
        js_vendor: {
          files: [
            //watched files
            './public/assets/js/vendor/*.js',
            ],   
          tasks: ['concat:js_vendor'],     //tasks to run
          options: {
            livereload: false                        //reloads the browser
          }
        },        
        js_widget_templates: {
          files: [
            //watched files
            './app/modules/*/widgets/*.mustache',
            "./app/views/widgets/*.mustache",
            './public/assets/js/widgets.js'
            ],   
          tasks: ['shell:publish'],     //tasks to run
          options: {
            livereload: true                        //reloads the browser
          }
        },
      }
    });

  // Plugin loading
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-watch');
  // grunt.loadNpmTasks('grunt-templates-hogan');
  // grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-shell');

  // Task definition
  grunt.registerTask('default', ['watch']);

};