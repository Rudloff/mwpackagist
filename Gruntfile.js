/*jslint node: true*/
module.exports = function (grunt) {
    'use strict';

    grunt.loadNpmTasks('grunt-jslint');
    grunt.loadNpmTasks('grunt-phpcs');
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
                standard: 'PSR12',
                bin: 'vendor/bin/phpcs'
            },
            php: {
                src: ['*.php', 'classes/*.php']
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
    grunt.registerTask('doc', ['phpdocumentor']);
};
