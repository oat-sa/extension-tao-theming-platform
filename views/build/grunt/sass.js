module.exports = function(grunt) { 

    var sass    = grunt.config('sass') || {};
    var watch   = grunt.config('watch') || {};
    var notify  = grunt.config('notify') || {};
    var root    = grunt.option('root') + '/taoThemingPlatform/views/';

    sass.taothemingplatform = { };
    sass.taothemingplatform.files = { };
    sass.taothemingplatform.files[root + 'css/platform-customizer.css'] = root + 'scss/platform-customizer.scss';

    watch.taothemingplatformsass = {
        files : [root + 'views/scss/**/*.scss'],
        tasks : ['sass:taothemingplatform', 'notify:taothemingplatformsass'],
        options : {
            debounceDelay : 1000
        }
    };

    notify.taothemingplatformsass = {
        options: {
            title: 'Grunt SASS', 
            message: 'SASS files compiled to CSS'
        }
    };

    grunt.config('sass', sass);
    grunt.config('watch', watch);
    grunt.config('notify', notify);

    //register an alias for main build
    grunt.registerTask('taothemingplatformsass', ['sass:taothemingplatform']);
};
