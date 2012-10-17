<?php
/**
 * Magazine
 *
 * Setup Namespace, Menu and Settings
 *
 * @package Magazine
 * @subpackage build
 */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* Path to Build Config, Setup and MODX */
require_once dirname(__FILE__) . '/build.config.php';
require_once dirname(__FILE__) . '/setup.class.php';
include_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('mgr');
echo '<pre>'; /* used for nice formatting of log messages */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$modx->log(modX::LOG_LEVEL_INFO, 'Instantiated magazine');

/* Instantiate Setup using static factory method */
$setup = Setup::factory($modx, 'magazine');

/* Create Namespace and save */
$namespace = $setup->createNamespace('{base_path}magazine/core/components/magazine/', true);

/* Create Action and add Namespace to it */
$action = $setup->createAction();
$action->addOne($namespace);

/* Create Menu, add Action and save */
$menu = $setup->createMenu('magazine', 'magazine.menu_desc');
$menu->addOne($action);
$menu->save();

$modx->log(modX::LOG_LEVEL_INFO, 'Created Namespace and Menu.');

$modx->log(modX::LOG_LEVEL_INFO, 'Creating Settings...');
/* Core and Assets Path */
$setup->createSetting('core_path', '{base_path}magazine/core/components/magazine/');
$setup->createSetting('assets_path', '{base_path}magazine/assets/components/magazine/');

/* Assets URL */
$setup->createSetting('assets_url', '{base_url}magazine/assets/components/magazine/');

$modx->log(modX::LOG_LEVEL_INFO, 'Clearing Settings Cache...');
/* Clear the cache: */
$cacheRefreshOptions =  array('system_settings' => array());
$modx->cacheManager-> refresh($cacheRefreshOptions);
$modx->log(modX::LOG_LEVEL_INFO, 'Settings Cache Cleared.');
