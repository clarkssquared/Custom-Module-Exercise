<?php

if (file_exists($app_root . '/' . $site_path . '/settings.provus.php')) {
  include $app_root . '/' . $site_path . '/settings.provus.php';
}

if (file_exists($app_root . '/' . $site_path . '/settings.pantheon.php')) {
  include $app_root . '/' . $site_path . '/settings.pantheon.php';
}

if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
  include $app_root . '/' . $site_path . '/settings.local.php';
}

$settings['config_sync_directory'] = '../config/default';$databases['default']['default'] = array (
  'database' => 'default',
  'username' => 'user',
  'password' => 'user',
  'prefix' => '',
  'host' => 'db',
  'port' => '3306',
  'isolation_level' => 'READ COMMITTED',
  'namespace' => 'Drupal\\mysql\\Driver\\Database\\mysql',
  'driver' => 'mysql',
  'autoload' => 'core/modules/mysql/src/Driver/Database/mysql/',
);
$settings['hash_salt'] = '6e1Iig0ans_k82OAcLvWf89kZHCIjSUCE4G_4LOPG3QZIy6oYtHd-M6ar7bEl_fSKOf5FGrJwg';
