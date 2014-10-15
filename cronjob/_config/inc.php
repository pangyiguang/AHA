<?php
return array(
    'appname'=>'好土气',//应用名称
    'constant'=>array(),//项目中要用到的常量
    'default'=>array(//默认执行的控制器与视图
        'action'=>'',
        'view'=>''
    ),
    'debug'=>true,//调试模式
    'errorReportLevel'=>E_ALL,//错误级别
    'defaultTimezone'=>'PRC',
    'autoloadfile'=>array(//需要自动载入的文件
        APP_ROOT.'/common/functions.php',
    ),
    'define'=>array(//键=>值,键名为定义的常量，统一转为大写
        
    ),
    'dbconfig'=>array(//数据库配置信息
//        'default'=>array(//默认连接
//            'dsn'=>'mysql:host=localhost;dbname=qgaUeiypmGvJwBAkkNkQ;port=3306',//数据源名称
//            'username'=>'root',//连接的用户
//            'password'=>'123456',//连接时密码
//            'initsql'=>'SET NAMES utf8',//连接字符集
//            'persistent'=>false //是否长连接
//        ),
//         'default'=>array(//默认连接
//            'dsn'=>'mysql:host='.getenv('HTTP_BAE_ENV_ADDR_SQL_IP').';dbname=qgaUeiypmGvJwBAkkNkQ;port='.getenv('HTTP_BAE_ENV_ADDR_SQL_PORT'),//数据源名称
//            'username'=>getenv('HTTP_BAE_ENV_AK'),//连接的用户
//            'password'=>getenv('HTTP_BAE_ENV_SK'),//连接时密码
//            'initsql'=>'SET NAMES utf8',//连接字符集
//            'persistent'=>false //是否长连接
//        ),
//        'default'=>array(//默认连接
//            'dsn'=>'mysql:host=sqld.duapp.com;dbname=qgaUeiypmGvJwBAkkNkQ;port=4050',//数据源名称
//            'username'=>'l62SO5cqQw2YuFUSlaNtzFZi',//连接的用户
//            'password'=>'WSkfmqnkG6zKrfnIO0CFuqCROrctRLyg',//连接时密码
//            'initsql'=>'SET NAMES utf8',//连接字符集
//            'persistent'=>false //是否长连接
//        )
        'default'=>array(//默认连接
            'dsn'=>'mysql:host=127.0.0.1;dbname=haotuqi;port=3306',//数据源名称
            'username'=>'root',//连接的用户
            'password'=>'p1987y5g12',//连接时密码
            'initsql'=>'SET NAMES utf8',//连接字符集
            'persistent'=>false //是否长连接
        ),
    ),
    'memcache'=>array(//memcache配置信息
        'default'=>array(//默认连接
            'host'=>'127.0.0.1',//主机地址
            'port'=>'11211',//连接端口
            'timeout'=>'3',//超时时间
        ),
    ),
    'memcached'=>array(//memcached配置信息
        'connectTimeout'=>1000,//连接超时时间,毫秒
        'prefixKey'=>'',//key前缀
        'host'=>array(//请自动排除重复的服务器,array('127.0.0.1',11211,0),array('127.0.0.1',11261,5)
            array('127.0.0.1',11211,0),//主机地址,连接端口,权重
        ),
    ),
    'redis'=>array(//memcache配置信息
        'default'=>array(//默认连接
            'host'=>'127.0.0.1',//主机地址
            'port'=>'6379',//连接端口
            'username'=>'',//用户名
            'password'=>'',//密码
            'timeout'=>'3',//超时时间
        ),
    ),
    'sphinx'=>array(
        'host'=>'127.0.0.1',
        'port'=>9312,
        'connectTimeout'=>1,//单位：秒
        'queryTime'=>2000   //查询最大时间,毫秒
    ),
);