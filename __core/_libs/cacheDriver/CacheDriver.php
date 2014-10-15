<?php
defined('AHA_ROOT') OR die('Unauthorized access!');
/**
 * Description of CacheDriver
 *
 * @author pangyiguang
 */
interface CacheDriver {
    function __construct($initArr=array());
    
    /**
     * 获取一个缓存信息
     */
    function get($prefix,$key,$optionArr=array());
    
    /**
     * 设置一个缓存
     */
    function set($prefix,$key,$value,$cacheTime,$optionArr=array());
    
    /**
     * 获取缓存信息
     * return array('size','expireTime','mtime','content');
     */
    function stats($prefix,$key,$optionArr=array());
    
    /**
     * 删除一个缓存
     */
    function del($prefix,$key,$optionArr=array());
    
    /**
     * 删除所有缓存
     */
    function clean($prefix,$optionArr=array());
}
