/*jslint node: true*/
module.exports = function (grunt) {
    'use strict';

    grunt.loadNpmTasks('grunt-jslint');
    grunt.loadNpmTasks('grunt-shipit');
    grunt.loadNpmTasks('shipit-git-update');

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
                postUpdateCmd: 'composer install --no-dev'
            }
        }
    });

    grunt.registerTask('satis', function () {
        grunt.shipit.remote('cd ' + grunt.shipit.config.deployTo + '; composer satis', this.async());
    });

    grunt.registerTask('lint', ['jslint']);
    grunt.registerTask('prod', ['shipit:prod', 'update']);
    grunt.registerTask('prod:satis', ['shipit:prod', 'satis']);
};
