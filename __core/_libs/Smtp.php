<?php
defined('AHA_ROOT') OR die('Unauthorized access!');
/**********************************使用说明start****************************************/

/*
$Server = 'mail.abc.com';      //SMTP 服务器
$Port = '25';      //SMTP服务器端口号 默认25
$Id = 'test@abc.com';  //服务器邮箱帐号
$Pw = 'abc6666';        //服务器邮箱密码
//发送内容
$Title = '测试邮件标题';        //邮件标题
$Content = '这是测试的邮件内容';        //邮件内容
$email = array('475901679@qq.com' => 'pangyiguang', 'avvvf@abc.com' => '琵琶语'); //接收者邮箱，数组，键为邮箱，值为显示名称
$from = array($MailId, '我是谁');//发送邮箱，数组，第一个值是邮箱，第二个是发送人名称
$smtp = new smtp($MailServer, $MailPort, true, true, $MailId, $MailPw);
if ($smtp->sendmail($email, $from, $Title, $Content)) {
    echo '邮件发送成功';            //返回结果
} else {
    echo '邮件发送失败';            //$succeed = 0;
}
*/

/**********************************使用说明end****************************************/



class aha_smtp {

    private $connection;
    private $recipients;
    private $headers;
    private $timeout = 5; //连接超时的时间
    private $errors = array();
    private $status = 1;
    private $body;
    private $from;
    private $host = 'localhost'; //SMTP 服务器的主机
    private $port = 25; //SMTP 服务器的端口
    private $helo; //发送HELO命令的名称
    private $auth = FALSE;
    private $user; //SMTP 服务器的用户名
    private $pass; //SMTP 服务器的登陆密码

    function __construct($params = array()) {
        if (!defined('CRLF')) {
            define('CRLF', "\r\n", TRUE);
        }
        foreach ($params as $key => $value) {
            $this->$key = $value;
        }

        $this->helo = $this->host;
        //  如果没有设置用户名则不验证        
        $this->auth = ('' == $this->user) ? FALSE : TRUE;
    }

    function connect($params = array()) {
        if (!isset($this->status)) {
            $obj = new aha_smtp($params);

            if ($obj->connect()) {
                $obj->status = 2;
            }
            return $obj;
        } else {

            $this->connection = fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
            socket_set_timeout($this->connection, 0, 250000);
            $greeting = $this->get_data();

            if (is_resource($this->connection)) {
                $this->status = 2;
                return $this->auth ? $this->ehlo() : $this->helo();
            } else {
                $this->errors[] = 'Failed to connect to server: ' . $errstr;
                return FALSE;
            }
        }
    }

    /**
     * 参数为数组
     * recipients      接收人的数组
     * from            发件人的地址，也将作为回复地址
     * headers         头部信息的数组
     * body            邮件的主体
     */
    function send($params = array()) {
        foreach ($params as $key => $value) {
            $this->set($key, $value);
        }
        if ($this->is_connected()) {
            //  服务器是否需要验证     
            if ($this->auth && !$this->auth()) {
                return FALSE;
            }
            $this->mail($this->from);
            if (is_array($this->recipients)) {
                foreach ($this->recipients as $value) {
                    $this->rcpt($value);
                }
            } else {
                $this->rcpt($this->recipients);
            }
            if (!$this->data()) {
                return FALSE;
            }
            $headers = str_replace(CRLF . '.', CRLF . '..', trim(implode(CRLF, $this->headers)));
            $body = str_replace(CRLF . '.', CRLF . '..', $this->body);
            if ($body[0] === '.') {
                $body = '.' . $body;
            }
            $this->send_data($headers);
            $this->send_data('');
            $this->send_data($body);
            $this->send_data('.');
            return (substr(trim($this->get_data()), 0, 3) === '250');
        } else {
            $this->errors[] = 'Not connected!';
            return FALSE;
        }
    }

    function helo() {
        if (is_resource($this->connection)
            AND $this->send_data('HELO ' . $this->helo)
            AND substr(trim($error = $this->get_data()), 0, 3) === '250') {
            return TRUE;
        } else {
            $this->errors[] = 'HELO command failed, output: ' . trim(substr(trim($error), 3));
            return FALSE;
        }
    }

    function ehlo() {
        if (is_resource($this->connection)
            AND $this->send_data('EHLO ' . $this->helo)
            AND substr(trim($error = $this->get_data()), 0, 3) === '250') {
            return TRUE;
        } else {
            $this->errors[] = 'EHLO command failed, output: ' . trim(substr(trim($error), 3));
            return FALSE;
        }
    }

