<?php /* Smarty version Smarty-3.1.14, created on 2013-11-30 11:00:45
         compiled from "E:\document\myframe\demo\view\index.tpl" */ ?>
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
  'function' => 
  array (
  ),
  'cache_lifetime' => 3600,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_529954ad961eb8_84689364',
  'variables' => 
  array (
    'title' => 0,
    'APP_ACTION' => 0,
    'APP_VIEW' => 0,
    'result' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_529954ad961eb8_84689364')) {function content_529954ad961eb8_84689364($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <title><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
    </head>
    <body>
        <div>
            <p><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</p>
            <p>APP_ACTION:<?php echo $_smarty_tpl->tpl_vars['APP_ACTION']->value;?>
</p>
            <p>APP_VIEW:<?php echo $_smarty_tpl->tpl_vars['APP_VIEW']->value;?>
</p>
            result:
            <pre><?php echo var_dump($_smarty_tpl->tpl_vars['result']->value);?>
</pre>
        </div>
    </body>
</html>
<?php }} ?>