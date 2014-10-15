<?php
/**
 * Description of index
 *  
 * @author pangyiguang
 */
class Actindex extends Controller {
    
    //初始化
    function _initialize(){
        
    }
    
    function index(){
        parent::getSmarty();
        parent::$smarty->assign('APP_ACTION', APP_ACTION);
        parent::$smarty->assign('APP_VIEW', APP_VIEW);
        parent::$smarty->assign('title', '一个调用smarty模板的例子');
        parent::$smarty->assign('result',  modeIndex::lists());
        parent::$smarty->display('index.tpl');
    }
    
    function index2(){
        $content=Cache::get('file', 'demo', 'index2');
        if($content){
            exit($content);
        }
        $data['title']='一个调用smarty模板的例子';
        $data['result']=modeIndex::lists();
        $content=MyView::display('index2', $data, TRUE,array(),true);
        Cache::set('file', 'demo', 'index2', $content, 3600*8);
        echo $content;
    }
}
       