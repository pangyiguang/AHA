<?php
defined('AHA_ROOT') OR die('Unauthorized access!');

/**
 * 模板处理类，主要是逻辑与视图分离，模板使用php语言，没有专用的模板语法
 * 处理灵活方便
 * 例子
 *  逻辑层：
 *      $data['result']=DB::select('select * from feed_back where f_type=2 and f_open=1 limit 15', array(), 'all');
 *      MyView::display('contact', $data, TRUE, array('pyg'=>'pangyiguang'));
 * 视图层（模板）：
 *      <h5><?php echo $result[0]['f_subject'];?></h5>
 * @author pangyiguang
 */
class MyView {

    private static $_viewDir;
    private static $_isZipHtml = false;
    private static $_replace = array();

    /**
     * 初始化配置信息
     * @param boolean $isZipHtml   是否压缩html内容
     * @param array   $replace     需要替换的内容，键值对应
     */
    private static function _initConfig($isZipHtml, $replace) {
        if (!defined('VIEW_ROOT')) {
            Common::output('没有定义模板目录路径：VIEW_ROOT');
        }
        if (!VIEW_ROOT || !file_exists(VIEW_ROOT)) {
            Common::output('定义的模板目录路径不可用（存在）：' . VIEW_ROOT);
        }
        self::$_viewDir = VIEW_ROOT;
        self::$_isZipHtml = $isZipHtml;
        if ($replace && is_array($replace)) {
            self::$_replace = $replace;
        }
    }

    /**
     * 处理变量并显示模板内容，调试模式情况下，末尾输出当前生成时间
     * @param string $viewFile  模板名称，必须是php格式文件
     * @param array  $data      用于模板的变量集，键值模式
     * @param boolean $isZipHtml    是否压缩html内容
     * @param boolean $return       是否返回html内容
     * @param array $replace        需要替换的内容，键值对应
     */
    public static function display($viewFile, $data = array(), $isZipHtml = false, $replace = array(), $return = false) {
        self::_initConfig($isZipHtml, $replace);
        ob_start();
        if (is_array($data) && $data) {
            extract($data, EXTR_OVERWRITE);
        }
        include self::_getViewFile($viewFile);

        //获取缓冲区的内容（是否进行压缩），并返回处理后的内容
        $html = ob_get_contents();
        ob_end_clean();
        self::_replaceHtml($html);
        self::_ZipHtml($html);
        $html = $html . (AHA::getConfig('debug') ? '<!--' . date('Y-m-d H:i:s') . '-->' : '');
        if ($return) {
            return $html;
        }
        echo $html;
    }

    /**
     * 获取模板路径
     * @param string $viewFile  模板名称，必须是php格式文件
     * @return string
     */
    private static function _getViewFile($viewFile) {
        $viewFile = realpath(self::$_viewDir . DIRECTORY_SEPARATOR . $viewFile . '.php');
        if ($viewFile && file_exists($viewFile)) {
            return $viewFile;
        }
        Common::output('找不到视图（模板）文件：' . $viewFile);
    }

    /**
     * 替换html内容
     * @param string $html  html内容
     */
    private static function _replaceHtml(&$html) {
        if (self::$_replace) {
            $html = str_replace(array_keys(self::$_replace), array_values(self::$_replace), $html);
        }
    }

    /**
     * 压缩html内容
     * @param string $html  html内容
     */
    private static function _ZipHtml(&$html) {
        if (self::$_isZipHtml && $html) {
            $pattern = array(
                "/> *([^ ]*) *</", //去掉注释标记 
                "/[\s]+/",
                "/<!--[^!]*-->/",
//                "/\" /",
//                "/ \"/",
                "'/\*[^*]*\*/'"
            );
            $replace = array(
                '>\\1<',
                ' ',
                '',
//                '"',
//                '"',
                ''
            );
            $html = str_replace("\r\n", '', $html); //清除回车换行
            $html = str_replace("\n", '', $html); //清除换行符 
            $html = str_replace("\t", '', $html); //清除制表符 
            $html = preg_replace($pattern, $replace, $html);
        }
    }

}
