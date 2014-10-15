<?php /*%%SmartyHeaderCode:3702529954ad91f824-74700278%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a2a6b5785e4a617c24e2be9c329ac84433ba54a3' => 
    array (
      0 => 'E:\\document\\myframe\\demo\\view\\index.tpl',
      1 => 1385780444,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3702529954ad91f824-74700278',
  'cache_lifetime' => 3600,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_529954ddd73f34_94476901',
  'variables' => 
  array (
    'title' => 0,
    'APP_ACTION' => 0,
    'APP_VIEW' => 0,
    'result' => 0,
  ),
  'has_nocache_code' => false,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_529954ddd73f34_94476901')) {function content_529954ddd73f34_94476901($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <title>一个调用smarty模板的例子</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
    </head>
    <body>
        <div>
            <p>一个调用smarty模板的例子</p>
            <p>APP_ACTION:index</p>
            <p>APP_VIEW:index</p>
            result:
            <pre>array(4) {
  [0]=>
  string(6) "liming"
  [1]=>
  string(12) " who are you"
  [2]=>
  string(18) "how can i help you"
  [3]=>
  string(8) "yes,i do"
}
</pre>
        </div>
    </body>
</html>
<?php }} ?>