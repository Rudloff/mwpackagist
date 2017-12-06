/*jslint node: true*/
module.exports = function (grunt) {
    'use strict';

    grunt.loadNpmTasks('grunt-jslint');
    grunt.loadNpmTasks('grunt-shipit');
    grunt.loadNpmTasks('shipit-git-update');
    grunt.loadNpmTasks('shipit-composer-simple');
    grunt.loadNpmTasks('grunt-phpcs');
    grunt.loadNpmTasks('grunt-phpunit');
    grunt.loadNpmTasks('grunt-jsonlint');
    grunt.loadNpmTasks('grunt-fixpack');
    grunt.loadNpmTasks('grunt-phpdocumentor');

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
                bin: 'vendor/bin/phpunit',
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
        },
        jsonlint: {
            manifests: {
                src: ['*.json', '*.webapp'],
                options: {
                    format: true
                }
            }
        },
        fixpack: {
            package: {
                src: 'package.json'
            }
        },
        phpdocumentor: {
            doc: {
                options: {
                    directory: 'classes/,tests/'
                }
            }
        }
    });

    grunt.registerTask('lint', ['jslint', 'fixpack', 'jsonlint', 'phpcs']);
    grunt.registerTask('test', ['phpunit']);
    grunt.registerTask('prod', ['shipit:prod', 'update', 'composer:install']);
    grunt.registerTask('satis', ['shipit:prod', 'composer:cmd']);
    grunt.registerTask('doc', ['phpdocumentor']);
};
