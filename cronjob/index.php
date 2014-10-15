<?php
/**
 * cron任务统一执行的文件，没有超时
 */
header('Content-Type:text/html; charset=utf-8');
set_time_limit(0);
define('APP_ROOT', dirname(__FILE__));
define('AHA_ROOT', dirname(APP_ROOT));
define('UPLOAD_ROOT', AHA_ROOT.'/upload');
define('CORE_ROOT', AHA_ROOT . '/__core');
define('DATA_ROOT', AHA_ROOT . '/data');
define('MODEL_ROOT', APP_ROOT . '/model');
define('ONING_ROOT', APP_ROOT . '/oning'); //定时执行文件目录
require CORE_ROOT . '/Common.php';
require CORE_ROOT . '/AHA.php'; //载入框架核心文件
spl_autoload_register(array('Common', 'loadClassFile'));
AHA::initConfig(include APP_ROOT . '/_config/inc.php'); //载入配置文件

//include ONING_ROOT.'/Baidulyric.php';

$__echo = false; //是否输出到屏幕
$__time_star = microtime(true);
$__now = time();

//不存在执行的配置文件时
if (!file_exists(APP_ROOT . '/_config/croning.php')) {
    exit('cron failed,please check the cron config!');
}

$__all = include APP_ROOT . '/_config/croning.php';
//数据不合法时
if (!$__all || !is_array($__all)) {
    exit('cron failed,please check the cron config!');
}


$__onFile = array();
if ($__all) {
    foreach ($__all as $__key => $__value) {
        if (strpos($__key, '-') === false) {//每周的处理
            preg_match('@^([\d\*]+) ([\d\*]+):([\d\*]+)$@U', $__key, $match);
        } else {//正常的处理
            preg_match('@^([\d\*]+)\-([\d\*]+)\-([\d\*]+) ([\d\*]+):([\d\*]+)$@U', $__key, $match);
        }
        if ($match) {
            array_shift($match);
            if (__getPreg($match, $__now)) {//是否是要执行的文件
                $__onFile = array_merge($__onFile, is_array($__value) ? $__value : array($__value));
            }
        }
    }
}
if ($__onFile) {
    Common::fileLog(DATA_ROOT . '/log/cron_index.log', '执行cron开始******************************' . date('Y-m-d H:i:s', $__now) . '******************************', $__echo);
    $__onFile = array_unique($__onFile);
    foreach ($__onFile as $__value) {
        if (file_exists(ONING_ROOT . '/' . $__value)) {
            $__time_star2 = microtime(true);
            Common::fileLog(DATA_ROOT . '/log/cron_index.log', "\t".$__value . ' 执行开始----------' . date('Y-m-d H:i:s') . '-----------', $__echo);
            include ONING_ROOT . '/' . $__value;
            Common::fileLog(DATA_ROOT . '/log/cron_index.log', "\t".$__value . ' 执行结束(花费时间：' . ((microtime(true) - $__time_star2) * 1000) . 'ms)-------------', $__echo);
        }
    }
    Common::fileLog(DATA_ROOT . '/log/cron_index.log', '执行cron结束(一共执行时间：' . ((microtime(true) - $__time_star) * 1000) . 'ms)*************' . date('Y-m-d H:i:s') . '*****************' . "\n##################################################\n", $__echo);
}

/**
 * 处理正则结果并返回该文件是否是当时要执行
 * @param array $match      正则结果，数组
 * @param integer $__now    当时时间戳
 * @return bool
 */
function __getPreg($match, $__now) {
    $back = false;
    list($__Y, $__m, $__d, $__N, $__H, $__i) = explode('-', date('Y-m-d-N-H-i', $__now));
    $argc = count($match);
    if ($argc === 3) {
        $argc = $match[0] === '*' ? $__N : $match[0];
        $argc.=' ';
        $argc.=$match[1] === '*' ? $__H : $match[1];
        $argc.=':';
        $argc.=$match[2] === '*' ? $__i : $match[2];
        $back = date('N H:i', $__now) === date($argc, $__now) ? true : false;
    } elseif ($argc === 5) {
        $argc = $match[0] === '*' ? $__Y : $match[0];
        $argc.='-';
        $argc.=$match[1] === '*' ? $__m : $match[1];
        $argc.='-';
        $argc.=$match[2] === '*' ? $__d : $match[2];
        $argc.=' ';
        $argc.=$match[3] === '*' ? $__H : $match[3];
        $argc.=':';
        $argc.=$match[4] === '*' ? $__i : $match[4];
        $back = date('Y-m-d H:i', $__now) === date($argc, $__now) ? true : false;
    }
    return $back;
}
