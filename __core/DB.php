<?php
class_exists('PDO') or die('Be sure to appropriately set the PDO extension');
/**
 * 数据库操作类，支持数据库切换以及服务器切换
 * 
 * 初始化连接：
 *      DB::initConnect('default');
 * 切换数据库：
 *      DB::changeDb('blog');
 * 
 * 初始化连接与切换数据库都会对影响函数、最后插入id、结果集数进行初值设置
 * 
 * 查询数据（支持参数化）：
 *      $result=DB::select('select * from tbl_user where id > ? and id < ? order by id desc limit ?', array(5,20,10), 'all');//将返回多条结果集
 *      $result=DB::select('select * from tbl_user order by id desc');//只返回一条结果集
 * 插入数据：
 *      DB::insert('tbl_user', array('username'=>'nihao','email'=>'','password'=>'123456'));//返回主键id
 * 编辑数据：
 *      DB::edit('tbl_user',array('username'=>'ok_'.  rand(0, 999)),array('id'=>1));//返回影响行数
 * 
 * 执行一条sql（支持参数化）：
 *      DB::query('insert into tbl_user(username,password,email) values(?,?,?)',array('贩夫贩妇','6546s','sssssss'));
 * 
 * 开启调试，可以使用DB::echoBug();来输出sql调试信息
 * 
 * 本类的sql操作都是经过参数化的，在拼写sql时尽量使每个元素参数化处理，防止sql注入
 * 
 * 
 * @author pangyiguang <475901679@qq.com>
 * @link    <http://www.pangyiguang.com/>
 * @version 1.0
 */
class DB {
    
    static $_db = array();                      //数据操作句柄存放数组
    private static $_config=array();             //数据库的连接信息
    private static $_dbkey=null;                 //当前的操作的句柄标识
    private static $_insert_id = 0;              //最后插入id
    private static $_affected_rows = -1;         //受影响函数
    private static $_result_count=0;             //结果集数量
    private static $_query_num = 0;              //实际执行sql的数量
    private static $_array_sql = array();        //实际执行sql的详细信息
    private static $_ActiveTransaction=false;    //主动事务
    
    /**
     * 初始化执行方法，私有静态
     * @throws Exception
     */
    private static function _init() {
        try {
            if(!self::$_config){
                self::$_config=AHA::getConfig('dbconfig');
            }
            if(!isset(self::$_config[self::$_dbkey]) || !self::$_config[self::$_dbkey]){
                throw new Exception('There is no setting info for '.self::$_dbkey);
            }
            $attr=array(
                PDO::ATTR_PERSISTENT=>self::$_config[self::$_dbkey]['persistent'],
                PDO::ATTR_ERRMODE=>  PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT=>3
                );
            if(strpos(self::$_config[self::$_dbkey]['dsn'], 'sqlite:')===false){
                $attr[PDO::ATTR_AUTOCOMMIT]=1;
            }
            self::$_db[self::$_dbkey] = new PDO(self::$_config[self::$_dbkey]['dsn'], self::$_config[self::$_dbkey]['username'], self::$_config[self::$_dbkey]['password'],$attr);
            if(!self::$_db[self::$_dbkey]){
                throw new Exception('Connet failed:'.self::$_config[self::$_dbkey]['dsn']);
            }
            if(self::$_config[self::$_dbkey]['initsql'] && self::$_db[self::$_dbkey]->exec(self::$_config[self::$_dbkey]['initsql'])===false){
                throw new Exception('Exec sql failed:'.self::$_config[self::$_dbkey]['initsql']);
            }
        } catch (PDOException $exc) {
            Common::output($exc->getMessage());
        }
    }
    
    /**
     * 初始化变量
     */
    private static function _setDefault(){
        self::$_affected_rows = -1;
        self::$_insert_id = 0;
        self::$_result_count = 0;
    }

    /**
     * 初始化数据库连接信息
     * @param string $dbkey     连接标识
     * @param boolean $default  是否初始化内部私有属性
     */
    public static function initConnect($dbkey='',$default=true){
        if($dbkey){
            if(!self::$_dbkey || self::$_dbkey!==$dbkey){
                self::$_dbkey=$dbkey;
            }
        }else{
            if(!self::$_dbkey){
                self::$_dbkey='default';
            }
        }
        if($default){
            self::_setDefault();
        }
        if(!isset(self::$_db[self::$_dbkey])){
            self::_init();
        }
    }

    /**
     * 改变当前操作的数据库
     * @param string $dbname    数据库名称
     */
    public static function changeDb($dbname=''){
        self::initConnect();
        if($dbname && self::$_db[self::$_dbkey]->exec('use '.$dbname)===false){
            Common::output('red','changeDb '.$dbname.' failed');
        }
    }
    
