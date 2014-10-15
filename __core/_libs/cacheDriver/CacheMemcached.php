<?php
defined('AHA_ROOT') OR die('Unauthorized access!');
/**
 * memcached缓存
 *
 * @author pangyiguang
 */
class CacheMemcached implements CacheDriver{
    
    private $_persistent_id='';
    private $_key=null;
    private $_Memcached=null;
    private $_time=0;
            
    function __construct($initArr = array()) {
        try {
            $init=AHA::getConfig('memcached');
            $this->_Memcached=new Memcached();
            $this->_Memcached->setOptions(array(
                Memcached::OPT_PREFIX_KEY=>$init['prefixKey'],
                Memcached::OPT_CONNECT_TIMEOUT=>$init['connectTimeout'],
            ));
            if(isset($init['host'][1])){
                $this->_Memcached->addServers($init['host']);
            }else{
                $this->_Memcached->addServer($init['host'][0][0], $init['host'][0][1], $init['host'][0][2]);
            }
            $this->_time=time();
        } catch (Exception $exc) {
            Common::output($exc->getMessage());
        }
    }
    
    function get($prefix,$key, $optionArr = array()) {
        $this->_getKey($prefix, $key);
        return $this->_Memcached->get($this->_key);
    }
    
    function set($prefix,$key, $value, $cacheTime, $optionArr = array()) {
        $this->_getKey($prefix, $key);
        if($cacheTime){
            $cacheTime+=$this->_time;
        }
        return $this->_Memcached->set($this->_key, $value, $cacheTime);
    }
    
    
    function stats($prefix,$key, $optionArr = array()) {
        return false;
    }
    
   
    function getStats() {
        return $this->_Memcached->getStats();
    }
    
    function del($prefix,$key, $optionArr = array()) {
        $this->_getKey($prefix, $key);
        return $this->_Memcached->delete($this->_key);
    }
    
    function clean($prefix,$optionArr = array()) {
        return $this->_Memcached->flush();
    }
    
     /**
     * 增加cache中存储的$key的值
     * 如果元素的值不是数值类型，将其作为0处理
     * 成功时返回新的元素值,失败时返回false
     * 
     * @param tring $prefix    key的前缀
     * @param string $key       key值
     * @param integer $num       增加的值，默认为1，如果key所对应的元素不是数值类型，并且不能被转换为数值，会将此值修改为$value
     * @return type
     */
    function increment($prefix,$key,$num=1){
        $this->_getKey($prefix, $key);
        return $this->_Memcached->increment($this->_key,(int)$num);
    }
    
    /**
     * 减小cache中存储的$key的值
     * 如果元素的值不是数值类型，将其作为0处理
     * 成功时返回新的元素值,失败时返回false
     * 
     * @param string $prefix    key的前缀
     * @param string $key       key值
     * @param integer $num       减少的值，默认为1，如果key所对应的元素不是数值类型，并且不能被转换为数值，会将此值修改为$value，如果运算结果小于0，则返回的结果是0
     * @return integer
     */
    function decrement($prefix,$key,$num=1){
        $this->_getKey($prefix, $key);
        return $this->_Memcached->decrement($this->_key,(int)$num);
    }
    
    private function _getKey($prefix,$key){
        $this->_key=$prefix.md5($key);
    }
}
