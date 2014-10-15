<?php
/**
 * 控制器处理文件
 *
 * @author pangyiguang
 */
abstract class Controller {

    protected static $smarty = null; //smarty操作句柄
    protected static $http_method = 'get'; //当前请求的方法
    //几个涉及到用户输入参数数组
    public static $posts = array();
    public static $gets = array();
    public static $files = array();
    public static $cookies = array();
    public static $sessions = array();
    public static $servers = array();

    /**
     * 控制器的构造函数，会检测继承类是否定义了初始化函数_initialize，主要用来统一处理或者权限处理
     */
    public function __construct() {
        $this->_init_inputs();
        if (method_exists($this, '_initialize')) {
            $this->_initialize();
        }
        define('TIME_STMP', time());
        self::$http_method = strtolower(filter_input(INPUT_SERVER, 'REQUEST_METHOD'));
    }

    /**
     * 魔术方法，调用的方法不存在时，返回相应提示
     * @param string $name  方法名
     * @param array $arguments  方法的参数
     */
    public function __call($name, $arguments = array()) {
        Common::output('You call the object method "' . $name . '" is not exist,these arguments:<br/>' . var_export($arguments, 1));
    }

    /**
     * 魔术方法，调用的静态方法不存在时，返回相应提示
     * @param string $name  方法名
     * @param array $arguments  方法的参数
     */
    public static function __callStatic($name, $arguments = array()) {
        Common::output('You call the object static method "' . $name . '" is not exist,these arguments:<br/>' . var_export($arguments, 1));
    }

    /**
     * 获取smarty的操作句柄，以及预处理一些变量
     * @staticvar null $smarty  smarty操作句柄
     * @return resource
     */
    public static function getSmarty() {
        if (!self::$smarty) {
            require_once CORE_ROOT . '/_libs/others/smarty/Smarty.class.php';
            self::$smarty = new Smarty();
            self::$smarty->template_dir = VIEW_ROOT;
            self::$smarty->compile_dir = APP_ROOT . '/data/smarty/templates_c';
            self::$smarty->caching = AHA::getConfig('cache');
            self::$smarty->debugging = AHA::getConfig('debug');
            self::$smarty->cache_lifetime = AHA::getConfig('cacheTime');
            self::$smarty->cache_dir = APP_ROOT . '/data/smarty/cache';
            self::$smarty->left_delimiter = '<{'; //变量的左分隔符
            self::$smarty->right_delimiter = '}>'; //变量的右分隔符
        }
    }

    /**
     * 获取用户get参数
     * @param string $key       $_GET变量的key
     * @param mixed $default    取值不到时的默认值
     * @return mixed
     */
    public function input_get($key, $default = '') {
        if (filter_has_var(INPUT_GET, $key)) {
            return self::$gets[$key];
        }
        return $default;
    }

    /**
     * 获取用户post参数
     * @param string $key       $_POST变量的key
     * @param mixed $default    取值不到时的默认值
     * @return mixed
     */
    public function input_post($key, $default = '') {
        if (filter_has_var(INPUT_POST, $key)) {
            return self::$posts[$key];
        }
        return $default;
    }

    /**
     * 获取用户文件上传参数
     * @param string $key       $_FILES变量的key
     * @param mixed $default    取值不到时的默认值
     * @return mixed
     */
    public function input_file($key, $default = array()) {
        return isset(self::$files[$key]) ? self::$files[$key] : $default;
    }

    /**
     * 获取server参数
     * @param string $key       $_SERVER变量的key
     * @param mixed $default    取值不到时的默认值
     * @return mixed
     */
    public function input_server($key, $default = '') {
        if (filter_has_var(INPUT_SERVER, $key)) {
            return self::$servers[$key];
        }
        return $default;
    }

    /**
     * 获取COOKIE参数
     * @param string $key       $_COOKIE变量的key
     * @param mixed $default    取值不到时的默认值
     * @return mixed
     */
    public function input_cookie($key, $default = '') {
        if (filter_has_var(INPUT_COOKIE, $key)) {
            return self::$cookies[$key];
        }
        return $default;
    }

    /**
     * 获取$_SESSION参数
     * @param string $key       $_SESSION变量的key
     * @param mixed $default    取值不到时的默认值
     * @return mixed
     */
    public function input_session($key, $default = '') {
        return isset(self::$sessions[$key]) ? self::$sessions[$key] : $default;
    }

    /**
     * 获取表单参数值，post值优先
     * @param string $key       $_POST || $_GET 变量的key
     * @param mixed $default    取值不到时的默认值
     * @return mixed
     */
    public function input_form($key, $default = '') {
        if (filter_has_var(INPUT_POST, $key)) {
            return self::$posts[$key];
        }
        if (filter_has_var(INPUT_GET, $key)) {
            return self::$gets[$key];
        }
        return $default;
    }

    /**
     * 初始化几个用户参数变量
     */
    private function _init_inputs() {
        self::$posts = &$_POST;
        self::$gets = &$_GET;
        self::$files = &$_FILES;
        self::$cookies = &$_COOKIE;
        self::$sessions = &$_SESSION;
        self::$servers = &$_SERVER;
    }

}
