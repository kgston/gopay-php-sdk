module.exports = function (grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        composer: grunt.file.readJSON('composer.json'),
        watch: {
            options: {
                livereload: true
            },
            files: [
                'Gruntfile.js',
                'src/**/*.php'
            ],
            tasks: ['default']
        },
        phpcs: {
            application: {
                src: ['src/**/*.php', 'tests/**/*.php']
            },
            options: {
                bin: 'vendor/bin/phpcs',
                standard: 'PSR2'
            }
        },
        phpunit: {
            classes: {
                dir: 'tests'
            },
            options: {
                bin: 'vendor/bin/phpunit',
                // testSuffix: "ScheduledPaymentTest.php",
                staticBackup: false,
                colors: true,
                noGlobalsBackup: false
            }
        }
    });

    grunt.loadNpmTasks('grunt-phpunit');
    grunt.loadNpmTasks('grunt-phplint');
    grunt.loadNpmTasks('grunt-phpcs');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', ['phplint', 'phpcs', 'phpunit']);
    grunt.registerTask('livereload', ['default', 'watch']);

};