    /**
     * 执行一条sql语句,只支持写入操作，不做任何结果集返回,要查询数据请用select();
     * 
     * DB::query('insert into users values(?,?,?,?)',array(18,'chunge','888@dd.com','K2544'));
     * 
     * @param string $sql   sql语句
     * @return boolean
     */
    public static function query($sql,$paramArray=array()) {
        self::initConnect('',false);
        return self::_ExecSQL($sql, $paramArray,$paramArray?true:false)?true:false;
    }

    /**
     * 执行sql唯一处理方法
     * @param string $sql           sql语句
     * @param array $paramArray     绑定的参数，数组形式，对应sql语句的参数
     * @param boolean $bind         是否绑定处理
     * @return resource|boolean
     */
    private static function _ExecSQL($sql,$paramArray,$bind=false){
        if(!$sql){
            return false;
        }
        if(!is_array($paramArray)){
            $paramArray=array();
        }
        try {
            $StmtHandle=self::$_db[self::$_dbkey]->prepare($sql,array(PDO::ATTR_CURSOR=>PDO::CURSOR_FWDONLY));
        } catch (PDOException $exc) {
            Common::output($exc->getMessage());
        }
        try {
            $StmtHandle=  self::_stmtSql_E($StmtHandle, $sql, $paramArray, $bind);
        } catch (PDOException $exc) {
            Common::output($exc->getMessage());
        }
        self::$_affected_rows=$StmtHandle->rowCount();
        self::$_insert_id=  self::$_db[self::$_dbkey]->lastInsertId();
        return $StmtHandle;
    }
    
    /**
     * sql参数化绑定与执行
     * @param resource $StmtHandle  pdo预处理句柄
     * @param string $sql           sql语句
     * @param array $vals           绑定的数组参数
     * @param boolean $bind         是否绑定
     * @return resource
     */
    private static function _stmtSql_E($StmtHandle,$sql,$vals=array(),$bind=false){
        if (AHA::getConfig('debug')){
            $t = microtime(true);
        }
        if($bind){
            $num=count($vals);
            if($num>0){
                for($i=0;$i<$num;$i++){
                    $StmtHandle->bindParam($i+1,$vals[$i],  self::_getType($vals[$i]));
                }
            }
            if(!$StmtHandle->execute()){
                Common::output('Exec SQL failed:'.$sql.';args:'.var_export($vals, 1));
            }
        }else{
            if(!$StmtHandle->execute($vals)){
                Common::output('Exec SQL failed:'.$sql.';args:'.var_export($vals, 1));
            }
        }
        if (AHA::getConfig('debug')){
            self::$_query_num++;
            $key=self::$_dbkey.':'.self::$_query_num;
            self::$_array_sql[$key]['value'] = htmlentities($sql);
            if($vals){
                self::$_array_sql[$key]['value'].=' &nbsp; &nbsp;  <b>绑定参数:</b>'.  var_export($vals, 1);
            }
            self::$_array_sql[$key]['key'] = round(microtime(true) - $t, 5);
        }
        return $StmtHandle;
    }

    /**
     * 外部用于插入数据的接口
     * @param string $table         表名
     * @param array $insertArray    插入数据，键值对应表数据的属性与值
     * @param string $type          插入模式，normal-正常；replace-替换插入；ignore-针对唯一数据忽略插入
     * @return integer  自增id
     */
    public static function insert($table,$insertArray,$type='normal'){
        self::initConnect('',false);
        if(!$table  || !$insertArray || !is_array($insertArray)){
            return 0;
        }
        if ($type === 'replace'){
            $insert_sql = 'replace into ';
        }elseif ($type === 'ignore'){
            $insert_sql = 'insert ignore into ';
        }else{
            $insert_sql = 'insert into ';
        }
        $insert_sql.='`' . trim($table, '`') . '`';
        $insertArray=  self::_GetPrepareData($insertArray,1);
        $insert_sql.=' (`'.  implode('`,`', $insertArray['param']).'`) values('.  implode(',', $insertArray['paramed']).')';
        self::_ExecSQL($insert_sql, $insertArray['paramValues']);
        return self::$_insert_id;
    }

    /**
     * 外部调用的修改数据方法
     * @param string $table     表名
     * @param array $editArray  修改数据，键值对应表数据的属性与值
     * @param string $where     修改条件，键值对应表数据的属性与值，and连接形式
     * @return integer  影响行数
     */
    public static function edit($table,$editArray,$where){
        self::initConnect('',false);
        if(!$table  || !$editArray || !is_array($editArray)){
            return 0;
        }
        $editArray=  self::_GetPrepareData($editArray,2);
        $sql='update `' . trim($table, '`') . '` set '.  implode(',', $editArray['paramed']).  self::_getWhereStr($where);
        self::_ExecSQL($sql, $editArray['paramValues']);
        return self::$_affected_rows;
    }
    
    /**
     * 查询数据
     * @param string $sql       sql语句
     * @param array $paramArray 绑定参数
     * @param string $mode      返回模式，one-返回一条；all-返回符合条件的所有记录，影响到数据的维数
     * @return array
     */
    public static function select($sql,$paramArray=array(),$mode='one'){
        self::initConnect('',false);
        return self::_getFetch(self::_ExecSQL($sql, $paramArray,true), $mode);
    }
    
