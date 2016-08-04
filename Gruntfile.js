/*jslint node: true*/
module.exports = function (grunt) {
    'use strict';

    grunt.loadNpmTasks('grunt-jslint');
    grunt.loadNpmTasks('grunt-shipit');
    grunt.loadNpmTasks('shipit-git-update');
    grunt.loadNpmTasks('shipit-composer-simple');

    grunt.initConfig({
        jslint: {
            Gruntfile: {
                src: 'Gruntfile.js'
            }
        },
        shipit: {
            prod: {
                deployTo: '/var/www/mwpackagist',
                servers: 'pierre@dev.rudloff.pro',
                composer: {
                    noDev: true,
                    cmd: 'satis'
                }
            }
        }
    });

    grunt.registerTask('lint', ['jslint']);
    grunt.registerTask('prod', ['shipit:prod', 'update', 'composer:install']);
    grunt.registerTask('satis', ['shipit:prod', 'composer:cmd']);
};
