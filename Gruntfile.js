/*jslint node: true*/
module.exports = function (grunt) {
    'use strict';

    grunt.loadNpmTasks('grunt-jslint');
    grunt.loadNpmTasks('grunt-shipit');
    grunt.loadNpmTasks('shipit-git-update');
    grunt.loadNpmTasks('shipit-composer-simple');
    grunt.loadNpmTasks('grunt-phpcs');
    grunt.loadNpmTasks('grunt-phpunit');

    grunt.initConfig({
        jslint: {
            Gruntfile: {
                src: 'Gruntfile.js'
            }
        },
        phpcs: {
            options: {
                standard: 'PSR2',
                bin: 'vendor/bin/phpcs'
            },
            php: {
                src: ['*.php', 'classes/*.php']
            },
            tests: {
                src: ['tests/*.php']
            }
        },
        phpunit: {
            options: {
                bin: 'php -dzend_extension=xdebug.so ./vendor/bin/phpunit',
                stopOnError: true,
                stopOnFailure: true,
                followOutput: true
            },
            classes: {
                dir: 'tests/'
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

    grunt.registerTask('lint', ['phpcs', 'jslint']);
    grunt.registerTask('test', ['phpunit']);
    grunt.registerTask('prod', ['shipit:prod', 'update', 'composer:install']);
    grunt.registerTask('satis', ['shipit:prod', 'composer:cmd']);
};