    /**
     * 结果集游标处理
     * @param resource $StmtHandle  pdo预处理句柄
     * @param string $mode          返回模式，one-返回一条；all-返回符合条件的所有记录，影响到数据的维数
     * @return array
     */
    private static function _getFetch($StmtHandle,$mode='one'){
        $result=array();
        switch ($mode) {
            case 'one':
                $result=$StmtHandle->fetch(PDO::FETCH_ASSOC);
                break;
            case 'all':
                $result=$StmtHandle->fetchAll(PDO::FETCH_ASSOC);
                break;
            default:
                break;
        }
        self::$_result_count=count($result);
        return $result;
    }

    /**
     * 返回最后插入的id
     * @return integer
     */
    public static function lsatInsertId(){
        self::initConnect('',false);
        return self::$_insert_id;
    }
    
    /**
     * 返回受影响的行数
     * @return integer
     */
    public static function affectedRows(){
        self::initConnect('',false);
        return self::$_affected_rows;
    }
    
    /**
     * 返回结果集的总数
     * @return integer
     */
    public static function resultCount(){
        self::initConnect('',false);
        return self::$_result_count;
    }

    /**
     * 开始事务处理
     * @return boolean
     */
    public static function beginTransaction() {
        self::initConnect('',false);
        if(self::$_ActiveTransaction){
            return false;
        }else{
            self::$_db[self::$_dbkey]->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
            self::$_ActiveTransaction=self::$_db[self::$_dbkey]->beginTransaction();
            return self::$_ActiveTransaction;
        }
    }

    /**
     * 提交事务处理
     */
    public static function commit() {
        self::initConnect('',false);
        self::$_db[self::$_dbkey]->commit();
        self::$_ActiveTransaction=false;
        self::$_db[self::$_dbkey]->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
        //usleep(250000);
    }

    /**
     * 事务回滚
     */
    public static function rollBack() {
        self::initConnect('',false);
        self::$_db[self::$_dbkey]->rollBack();
        self::$_ActiveTransaction=false;
        self::$_db[self::$_dbkey]->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
    }
    

    /**
     * 参数处理
     * @param string $string            参数
     * @param integer $parameter_type   参数结构类型
     * @return string
     */
    private static function _quote($string, $parameter_type = PDO::PARAM_STR) {
        //return $string;
        return self::$_db[self::$_dbkey]->quote($string, $parameter_type);
    }
    
    /**
     * 预处理数据处理
     * @param array $data       处理的数据
     * @param integer $type     处理模式
     * @return array
     */
    private static function _GetPrepareData($data,$type=1){
        $return = array('param'=>array(),'paramed'=>array(),'paramValues'=>array());
        if($data){
            if($type===1){// 插入
                foreach ($data as $key => $value) {
                    $return['paramed'][]=':'.$key;
                    $return['param'][]=$key;
                    $return['paramValues'][':'.$key]=$value;
                }
            }elseif($type===2){//更新
                foreach ($data as $key => $value) {
                    $return['paramed'][]='`'.$key.'`=:'.$key;
                    $return['param'][]=$key;
                    $return['paramValues'][':'.$key]=$value;
                }
            }
        }
        return $return;
    }

    /**
     * where条件信息处理
     * @param array $data 要处理的数据
     * @return string
     */
    private static function _getWhereStr($data){
        $return = '';
        if($data && is_array($data)){
            $return=' where ';
            $i=0;
            foreach ($data as $key => $value) {
                $return.=($i===0?'':'and ').  self::_whereCondition($key,$value);
                $i++;
            }
        }  elseif($data && is_string($data)) {
            $return=' where '.$data;
        }
        return $return;
    }

    private static function _whereCondition($key,$value){
        $where='';
        if($key && $value){
            if(preg_match('@^([\w\-`]+)\s+([<>=]+)@U', $key, $matches)){
                $where='`'.trim($matches[1],'`').'` '.$matches[2].' '.  self::_quote($value, self::_getType($value));
            }else{
                $where='`'.trim($key,'`').'` = '.  self::_quote($value, self::_getType($value));
            }
        }
        return $where;
    }

    /**
     * 参数数据结构判断
     * @param mix $value    参数
     * @return integer
     */
    private static function _getType($value){
        $return = PDO::PARAM_STR;
        if(is_int($value)){
            $return = PDO::PARAM_INT;
        }elseif(is_null($value)){
            $return = PDO::PARAM_NULL;
        }elseif(is_bool($value)){
            $return = PDO::PARAM_BOOL;
        }elseif(is_resource($value)){
            $return = PDO::PARAM_LOB;
        }
        return $return;
    }
    
    /**
     * 用于输出调试信息（sql的执行情况）
     */
    public static function echoBug() {
        Common::bug('一共执行SQL', self::$_array_sql);
    }
}
