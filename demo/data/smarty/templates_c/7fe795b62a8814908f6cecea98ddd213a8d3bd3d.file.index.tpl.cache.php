<?php /* Smarty version Smarty-3.1.14, created on 2014-10-14 16:54:11
         compiled from "D:\documentRoot\AHA\demo\view\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2010543ce4b3e0b577-02776607%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7fe795b62a8814908f6cecea98ddd213a8d3bd3d' => 
    array (
      0 => 'D:\\documentRoot\\AHA\\demo\\view\\index.tpl',
      1 => 1404046454,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2010543ce4b3e0b577-02776607',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_543ce4b40152a7_81904121',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_543ce4b40152a7_81904121')) {function content_543ce4b40152a7_81904121($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <title>{$title}</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
    </head>
    <body>
        <div>
            <p>{$title}</p>
            <p>APP_ACTION:{$APP_ACTION}</p>
            <p>APP_VIEW:{$APP_VIEW}</p>
            result:
            <pre>{$result|var_dump}</pre>
        </div>
    </body>
</html>
<?php }} ?>