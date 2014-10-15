<?php

/**
 * Description of Http
 *
 * @author pangyiguang
 */
class Http {

    /**
     * curl的http请求封装,单线程
     * 返回结果是数组
     * array(
     *      'code'=>(int)请求结果状态码,
     *      'header'=>(string)返回头域信息,
     *      'body'=>(string)返回的实体内容,
     * );
     * 
     * @param string $url               请求url路径
     * @param string $method            HTTP REQUEST方法，包括PUT、POST、GET、OPTIONS、DELETE、HEAD
     * @param array $headers            请求需要的特殊HTTP HEADERS
     * @param mixed $body               需要POST发送的数据
     * @param resource $file_handle     文件资源句柄
     * @param integer $timeout          超时时间，秒
     * @param is200     bool            是否状态响应为200才返回内容,等于false时，程序自动抓取跳转后的内容
     * @return array
     */
    public static function curl($url,$method='GET',$headers = array(), $body = NULL, $file_handle = NULL, $timeout = 5,$is200=true) {
        $method=$method?strtoupper($method):'GET';
        $ch = curl_init($url);
        $return = array('code' => 200, 'header' => '', 'body' => '');
        $_headers = array();
        if (isset($headers) && $headers && is_array($headers)) {
            foreach ($headers as $k => $v) {
                array_push($_headers, "{$k}: {$v}");
            }
        }
        $length = 0;
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        if (isset($body) && $body) {
            if (is_resource($body)) {
                fseek($body, 0, SEEK_END);
                $length = ftell($body);
                fseek($body, 0);
                array_push($_headers, "Content-Length: {$length}");
                curl_setopt($ch, CURLOPT_INFILE, $body);
                curl_setopt($ch, CURLOPT_INFILESIZE, $length);
            } else {
                if (is_array($body)) {
                    $body = http_build_query($body);
                }
                $length = strlen($body);
                array_push($_headers, "Content-Length: {$length}");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }
        } else {
            array_push($_headers, "Content-Length: {$length}");
        }
        array_push($_headers, "Date: {$date}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $_headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        //是否忽略url重定向后的结果
        if(!$is200){
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1); 
        }else{
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        }
        if ($method == 'PUT' || $method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
        } else {
            curl_setopt($ch, CURLOPT_POST, 0);
        }
        if ($method == 'GET' && is_resource($file_handle)) {
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FILE, $file_handle);
        }
        //请求方法为HEAD时，不返回实体内容
        if ($method == 'HEAD') {
            curl_setopt($ch, CURLOPT_NOBODY, true);
        }
        $response = curl_exec($ch);
        //获取http状态码
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $return['code'] = $http_code;
        if ($is200 && $http_code !== 200) {
            return $return;
        }
        if ($response) {
            //返回信息各就各位
            list($return['header'], $return['body']) = explode("\r\n\r\n", $response, 2);
        }
        unset($_headers, $body, $response);
        return $return;
    }
    
    /**
     * 多线程处理curl请求，get模式
     * 返回的数组，对应于url请求的请求结果
     * @param array $urlArr         请求的url数组集
     * @param integer $oneLimit     超时时间，默认5秒
     * @return boolean || array
     */
    public static function curlM($urlArr,$oneLimit=5){
        if(!$urlArr){
            return false;
        }
        if(!is_array($urlArr)){
            return false;
        }

        //初始化一个批处理句柄
        $cm=  curl_multi_init();
        $curls=array();

        //每个单独的curl句柄的连接参数
        $setoption=array(
            CURLOPT_HEADER=>false,
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_TIMEOUT=>(int)$oneLimit,
        );

        //向$cm批处理会话中添加单独的curl句柄
        foreach ($urlArr as $key => $value) {
            $curls[$key]=  curl_init($value);
            curl_setopt_array($curls[$key], $setoption);
            curl_multi_add_handle($cm, $curls[$key]);
        }

        //处理在栈中的每一个句柄，直到处理完毕
        $still=0;
        do {
            curl_multi_exec ($cm, $still);
        } while ($still>0);

        $result=array();

        foreach ($urlArr as $key => $value) {
            //连接到一个具体的句柄，返回对应的文本
            $result[$key]=  curl_multi_getcontent($curls[$key]);
            //移除curl批处理句柄资源中的某个句柄资源
            curl_multi_remove_handle($cm, $curls[$key]);
            //关闭子进程的资源
            curl_close($curls[$key]);
        }
        //关闭批处理句柄
        curl_multi_close($cm); 
        return $result;
    }

    /**
     * 发送http状态
     * @param integer $code http状态码
     */
    public static function sendHttpStatus($code) {
        $_status = array(
            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily ', // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',
            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'
        );
        if (isset($_status[$code])) {
            header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
            // 确保FastCGI模式下正常
            header('Status:' . $code . ' ' . $_status[$code]);
        }
    }

    /**
     * 根据类型获取文件扩展名
     * @param string $contentType 类型
     * @return string
     */
    public static function getExtFromType($contentType) {
        $return = '';
        $contentType = strtolower($contentType);
        switch ($contentType) {
            case 'application/pdf':
                $return = 'pdf';
                break;
            case 'application/json':
                $return = 'json';
                break;
            case 'application/xml':
                $return = 'xml';
                break;
            case 'application/octet-stream':
                $return = 'exe';
                break;
            case 'application/zip':
                $return = 'zip';
                break;
            case 'application/msword':
                $return = 'doc';
                break;
            case 'application/vnd.ms-excel':
                $return = 'xls';
                break;
            case 'application/vnd.ms-powerpoint':
                $return = 'ppt';
                break;
            case 'image/gif':
                $return = 'gif';
                break;
            case 'image/png':
                $return = 'png';
                break;
            case 'image/jpg':
            case 'image/jpeg':
                $return = 'jpg';
                break;
            case 'audio/mpeg':
                $return = 'mp3';
                break;
            case 'audio/x-wav':
                $return = 'wav';
                break;
            case 'video/mpeg':
                $return = 'mpeg';
                break;
            case 'video/quicktime':
                $return = 'mov';
                break;
            case 'video/x-msvideo':
                $return = 'avi';
                break;
            case 'application/force-download':
            default: $return = 'txt';
        }
        return $return;
    }

}
