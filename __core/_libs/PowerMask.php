<?php
/**
 * PHP二进制权限操作类
 */
class PowerMask{
    
    //初始化权限码
    private $power=0;
    
    function __construct($power) {
        if ($this->isPowerCode($power)) {
            $this->power = $power;
        }
    }
    
    /**
     * 返回当前的权限码
     * @return integer
     */
    function getPowerCode(){
        return $this->power;
    }
    
    /**
     * 判断是否有权限，返回值为0时标识没有权限
     * @param integer $code  检测的权限代码
     * @return integer|boolren 真正拥有的权限值|权限码不合法时返回false
     */
    function isPower($code){
        if (!$this->isPowerCode($code)) {
            return FALSE;
        }
        return $this->power & $code;
    }
    
    /**
     * 添加权限
     * @param integer $code 要添加的权限码
     * @return boolren 权限码不合法时返回false
     */
    function addPower($code){
        if (!$this->isPowerCode($code)) {
            return FALSE;
        }
        $this->power = $this->power | $code;
        return TRUE;
    }
    
    /**
     * 删除相应的权限
     * @param integer $code 要删除的权限码
     * @return boolren 权限码不合法时返回false
     */
    function delPower($code){
        if (!$this->isPowerCode($code)) {
            return FALSE;
        }
        $this->power = $this->power ^ $code;
        return TRUE;
    }
    
    /**
     * 分析权限码所包含的权限
     * @param integer $code 权限码
     * @return array|boolren 权限数组|权限码不合法时返回false
     */
    function parsePower($code){
        if (!$this->isPowerCode($code)) {
            return FALSE;
        }
        $powerlist=array();
        $code=decbin($code);
        $num=strlen($code);
        for($i=0;$i<$num;$i++){
            if($code{$i}){
                $powerlist[]=pow (2,$num-$i-1);
            }
        }
        return $powerlist;
    }
    
    /**
     * 检测权限码的合法性
     * @param type $code
     * @return boolren 权限码不合法时返回false
     */
    function isPowerCode($code){
        return !is_numeric($code)||$code>PHP_INT_MAX?FALSE:TRUE;
    }
}

//$powerhandle=new power(89);
//var_dump($powerhandle->getPowerCode());
//$powerhandle->addPower(2);
//var_dump($powerhandle->getPowerCode());
//$powerhandle->delPower(8);
//var_dump($powerhandle->getPowerCode());
//var_dump($powerhandle->isPower(9),$powerhandle->parsePower($powerhandle->getPowerCode()));