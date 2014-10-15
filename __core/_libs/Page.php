<?php
/**
 * Description of Page
 *
 * @author pangyiguang
 */
class Page {
    
    public static function parse($initConfig=array()){
        //参数初始化
        $initConfig['page_star']=(isset($initConfig['page_star']) && $initConfig['page_star'])?$initConfig['page_star']:'';                         //分页uri前缀
        $initConfig['page_end']=(isset($initConfig['page_end']) && $initConfig['page_end'])?$initConfig['page_end']:'';                             //分页uri后缀
        $initConfig['total_num']=(isset($initConfig['total_num']) && $initConfig['total_num'])?(int)$initConfig['total_num']:0;                     //总记录数
        $initConfig['per_num']=(isset($initConfig['per_num']) && $initConfig['per_num'])?(int)$initConfig['per_num']:10;                            //每页条数
        $initConfig['current_page']=(isset($initConfig['current_page']) && $initConfig['current_page'])?(int)$initConfig['current_page']:1;         //当前页码
        $initConfig['num_shuzi']=(isset($initConfig['num_shuzi']) && $initConfig['num_shuzi'])?(int)$initConfig['num_shuzi']:8;                     //显示的数字分页数,
        $initConfig['show_info']=(isset($initConfig['show_info']) && $initConfig['show_info'])?(int)$initConfig['show_info']:0;                     //是否显示分页信息
        
        //分页处理开始
        $pages = ceil($initConfig['total_num'] / $initConfig['per_num']);
        $link['info'] = '';
        $link['link'] = '<div class="pagination"><ul>';
        if($initConfig['show_info']){
            $link['info']='共'.$initConfig['total_num'].'条 共'.$pages.'页 ';
        }
        if ($pages > 1) {//数字分页开始
            $nplink = '';
            $splink = '';
            if ($pages > $initConfig['num_shuzi']) {
                if ($initConfig['current_page'] <= intval($initConfig['num_shuzi'] / 2)) {
                    for ($i = 1; $i <= $initConfig['num_shuzi']; $i++) {
                        if ($initConfig['current_page'] == $i) {
                            $nplink .='<li><a class="active" target="_self" href="' . $initConfig['page_star'] . $i.'#">' . $i . '</a></li>';
                        } else {
                            $nplink .='<li><a target="_self" href="' . $initConfig['page_star'] . $i . $initConfig['page_end'] . '">' . $i . '</a></li>';
                        }
                    }
                }else{
                    if ($initConfig['current_page'] > ($pages - intval($initConfig['num_shuzi'] / 2))) {
                        for ($i = ($pages - $initConfig['num_shuzi'] + 1); $i <= $pages; $i++) {
                            if ($initConfig['current_page'] == $i) {
                                $nplink .='<li><a class="active" target="_self" href="' . $initConfig['page_star'] . $i.'#">' . $i . '</a></li>';
                            } else {
                                $nplink .='<li><a target="_self" href="' . $initConfig['page_star'] . $i . $initConfig['page_end'] . '">' . $i . '</a></li>';
                            }
                        }
                    } else {
                        for ($i = ($initConfig['current_page'] - intval($initConfig['num_shuzi'] / 2)); $i <= ($initConfig['current_page'] + intval($initConfig['num_shuzi'] / 2)); $i++) {
                            if ($initConfig['current_page'] == $i) {
                                $nplink .='<li><a class="active" target="_self" href="' . $initConfig['page_star'] . $i.'">' . $i . '</a></li>';
                            } else {
                                $nplink .='<li><a target="_self" href="' . $initConfig['page_star'] . $i . $initConfig['page_end'] . '">' . $i . '</a></li>';
                            }
                        }
                    }
                }
            } else {
                for ($i = 1; $i <= $pages; $i++) {
                    if ($initConfig['current_page'] === $i) {
                        $nplink .='<li><a class="active" target="_self" href="' . $initConfig['page_star'] . $i.'">' . $i . '</a></li>';
                    } else {
                        $nplink .='<li><a target="_self" href="' . $initConfig['page_star'] . $i . $initConfig['page_end'] . '">' . $i . '</a></li>';
                    }
                }
            }//数字分页结束
            if ($initConfig['current_page'] === 1) {
                $link['link'] .= '<li><a class="active" target="_self" href="' . $initConfig['page_star'] . '1#">首页</a></li>' . $nplink . '<li><a target="_self" href="' . $initConfig['page_star'] . ($initConfig['current_page'] + 1) . $initConfig['page_end'] . '">下一页</a></li>'.$splink.'<li><a target="_self" href="' . $initConfig['page_star'] . $pages . $initConfig['page_end'] . '">末页</a></li>';
            } elseif ($initConfig['current_page'] === $pages) {
                $link['link'] .= '<li><a target="_self" href="' . $initConfig['page_star'] . '1' . $initConfig['page_end'] . '">首页</a></li><li><a target="_self" href="' . $initConfig['page_star'] . ($initConfig['current_page'] - 1) . $initConfig['page_end'] . '">上一页</a></li>' . $nplink . '<li class="active"><a target="_self" href="#">末页</a></li>';
            } else {
                $link['link'] .= '<li><a target="_self" href="' . $initConfig['page_star'] . '1' . $initConfig['page_end'] . '">首页</a></li><li><a target="_self" href="' . $initConfig['page_star'] . ($initConfig['current_page'] - 1) . $initConfig['page_end'] . '">上一页</a></li>' . $nplink . '<li><a target="_self" href="' . $initConfig['page_star'] . ($initConfig['current_page'] + 1) . $initConfig['page_end'] . '">下一页</a></li>'.$splink.'<li><a target="_self" href="' . $initConfig['page_star'] . $pages . $initConfig['page_end'] . '">末页</a></li>';
            }
        }
        $link['link'] .= '</ul></div>';
        return $link;
    }
}
