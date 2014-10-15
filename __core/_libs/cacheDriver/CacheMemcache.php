<?php
defined('AHA_ROOT') OR die('Unauthorized access!');
/**
 * memcache缓存
 *
 * @author pangyiguang
 */
class CacheMemcache implements CacheDriver{
    
    private $_key=null;
    private $_Memcache=null;
    
    function __construct($initArr = array()) {
        try {
            $init=AHA::getConfig('memcache');
            $this->_Memcache=new Memcache();
            $this->_Memcache->connect($init['default']['host'], $init['default']['port'], $init['default']['timeout']);
        } catch (Exception $exc) {
            Common::output($exc->getMessage());
        }
    }
    
    function get($prefix,$key, $optionArr = array()) {
        $this->_getKey($prefix, $key);
        return $this->_Memcache->get($this->_key);
    }
    
    function set($prefix,$key, $value, $cacheTime, $optionArr = array()) {
        $this->_getKey($prefix, $key);
        return $this->_Memcache->set($this->_key, $value,1, $cacheTime);
    }
    
    
    function stats($prefix,$key, $optionArr = array()) {
        return false;
    }
    
   
    function getStats() {
        return $this->_Memcache->getStats();
    }
    
    function del($prefix,$key, $optionArr = array()) {
        $this->_getKey($prefix, $key);
        return $this->_Memcache->delete($this->_key);
    }
    
    function clean($prefix,$optionArr = array()) {
        return $this->_Memcache->flush();
    }
    
    function increment($prefix,$key,$num=1){
        $this->_getKey($prefix, $key);
        return $this->_Memcache->increment($this->_key,(int)$num);
    }
    
    function decrement($prefix,$key,$num=1){
        $this->_getKey($prefix, $key);
        return $this->_Memcache->decrement($this->_key,(int)$num);
    }
    
    private function _getKey($prefix,$key){
        $this->_key=$prefix.md5($key);
    }
    
    function __destruct() {
        $this->_Memcache->close();
    }
}
