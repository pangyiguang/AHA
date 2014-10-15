<?php
defined('AHA_ROOT') OR die('Unauthorized access!');
/**
 * 文件缓存
 * @author pangyiguang
 */
class CacheFile implements CacheDriver {
    
    private $_fileRoot=null;
    private $_filePath=null;
    private $_cacheTime=0;
    private $_prefix='_aha_';
            
    function __construct($initArr = array()) {
        if(isset($initArr['fileRoot'])){
            $this->_fileRoot=realpath($initArr['fileRoot']);
        }else{
            $this->_fileRoot=  APP_ROOT .DIRECTORY_SEPARATOR. 'data'.DIRECTORY_SEPARATOR.'FileCache';
        }
        if(isset($initArr['cacheTime'])){
             $this->_cacheTime=(int)$initArr['cacheTime'];
        }else{
            $this->_cacheTime=  AHA::getConfig('cacheTime');
        }
    }
    
    function get($prefix,$key, $optionArr = array()) {
        $content=  $this->_getContent($prefix, $key);
        if(!$content){
            return false;
        }
        $expireTime=isset($content['expireTime'])?(int)$content['expireTime']:0;
        if(time()>$expireTime){
            unlink($this->_filePath);
            return false;
        }
        return isset($content['content'])?$content['content']:'';
    }
    
    function set($prefix,$key, $value, $cacheTime, $optionArr = array()) {
        $this->_cacheTime=(int)$cacheTime;
        $this->_prefix=trim($prefix);
        $this->_getFilePath($key, $this->_prefix);
        $dir=  dirname($this->_filePath);
        if(!file_exists($dir)){
            mkdir($dir, 0777, true) or Common::output('创建缓存目录失败：'.$dir);
        }
        $this->_isWriteAble();
        if(file_put_contents($this->_filePath,  serialize(array('expireTime'=>time()+$this->_cacheTime,'content'=>$value)))){
            return true;
        }
        return false;
    }
    
    function stats($prefix,$key, $optionArr = array()) {
        $content=  $this->_getContent($prefix, $key);
        if(!$content){
            return false;
        }
        return array_merge($content, array('size'=>  Common::SizeConvert(filesize($this->_filePath)),'mtime'=>  filemtime($this->_filePath)));
    }
    
    function del($prefix,$key, $optionArr = array()) {
        $this->_prefix=trim($prefix);
        $this->_getFilePath($key, $this->_prefix);
        $this->_isWriteAble();
        if(unlink($this->_filePath)){
            return true;
        }
        return false;
    }
    
    function clean($prefix,$optionArr = array()) {
        $this->_delClean($dir,$prefix);
        return true;
    }
    
    private function _delClean($dir,$prefix='') {
        foreach (glob($dir . DIRECTORY_SEPARATOR .$prefix. '*') as $file) {
            if (is_dir($file)) {
                $this->_delClean($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dir);
    }

    private function _getContent($prefix,$key){
        $this->_prefix=trim($prefix);
        $this->_getFilePath($key, $this->_prefix);
        if(!file_exists($this->_filePath)){
            return false;
        }
        $this->_isReadAble();
        $content = file_get_contents($this->_filePath);
        if(!$content){
            return false;
        }
        $content=@unserialize($content);
        if(!$content){
            return false;
        }
        return $content;
    }

    private function _getFilePath($key,$prefix=''){
        $this->_filePath = $this->_fileRoot.DIRECTORY_SEPARATOR.$prefix.DIRECTORY_SEPARATOR.md5($key);
    }
    
    private function _isWriteAble(){
        if(file_exists($this->_filePath) && !is_writable($this->_filePath)){
            Common::output('文件不可写：'.$this->_filePath);
        }
    }
    
    private function _isReadAble(){
        if(!is_readable($this->_filePath)){
            Common::output('文件不可读：'.$this->_filePath);
        }
    }
    
}
