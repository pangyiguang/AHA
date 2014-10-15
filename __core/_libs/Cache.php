<?php
defined('AHA_ROOT') OR die('Unauthorized access!');

/**
 * 缓存处理类
 * @author pangyiguang
 */

class Cache {
    
    private static $_cacheHandle=null;//缓存操作句柄
    
    /**
     * 初始化缓存
     * @param string $mode  缓存操作模式
     */
    private static function _initHandle($mode='file'){
        $mode=trim($mode);
        if(!isset(self::$_cacheHandle[$mode])){
            require_once AHA_ROOT . '/__core/_libs/cacheDriver/CacheDriver.php';
            switch ($mode) {
                case 'file':
                    require_once AHA_ROOT . '/__core/_libs/cacheDriver/CacheFile.php';
                    self::$_cacheHandle[$mode]=new CacheFile();
                    break;
                case 'memcache':
                    require_once AHA_ROOT . '/__core/_libs/cacheDriver/CacheMemcache.php';
                    self::$_cacheHandle[$mode]=new CacheMemcache();
                    break;
                case 'memcached':
                    require_once AHA_ROOT . '/__core/_libs/cacheDriver/CacheMemcached.php';
                    self::$_cacheHandle[$mode]=new CacheMemcached();
                    break;
                case 'redis':
                    require_once AHA_ROOT . '/__core/_libs/cacheDriver/CacheRedis.php';
                    self::$_cacheHandle[$mode]=new CacheRedis();
                    break;
            }
            if(!self::$_cacheHandle[$mode]){
                Common::output('初始化缓存层失败：'.$mode);
            }
        }            
    }
    
    /**
     * 获取一个缓存
     * @param string $mode      缓存操作模式
     * @param string $prefix    缓存key前缀
     * @param string $key       缓存key
     * @param array $optionArr  扩展附加信息
     * @return mixed
     */
    public static function get($mode,$prefix,$key,$optionArr=array()){
        self::_initHandle($mode);
        return self::$_cacheHandle[$mode]->get($prefix,$key,$optionArr);
    }
    
    /**
     * 设置一个缓存
     * @param string $mode          缓存操作模式
     * @param string $prefix        缓存key前缀
     * @param string $key           缓存key
     * @param mixed $value          缓存保存的值
     * @param integer $cacheTime    缓存过期时间
     * @param array $optionArr      扩展附加信息
     * @return boolean
     */
    public static function set($mode,$prefix,$key,$value,$cacheTime,$optionArr=array()){
        self::_initHandle($mode);
        return self::$_cacheHandle[$mode]->set($prefix,$key,$value,$cacheTime,$optionArr);
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
    public static function increment($mode,$prefix,$key,$value) {
        self::_initHandle($mode);
        return method_exists(self::$_cacheHandle[$mode], 'increment')?self::$_cacheHandle[$mode]->increment($prefix,$key,$value):false;
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
    public static function decrement($mode,$prefix,$key,$value) {
        self::_initHandle($mode);
        return method_exists(self::$_cacheHandle[$mode], 'decrement')?self::$_cacheHandle[$mode]->decrement($prefix,$key,$value):false;
    }
    
    /**
     * 获取缓存信息
     * return array('size','expireTime','mtime','content');
     * 
     * @param string $mode      缓存操作模式
     * @param string $prefix    缓存key前缀
     * @param string $key       缓存key
     * @param array $optionArr  扩展附加信息
     * @return array or boolean
     */
    public static function stats($mode,$prefix,$key,$optionArr=array()){
        self::_initHandle($mode);
        return self::$_cacheHandle[$mode]->stats($prefix,$key,$optionArr);
    }
    
    /**
     * 获取所有的缓存信息
     * 
     * @param string $mode      缓存操作模式
     * @return array or boolean
     */
    public static function getStats($mode) {
        return method_exists(self::$_cacheHandle[$mode], 'getStats')?self::$_cacheHandle[$mode]->getStats():false;
    }

    /**
     * 删除一个缓存
     * @param string $mode          缓存操作模式
     * @param string $prefix        缓存key前缀
     * @param string $key           缓存key
     * @param array $optionArr      扩展附加信息
     * @return boolean
     */
    public static function del($mode,$prefix,$key,$optionArr=array()){
        self::_initHandle($mode);
        return self::$_cacheHandle[$mode]->del($prefix,$key,$optionArr);
    }

    /**
     * 删除所有缓存
     * @param string $mode      缓存操作模式
     * @param string $prefix    缓存key前缀
     * @param array $optionArr  扩展附加信息
     * @return true
     */
    public function clean($mode,$prefix,$optionArr=array()){
        self::_initHandle($mode);
        return self::$_cacheHandle[$mode]->clean($prefix,$optionArr);
    }
    
}