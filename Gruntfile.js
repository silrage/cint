module.exports = function(grunt) {
  var fileList = 'src/*.js';
  var buildDir = 'assets/';
  var distDir = 'src/';
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    less: {
      development: {
        options: {
          compress: true,
          yuicompress: true,
          optimization: 2,
          banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */'
        },
        files: {
          "assets/styles.min.css": "src/*.less" // destination file and source file
        }
      }
    },
    // jshint: {
    //   files: ['Gruntfile.js', distDir+'*.js']
    // },
    uglify: {
      options: {
        compress: false,
        beatify: true,
        report: 'min',
        mangle: false,
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
      },
      all_src: {
        src: fileList,
        dest: buildDir+'scripts.min.js',
      }
    },
    watch: {
      files: [
      // '<%= jshint.files %>',
      'src/*.less',
      'assets/styles.min.css'
      ],
      options: {
        nospawn: true
      },
      tasks: [
      'less',
      // 'jshint',
      'uglify'
      ]
    }
  });

  grunt.loadNpmTasks('grunt-contrib-uglify');
  // grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-less');

  grunt.registerTask('default', [
    // 'jshint',
    'less',
    'uglify',
    'watch'
    ]);

};