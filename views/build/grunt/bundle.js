module.exports = function(grunt) { 

    var requirejs   = grunt.config('requirejs') || {};
    var clean       = grunt.config('clean') || {};
    var copy        = grunt.config('copy') || {};

    var root        = grunt.option('root');
    var libs        = grunt.option('mainlibs');
    var ext         = require(root + '/tao/views/build/tasks/helpers/extensions')(grunt, root);

    /**
     * Remove bundled and bundling files
     */
    clean.taosimpledeliverybundle = ['output',  root + '/taoSimpleDelivery/views/js/controllers.min.js'];
    
    /**
     * Compile tao files into a bundle 
     */
    requirejs.taosimpledeliverybundle = {
        options: {
            baseUrl : '../js',
            dir : 'output',
            mainConfigFile : './config/requirejs.build.js',
            paths : { 'taoSimpleDelivery' : root + '/taoSimpleDelivery/views/js' },
            modules : [{
                name: 'taoSimpleDelivery/controller/routes',
                include : ext.getExtensionsControllers(['taoSimpleDelivery']),
                exclude : ['mathJax', 'mediaElement'].concat(libs)
            }]
        }
    };

    /**
     * copy the bundles to the right place
     */
    copy.taosimpledeliverybundle = {
        files: [
            { src: ['output/taoSimpleDelivery/controller/routes.js'],  dest: root + '/taoSimpleDelivery/views/js/controllers.min.js' },
            { src: ['output/taoSimpleDelivery/controller/routes.js.map'],  dest: root + '/taoSimpleDelivery/views/js/controllers.min.js.map' }
        ]
    };

    grunt.config('clean', clean);
    grunt.config('requirejs', requirejs);
    grunt.config('copy', copy);

    // bundle task
    grunt.registerTask('taosimpledeliverybundle', ['clean:taosimpledeliverybundle', 'requirejs:taosimpledeliverybundle', 'copy:taosimpledeliverybundle']);
};
