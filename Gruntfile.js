/*jslint node: true*/

/**
 * Initialize Grunt config.
 * @param {Object} grunt
 * @param {Function} grunt.initConfig
 * @param {Function} grunt.loadNpmTasks
 * @param {Function} grunt.registerTask
 */
module.exports = function (grunt) {
    'use strict';

    grunt.loadNpmTasks('grunt-jslint');
    grunt.loadNpmTasks('grunt-phpcs');
    grunt.loadNpmTasks('grunt-jsonlint');
    grunt.loadNpmTasks('grunt-fixpack');
    grunt.loadNpmTasks('grunt-phpdocumentor');
    grunt.loadNpmTasks('grunt-phpstan');

    grunt.initConfig({
        jslint: {
            Gruntfile: {
                src: 'Gruntfile.js'
            }
        },
        phpcs: {
            options: {
                standard: 'PSR12',
                bin: 'vendor/bin/phpcs'
            },
            php: {
                src: ['mwpackagist-build', 'classes/*.php']
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
        },
        phpstan: {
            options: {
                level: 'max',
                bin: 'vendor/bin/phpstan'
            },
            php: {
                src: ['mwpackagist-build', 'classes/*.php']
            }
        }
    });

    grunt.registerTask('lint', ['jslint', 'fixpack', 'jsonlint', 'phpcs', 'phpstan']);
    grunt.registerTask('doc', ['phpdocumentor']);
};
