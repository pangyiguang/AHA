<?php
return array(
    'default'=>array(//默认执行的控制器与视图
        'action'=>'index',
        'view'=>'index'
    ),
    'debug'=>false,//调试模式
    'errorReportLevel'=>E_ALL,//错误级别
    'cache'=>false,//是否缓存
    'cacheTime'=>0,//缓存时间，秒
    'defaultTimezone'=>'PRC',
    'autoloadfile'=>array(//需要自动载入的文件
        
    ),
    'define'=>array(//键=>值,键名为定义的常量，统一转为大写
        
    ),
    'sessionConfig'=>array(//session信息配置
        'start'=>false,//默认关闭
        'lifeTime'=>3600,//默认一小时
    ),
    'dbconfig'=>array(//数据库配置信息
        'default'=>array(//默认连接
            'dsn'=>'mysql:host=myhost;dbname=mydb;port=3306',//数据源名称
            'username'=>'',//连接的用户
            'password'=>'',//连接时密码
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