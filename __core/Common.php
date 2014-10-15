<?php

/**
 * 核心的公共代码库
 *
 * @author pangyiguang
 */
class Common {

    /**
     * 错误输出
     * @param string $message     错误信息
     * @param string $color       显示颜色
     */
    public static function output($message = '', $color = 'red') {
        $str = '';
        if (AHA::getConfig('debug')) {
            $e = error_get_last();
            $str = '<div style="width:98%;border:1px solid #303030;margin:2px;padding:5px;background:#E1E1E1;color:#000">';
            if ($message) {
                $str.='<b>MESSAGE:</b><span style="color:' . $color . '"><pre>' . htmlentities($message) . '</pre></span><br />';
            }
            if ($e) {
                $str.='<b>ERROR_TYPE:</b><span style="color:' . $color . '">' . $e['type'] . '</span><br />';
                $str.='<b>PHP_MESSAGE:</b><span style="color:' . $color . '">' . $e['message'] . '</span><br />';
                $str.='<b>PHP_FILE:</b><span style="color:' . $color . '">' . $e['file'] . '</span><br />';
                $str.='<b>PHP_LINE:</b><span style="color:' . $color . '">' . $e['line'] . '</span><br />';
            }
            $str.=self::getBug();
            $str.='</div>';
        }else{
            if(defined('PAGE_ERROR')){
                Common::location(PAGE_ERROR);
            }else{
                $str=<<<STR
<!DOCTYPE html>
<html>
    <head>
        <title>ERROR PAGE</title>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div>I'm sorry, the program errors! </div>
    </body>
</html>
STR;
            }
        }
        exit($str);
    }

    /**
     * 调试的输出，用于优化查询、排查错误
     */
    public static function bug($title = '', $array_put = array()) {
        if ($title && AHA::getConfig('debug')) {
            $count = count($array_put);
            $str = '<style type="text/css">.b_box{width:98%;border:1px solid #303030;margin:2px;padding:5px;background:#E1E1E1;color:#000}.key{display:inline-block;width:150px;color:red}.value{font-size:12px;color:#2B4E02}</style>';
            $str.='<div class="b_box">';
            $str.='<b>' . $title . ':</b>' . $count . '<br />';
            $str.='<b>PHP占用内存:</b>' . Common::SizeConvert(memory_get_usage()) . '<br />';
            if ($count) {
                foreach ($array_put as $key => $value) {
                    $str.='<b class="key">item_' . $key . ':</b>[' . $value['key'] . ']<span class="value">' . $value['value'] . '</span><br />';
                }
            }
            $str.=self::getBug();
            $str.='</div>';
            echo $str;
        }
    }

    public static function getBug() {
        $trace = '<p style="padding:2px;"><b>调试跟踪</b><br />';
        foreach (debug_backtrace() as $k => $v) {
            if($v['args']){
                foreach ($v['args'] as $key => $value) {
                    if(is_array($value)){
                        $v['args'][$key]=  var_export($value, 1);
                    }
                }
            }
            $trace .= '#' . ($k) . ' ' . (isset($v['file'])?$v['file']:'未知文件') . '(' . (isset($v['line'])?$v['line']:'未知行') . '): ' . (isset($v['class']) ? $v['class'] : '') .(isset($v['type']) ? $v['type'] : ' '). $v['function'] . '(' . ($v['args']?implode(', ', $v['args']):'') . ')<br/>' . "\n";
        }
        $trace.='</p>';
        return $trace;
    }


    /**
     * 容量转换
     * @param int $filesize 大小bytes
     * @return string 
     */
    public static function SizeConvert($filesize) {
        if ($filesize >= 1073741824) {
            $filesize = round($filesize / 1073741824, 2) . 'G';
        } elseif ($filesize >= 1048576) {
            $filesize = round($filesize / 1048576, 2) . 'M';
        } elseif ($filesize >= 1024) {
            $filesize = round($filesize / 1024, 2) . 'k';
        } else {
            $filesize = $filesize . 'b';
        }
        return $filesize;
    }

    /**
     * 创建目录
     * @param string $dir   目录路径
     * @param boolean $dirIsFile    是否为文件
     * @return boolean 
     */
    public static function createDir($dir, $dirIsFile = false) {
        if ($dir && $dirIsFile) {
            $dir = dirname($dir);
        }
        if ($dir && mkdir($dir, 0777, true)) {
            return true;
        }
        return false;
    }

    /**
     * 判断数组是一维还是二维
     * @param array $array 需要判断的数组
     * @return int
     */
    public static function is_2dimension_array($array) {
        $dimension = 1;
        if ($array && is_array($array)) {
            foreach ($array as $value) {
                if (is_array($value)) {
                    $dimension = 2;
                }
                break;
            }
        }
        return $dimension;
    }

