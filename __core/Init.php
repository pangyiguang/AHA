<?php
/**
 * 程序初始化调用
 * 调用前务必定义应用名称（目录）APP_NAME的值，而且应用名称必须与__core同级
 * 使用此框架时，必须调用
 */
header('Content-Type:text/html; charset=utf-8');
defined('APP_NAME') OR die('Unauthorized access!');

define('AHA_ROOT', dirname(dirname(__FILE__)));
define('DATA_ROOT', AHA_ROOT . DIRECTORY_SEPARATOR . 'data');
define('UPLOAD_ROOT', AHA_ROOT . DIRECTORY_SEPARATOR . 'upload');
define('APP_ROOT', AHA_ROOT . DIRECTORY_SEPARATOR . APP_NAME);
define('CORE_ROOT', AHA_ROOT . DIRECTORY_SEPARATOR . '__core');
define('ACTION_ROOT', APP_ROOT . DIRECTORY_SEPARATOR . 'action');
define('MODEL_ROOT', APP_ROOT . DIRECTORY_SEPARATOR . 'model');
define('VIEW_ROOT', APP_ROOT . DIRECTORY_SEPARATOR . 'view');
require CORE_ROOT . DIRECTORY_SEPARATOR . 'Common.php';
require CORE_ROOT . DIRECTORY_SEPARATOR . 'AHA.php'; //载入框架核心文件
spl_autoload_register(array('Common', 'loadClassFile'));
if (!file_exists(APP_ROOT . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'inc.php')) {
    Common::output('配置文件不存在：' . APP_ROOT . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'inc.php');
}
AHA::initConfig(include APP_ROOT . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'inc.php'); //载入配置文件
AHA::goAction();
