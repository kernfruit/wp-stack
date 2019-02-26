<?php
namespace Deployer;

require 'recipe/common.php';

/**
 * Deploy
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

/**
 * Database
 */
task('db:backup', function () {
    $remoteDBName = run('. {{release_path}}/.env && echo $DB_NAME');
    $filename = $remoteDBName.'-'.date('Ymd_His').'.sql.gz';
    run('mkdir -p {{deploy_path}}/backup');
    run('. {{release_path}}/.env && mysqldump --add-drop-table -u$DB_USER -p$DB_PASSWORD $DB_NAME | gzip > {{deploy_path}}/backup/'.$filename);
})->desc('Backup database');

task('db:rollback', function () {
    $lastBackup = run('find {{deploy_path}}/backup/ -type f -printf "%p\n" | sort -r | sed "2q;d"');
    run('. {{release_path}}/.env && gzip -dc '.$lastBackup.' | mysql -u$DB_USER -p$DB_PASSWORD $DB_NAME');
})->desc('Import last database backup');

before('db:rollback', 'db:backup');

task('db:pull', function () {
    run('. {{release_path}}/.env && mysqldump -u$DB_USER -p$DB_PASSWORD $DB_NAME | gzip > .db.temp.sql.gz');
    download('.db.temp.sql.gz', '.');
    runLocally('lando db-import .db.temp.sql.gz && rm .db.temp.sql.gz');
    run('rm .db.temp.sql.gz');
})->desc('Pull database from server');

task('db:push', function () {
    $localWPHome = runLocally('. .env.lando && echo $WP_HOME');

    runLocally('lando db-export --stdout | gzip > .db.temp.sql.gz');
    upload('.db.temp.sql.gz', '.');

    run('. {{release_path}}/.env && gzip -dc .db.temp.sql.gz | mysql -u$DB_USER -p$DB_PASSWORD $DB_NAME && rm .db.temp.sql.gz && ./wp-cli.phar search-replace "'.$localWPHome.'" "${WP_HOME}" --path="{{release_path}}/web/wp"');
    run('./wp-cli.phar transient delete --all --network --path="{{release_path}}/web/wp"');

    runLocally('rm .db.temp.sql.gz');
})->desc('Push database to server');

before('db:push', 'db:backup');

/**
 * Uploads
 */
task('uploads:pull', function () {
    download('{{release_path}}/web/app/uploads/', 'web/app/uploads/');
})->desc('Copy uploads from server');

task('uploads:push', function () {
    upload('web/app/uploads/', '{{release_path}}/web/app/uploads/');
})->desc('Copy uploads to server');

/**
 * Content
 */
task('content:pull', [
    'uploads:pull',
    'db:pull',
])->desc('Pull database and uploads from server');

task('content:push', [
    'uploads:push',
    'db:push',
])->desc('Push database and uploads to server');

/**
 * WP-CLI
 */
task('wp-cli:install', function () {
    run("curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar");
    run("chmod +x wp-cli.phar");
    run("echo \"alias wp=/usr/home/\$USER/wp-cli.phar\" >> ~/.bashrc");
})->desc('Install WP-CLI on host');