    /**
     * 用于自动加载类库文件
     * @param string $class_name 类名称
     * @return boolean
     */
    public static function loadClassFile($class_name) {
        if (class_exists($class_name, false)) {
            return true;
        }
        if (self::includeFile(CORE_ROOT . DIRECTORY_SEPARATOR . $class_name . '.php')) {
            return true;
        } elseif (self::includeFile(MODEL_ROOT . DIRECTORY_SEPARATOR . $class_name . '.php')) {
            return true;
        } elseif (self::includeFile(CORE_ROOT . DIRECTORY_SEPARATOR . '_libs' . DIRECTORY_SEPARATOR . $class_name . '.php')) {
            return true;
        }
        return false;
    }

    /**
     * 包含文件，检测是否存在
     * @param string $path  文件路径
     * @return boolean
     */
    public static function includeFile($path) {
        if ($path && file_exists($path)) {
            require $path;
            return true;
        }
        return false;
    }

    /**
     * 获取客户端的ip地址
     * @return string
     */
    public static function get_client_ip() {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = 'unknown';
        }
        return $ip;
    }

    /**
     * 是否来自ajax的异步请求
     * @return boolean
     */
    public static function isAjax() {
        if (!filter_has_var(INPUT_SERVER,'HTTP_X_REQUESTED_WITH') || strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) !== 'xmlhttprequest') {
            return false;
        }
        return true;
    }

    public static function showBox($url, $title, $message, $time = 5) {
        echo <<<SSSS
<!DOCTYPE html>
<html>
    <head>
        <title>$title</title>
        <meta charset="UTF-8">
        <meta http-equiv="refresh" content="$time;url=$url">
        <meta name="viewport" content="width=device-width">
        <style type="text/css">
            body{color: #333333;font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;font-size: 14px;line-height: 1.8em;width: 100%;text-align: center;padding-top: 20px}
            .container{width: 50%;height: 300px;margin: 0 auto;}
            .container h3{padding: 5px;margin: 0;width: 100%;height: 30px;font-size: 18px;background-color: #DFF0D8;color: #468847;border: 1px solid #CCCCCC;}
            .container p{width:100%;height:250px;background-color: #F5F5F5;border: 1px solid #CCCCCC;color: #333333;margin: 0 0 10px;padding: 5px;word-break: break-all;word-wrap: break-word;}
        </style>
    </head>
    <body>
        <div class="container">
            <h3>$title</h3>
            <p>$message<br />正在为你跳转... ...<br /> <a href="$url">点击这里就过去了</a></p>
        </div>
    </body>
</html>
SSSS;
        exit;
    }

    public static function location($url, $str = '', $t404 = false) {
        if ($t404) {
            header('HTTP/1.0 404 Not Found');
        }
        if ($str) {
            echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">alert("' . addcslashes($str, '"') . '");window.location.href="' . $url . '";</script><head><body></body></html>';
        } else {
            header('Location:' . $url);
        }
        exit;
    }

    public static function varDump(&$var, $type = 0) {
        if (!$type) {
            echo '<ul type="circle" style="border-left:1px solid #a0a0a0;padding-bottom:4px;padding-right:4px"><li>';
        }
        if (is_array($var)) {
            echo '[array][' . count($var) . ']';
            echo '<ul type="circle" style="border-left:1px solid #a0a0a0;padding-bottom:4px;padding-right:4px">';
            foreach ($var as $k => $v) {
                echo '<li>"' . $k . '"=>';
                self::varDump($v, 1);
            }
            echo '</ul>';
        } else {
            echo '[' . gettype($var) . '][' . $var . ']</li>';
        }
        if (!$type) {
            echo '</ul>';
        }
    }

    public static function to_full_index_str($str) {
        if (!$str) {
            return '';
        }
        $arr = explode(' ', $str);
        $nu = count($arr);
        $arr2 = array();
        for ($i = 0; $i < $nu; $i++) {
            $arr2[] = base64_encode(trim($arr[$i]));
        }
        return implode(' ', $arr2);
    }

    public static function split_str($source, $array = false) {
        $search = array(",", "/", "\\", ".", ";", ":", "\"", "!", "~", "`", "^", "(", ")", "?", "-", "\t", "\n", "'", "<", ">", "\r", "\r\n", "$", "&", "%", "#", "@", "+", "=", "{", "}", "[", "]", "：", "）", "（", "．", "。", "，", "！", "；", "“", "”", "‘", "’", "［", "］", "、", "—", "　", "《", "》", "－", "…", "【", "】",);
        $str = str_replace($search, ' ', mb_strtolower($source, "UTF-8"));
        $out = array();
        $j = -1;
        $builden = false; //英文拼接状态
        for ($i = 0; $i < mb_strlen($str, "UTF-8"); $i++) {
            $curtoken = mb_substr($str, $i, 1, "UTF-8");
            $isen = true;
            if ($curtoken == " ") {//如果是空格
                $isen = false;
                $builden = false;
            }
            if (preg_match('/^[\x{4e00}-\x{9fa5}]$/u', $curtoken)) {//中文
                $j++;
                $out[$j]["a"] = $curtoken;
                $out[$j]["b"] = "CN";
                $isen = false;
                $builden = false;
            }
            if ($isen) {//英文
                if (!$builden) {//如果不在拼接英文，就开新区
                    $j++;
                    $out[$j]["a"] = "";
                    $out[$j]["b"] = "EN";
                    $builden = true;
                }
                $out[$j]["a"].=$curtoken;
            }
        }

        $re = array();
        $j = 0;
        for ($i = 0; $i < count($out); $i++, $j++) {
            if (isset($out[$i + 1]["b"]) && $out[$i]["b"] == "CN" && $out[$i + 1]["b"] == "CN") {
                $re[$j] = $out[$i]["a"] . $out[$i + 1]["a"];
            } else {
                $re[$j] = $out[$i]["a"];
            }
        }
        $returnstr = "";
        if (!$array) {
            for ($i = 0; $i < count($re); $i++, $j++) {
                $returnstr.=($returnstr == "" ? "" : " ") . $re[$i];
            }
            return $returnstr;
        } else {
            return $re;
        }
    }

    public static function encodetag($source, $searchmode = false) {
        $ar = explode(" ", ($source)); //转换成数组
        for ($i = 0; $i < count($ar); $i++) {
            if ($ar[$i] != "") {
                $tmpstr = $ar[$i];
                $prefix = "";
                if ($searchmode) {
                    $prefix = mb_substr($tmpstr, 0, 1, "UTF-8");
                    if ($prefix != "+" && $prefix != "-") {
                        $prefix = "";
                    } else {
                        $tmpstr = mb_substr($tmpstr, 1, mb_strlen($tmpstr, "UTF-8"), "UTF-8");
                    }
                }
                $tmpstr = mb_strtolower($tmpstr, "UTF-8");
                $tmpstr = md5($tmpstr);
                $tmpstr = mb_substr($tmpstr, 8, 16);
                if ($searchmode) {
                    $tmpstr = '"' . $tmpstr . '"';
                }
                $ar[$i] = $prefix . $tmpstr;
            }
        }
        return implode(" ", $ar);
    }

    public static function quoteString($content) {
        if (get_magic_quotes_gpc()) {
            $content = stripslashes($content);
        }
        return $content;
    }

    public static function getSqlWhereStr($where = array()) {
        $pram = array();
        $sql = '';
        if ($where) {
            foreach ($where as $key => $value) {
                $sql.='`' . trim($key, '` ') . '`=? and ';
                $pram[] = $value;
            }
            $sql = rtrim($sql, ' and ');
        }
        return array($sql, $pram);
    }

    /**
     * @todo 词义化时间
     * @param int $time 时间邮戳
     * @return string
     */
    public static function wordTime($time) {
        $time = (int) substr($time, 0, 10);
        $int = time() - $time;
        $str = '';
        if ($int <= 2){
            $str = sprintf('刚刚', $int);
        }elseif ($int < 60){
            $str = sprintf('%d秒前', $int);
        }elseif ($int < 3600){
            $str = sprintf('%d分钟前', floor($int / 60));
        }elseif ($int < 86400){
            $str = sprintf('%d小时前', floor($int / 3600));
        }elseif ($int < 2592000){
            $str = sprintf('%d天前', floor($int / 86400));
        }else{
            $str = date('Y-m-d H:i:s', $time);
        }
        return $str;
    }

    
    public static function fileLog($filename,$string,$echo=false){
        $log='';
        if ($filename){
            if(!file_exists(dirname($filename))){
                mkdir(dirname($filename), 0777, true);
            }
            if(!file_exists($filename)){
                touch($filename);
            }
            if(is_writable($filename)){
                $string.="\n";
                if($echo){
                    echo $string;
                }
                file_put_contents($filename, $string, FILE_APPEND);
            }else{
                $log=$filename.' file is not writable'."\n";
            }
        }else{
            $log=$filename.' file is not legal'."\n";
        }
        if($echo){
            echo $log;
        }
        if($log){
            exit();
        }
    }
}
