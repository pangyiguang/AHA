<?php
defined('AHA_ROOT') OR die('Unauthorized access!');
/**
 * redis缓存
 * @author pangyiguang
 */
class CacheRedis implements CacheDriver {
    
    function __construct($initArr = array()) {
        ;
    }
    
    function get($prefix,$key, $optionArr = array()) {
        ;
    }
    
    function set($prefix,$key, $value, $cacheTime, $optionArr = array()) {
        ;
    }
    
    function stats($prefix,$key, $optionArr = array()) {
        ;
    }
    
    function del($prefix,$key, $optionArr = array()) {
        ;
    }
    
    function clean($prefix,$optionArr = array()) {
        ;
    }
}
