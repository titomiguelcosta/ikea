<?php

namespace Deployer;

require 'recipe/symfony4.php';

set('application', 'ikea:api');
set('repository', 'git@github.com:titomiguelcosta/ikea.git');
set('git_tty', false);
set('keep_releases', 3);
set('shared_dirs', ['var/log', 'var/sessions', 'vendor']);
set('shared_files', ['.env.prod.local', '.env.test.local']);
set('writable_dirs', ['var/log', 'var/cache']);
set('writable_mode', 'acl');
set('composer_action', 'install');
set('composer_options', '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader --no-suggest');

task('tests:execute', function () {
    run('php {{release_path}}/bin/console doctrine:migrations:migrate --env=test -n');
    run('cd {{release_path}} && php vendor/bin/phpunit');
});

host('ikea.titomiguelcosta.com')
    ->user('ubuntu')
    ->stage('prod')
    ->set('deploy_path', '/mnt/websites/ikea')
    ->set('branch', 'master')
    ->set('env', ['APP_ENV' => 'prod']);

after('deploy:failed', 'deploy:unlock');
before('deploy:symlink', 'database:migrate');
after('database:migrate', 'tests:execute');
