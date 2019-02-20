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

task('deploy:environment', 'cp .env.production .env');

task('deploy', [
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:environment',
    'deploy:writable',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy your project');

after('deploy', 'success');
