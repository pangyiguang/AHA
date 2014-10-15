<?php
/**
 * 任务管理器配置文件
 * 
 * Y-m-d H:i        ：年 月 日 时 分
 * N H:i            ：星期(1 - 7|周一 - 周日) 时 分
 * 
 * 2013-12-25 19:49 : 固定时间，只执行一次
 * *-12-25 20:00    : 每年的某月某日 某小时某分
 * 2013-12-25 *:49  : 某天的每个小时的49分都执行一次
 * *-*-* 20:00      : 每天晚上8点0分执行
 * *-*-* *:*        ：每分钟都在执行
 * 2 20:01          ：每周二的20:01时间都执行一次
 * 
 * * 号表示当前位置的任何时间。以此类推....
 * 
 * 格式：
 * array(
 *      key=>value,
 * );
 * 
 * 说明：
 * key是定义的执行时间，value是执行的文件，可以是数组或者字符串，当同一时间有多个任务执行时，为了避免key的覆盖请用一维数组模式。
 * 
 */
return array(
    '*-*-* *:20'=>array(
//        'collect_yiyao.php',
        ),
    '*-*-* *:5'=>array(
        'gaoxiaoo_publish.php'
    ),
    '*-*-* *:10'=>array(
        'gaoxiaoo_publish.php'
    ),
    '*-*-* *:15'=>array(
        'gaoxiaoo_publish.php'
    ),
    '*-*-* *:25'=>array(
        'gaoxiaoo_publish.php'
    ),
    '*-*-* *:35'=>array(
        'gaoxiaoo_publish.php'
    ),
    '*-*-* *:45'=>array(
        'gaoxiaoo_publish.php'
    ),
    '*-*-* *:55'=>array(
        'gaoxiaoo_publish.php'
    ),
);