    function auth() {
        if (is_resource($this->connection)
            AND $this->send_data('AUTH LOGIN')
            AND substr(trim($error = $this->get_data()), 0, 3) === '334'
            AND $this->send_data(base64_encode($this->user))            // Send username
            AND substr(trim($error = $this->get_data()), 0, 3) === '334'
            AND $this->send_data(base64_encode($this->pass))            // Send password
            AND substr(trim($error = $this->get_data()), 0, 3) === '235') {
            return TRUE;
        } else {
            $this->errors[] = 'AUTH command failed: ' . trim(substr(trim($error), 3));
            return FALSE;
        }
    }

    function mail($from) {
        if ($this->is_connected()
            AND $this->send_data('MAIL FROM:<' . $from . '>')
            AND substr(trim($this->get_data()), 0, 2) === '250') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function rcpt($to) {
        if ($this->is_connected()
            AND $this->send_data('RCPT TO:<' . $to . '>')
            AND substr(trim($error = $this->get_data()), 0, 2) === '25') {
            return TRUE;
        } else {
            $this->errors[] = trim(substr(trim($error), 3));
            return FALSE;
        }
    }

    function data() {
        if ($this->is_connected()
            AND $this->send_data('DATA')
            AND substr(trim($error = $this->get_data()), 0, 3) === '354') {
            return TRUE;
        } else {
            $this->errors[] = trim(substr(trim($error), 3));
            return FALSE;
        }
    }

    function is_connected() {
        return (is_resource($this->connection) AND ( $this->status === 2));
    }

    function send_data($data) {
        if (is_resource($this->connection)) {
            return fwrite($this->connection, $data . CRLF, strlen($data) + 2);
        } else {
            return FALSE;
        }
    }

    function &get_data() {
        $return = '';
        $line = '';
        if (is_resource($this->connection)) {
            while (strpos($return, CRLF) === FALSE OR substr($line, 3, 1) !== ' ') {
                $line = fgets($this->connection, 512);
                $return .= $line;
            }
            return $return;
        } else {
            return FALSE;
        }
    }

    function set($var, $value) {
        $this->$var = $value;
        return TRUE;
    }

}

class Smtp {

    private $debug;
    private $host;
    private $port;
    private $auth;
    private $user;
    private $pass;

    function smtp($host = '', $port = 25, $auth = false, $debug = false, $user = null, $pass = null) {
        $this->host = $host;
        $this->port = $port;
        $this->auth = $auth;
        $this->debug = $debug;
        $this->user = $user;
        $this->pass = $pass;
    }

    function sendmail($to, $from, $subject, $content) {
        if (!$to || !$from) {
            return FALSE;
        }
        $subject = '=?UTF-8?B?' . base64_encode($subject) . '==?=';
        $content = base64_encode($content);
        $i = 0;
        $to_s = array();
        foreach ($to as $key => $value) {
            if ($i === 0) {
                $headers[] = 'To:=?UTF-8?B?' . base64_encode($value) . "?= <{$key}>";
            } else {
                $headers[] = 'To:=?UTF-8?B?' . base64_encode($value) . "?= <{$key}>";
            }
            $to_s[] = $key;
            $i++;
        }

        $headers[] = 'From:=?UTF-8?B?' . base64_encode($from[1]) . "?= <{$from[0]}>";
        $headers[] = 'MIME-Version: Blueidea v1.0';
        $headers[] = 'X-Mailer: 9gongyu Mailer v1.0';
        $headers[] = 'Subject:' . $subject;
        $headers[] = 'Content-Type: text/html; charset=UTF-8; format=flowed';
        $headers[] = 'Content-Transfer-Encoding: base64';
        $headers[] = 'Content-Disposition: inline';
        //SMTP 服务器信息
        $params['host'] = $this->host;
        $params['port'] = $this->port;
        $params['user'] = $this->user;
        $params['pass'] = $this->pass;
        if (empty($params['host']) || empty($params['port'])) {
            // 如果没有设置主机和端口直接返回 false
            return false;
        } else {
            //  发送邮件
            $send_params['recipients'] = $to_s;
            $send_params['headers'] = $headers;
            $send_params['from'] = $from[0];
            $send_params['body'] = $content;
            $smtp = new aha_smtp($params);
            if ($smtp->connect() AND $smtp->send($send_params)) {
                return TRUE;
            } else {
                return FALSE;
            } // end if
        }
    }

}