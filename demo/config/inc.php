<?php
return array(
    'default'=>array(//默认执行的控制器与视图
        'action'=>'index',
        'view'=>'index'
    ),
    'debug'=>true,//调试模式
    'errorReportLevel'=>E_ALL,//错误级别
    'cache'=>true,//是否缓存
    'cacheTime'=>3600,//缓存时间
    'defaultTimezone'=>'PRC',
    'autoloadfile'=>array(//需要自动载入的文件
        APP_ROOT.'/common/functions.php'
    ),
    'define'=>array(//键=>值,键名为定义的常量，统一转为大写
        
    ),
    'sessionConfig'=>array(//session信息配置
        'start'=>false,//默认关闭
        'lifeTime'=>3600,//默认一小时
    ),
    'dbconfig'=>array(//数据库配置信息
        'default'=>array(//默认连接
            'dsn'=>'mysql:host=localhost;dbname=test;port=3306',//数据源名称
            'username'=>'root',//连接的用户
            'password'=>'123456',//连接时密码
            'initsql'=>'SET NAMES utf8',//连接字符集
            'persistent'=>false //是否长连接
        ),
        'ly'=>array(//连接2
            'dsn'=>'mysql:host=localhost;dbname=test2;port=3306',//数据源名称
            'username'=>'root',//连接的用户
            'password'=>'123456',//连接时密码
            'initsql'=>'set names utf8',//连接字符集
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
);