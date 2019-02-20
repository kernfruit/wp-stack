<?php
namespace Deployer;

require 'recipe/common.php';

/**
 * Configuration
 */
set('keep_releases', 5);
set('repository', 'ssh://url-to-your.git');
set('git_tty', true); // [Optional] Allocate tty for git on first deployment
set('allow_anonymous_stats', false);

inventory('hosts.yml');

set('shared_dirs', ['web/app/uploads']);
set('writable_dirs', ['web/app/uploads']);

task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy your project');

after('deploy', 'success');

task('install-composer', function () {
    run("php -d allow_url_fopen=On -r \"readfile('https://getcomposer.org/installer');\" > composer-setup.php;");
    run("php -d allow_url_fopen=On composer-setup.php");
    run("php -r \"unlink('composer-setup.php');\"");
    run("echo \"alias composer=/usr/bin/php -d allow_url_fopen=On /usr/home/\$USER/composer.phar\" >> ~/.bashrc");

})->desc('Install composer on a host');
