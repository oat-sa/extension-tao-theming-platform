module.exports = function(grunt) { 

    var requirejs   = grunt.config('requirejs') || {};
    var clean       = grunt.config('clean') || {};
    var copy        = grunt.config('copy') || {};

    var root        = grunt.option('root');
    var libs        = grunt.option('mainlibs');
    var ext         = require(root + '/tao/views/build/tasks/helpers/extensions')(grunt, root);
    var out         = 'output';


    /**
     * Remove bundled and bundling files
     */
    clean.taothemingplatformbundle = [out];
    
    /**
     * Compile tao files into a bundle 
     */
    requirejs.taothemingplatformbundle = {
        options: {
            baseUrl : '../js',
            dir : out,
            mainConfigFile : './config/requirejs.build.js',
            paths : { 'taoThemingPlatform' : root + '/taoThemingPlatform/views/js' },
            modules : [{
                name: 'taoThemingPlatform/controller/routes',
                include : ext.getExtensionsControllers(['taoThemingPlatform']),
                exclude : ['mathJax', 'mediaElement'].concat(libs)
            }]
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taothemingplatformbundle = {
        files: [
            { src: [out + '/taoThemingPlatform/controller/routes.js'],  dest: root + '/taoThemingPlatform/views/js/controllers.min.js' },
            { src: [out + '/taoThemingPlatform/controller/routes.js.map'],  dest: root + '/taoThemingPlatform/views/js/controllers.min.js.map' }
        ]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('taothemingplatformbundle', ['clean:taothemingplatformbundle', 'requirejs:taothemingplatformbundle', 'copy:taothemingplatformbundle']);
};
