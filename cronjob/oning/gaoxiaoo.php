<?php
$header=array();
$time=time();
ob_start();
for($i=1;$i<1102;$i++){
    $url='http://gaoxiaoo.com/page/'.$i.'/';
    $content=Http::curl($url,'GET',$header);
    if($content['body']){
        preg_match_all('@<div class="article" title="(.+)">[\s\S]+<p><a href="(.+)"><strong>[\s\S]+</ul>@Ui', $content['body'], $content);
        if(!$content){
            continue;
        }
        array_shift($content);
        foreach ($content[0] as $key => $value) {
            $insert=array(
                'type'=>1,
                'title'=>  trim($value),
                'link_url'=>trim($content[1][$key]),
                'addtime'=>$time,
            );
            DB::insert('collect_will', $insert);
        }
        echo 'suss:';
    }else{
        echo 'fail:';
    }
    echo $url.'<br />';
    ob_end_flush();
    sleep(1);
}
