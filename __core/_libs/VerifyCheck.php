<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author pangyiguang
 */
class VerifyCheck {

    /**
     * 判断是否是通过手机访问
     * @return boolean
     */
    static function isMobile() {
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        //判断手机发送的客户端标志
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-',
                'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront',
                'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'opera mobi', 'openwave', 'nexusone', 'cldc', 'midp',
                'wap', 'mobile');
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", $userAgent) && strpos($userAgent, 'ipad') == 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * 验证是否手机号码
     * @param string $str 手机号码
     * @return bool 
     */
    static function NOIsMobile($str) {
        return preg_match('@^(13[0-9]|147|15[0-3|5-9]|18[0|2|5-9])\d{8}$@', $str);
    }

    /**
     * 验证是否是移动的手机号码
     * @param string $str 手机号码
     * @return bool 
     */
    static function NOIsChinaMobile($str) {
        return preg_match('@^(13[4-9]|147|15[0-3|7-9]|18[1|2|7-9])\d{8}$@', $str);
    }

    /**
     * 验证是否是联通的手机号码
     * @param string $str 手机号码
     * @return bool 
     */
    static function NOIsUnicomMobile($str) {
        return preg_match('@^(13[0-2]|15[5|6]|18[5|6])\d{8}$@', $str);
    }

    /**
     * 验证是否是电信的手机号码
     * @param string $str 手机号码
     * @return bool 
     */
    static function NOIsTelecomMobile($str) {
        return preg_match('@^(133|153|18[0|9])\d{8}$@', $str);
    }

    /**
     * 判断是否CMWAP用户
     * @return bool 
     */
    static function isCMWAP() {
        if (filter_has_var(INPUT_SERVER, 'HTTP_X_UP_BEAR_TYPE') && strcasecmp(filter_input(INPUT_SERVER, 'HTTP_X_UP_BEAR_TYPE'), 'GPRS/EDGE')) {
            return true;
        }
        return false;
    }

    /**
     * 判断是否为有效邮件地址
     * @param string $email 待检测的邮件地址
     * @return bool
     */
    static function isEmail($email) {
        if (!preg_match('@^[^@]+@[^\.@]+\.[^@]+$@i', $email)) {
            return false;
        }
        return true;
    }

    /**
     * 判断是否为有效网址
     * @static
     * @param string $weburl 待检测的网址
     * @return bool 
     */
	function isHttpUrl($weburl) {
		if (!preg_match('@^http://[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*$@i', $weburl)) {
			return false;
		} 
		return true;
	}
}
