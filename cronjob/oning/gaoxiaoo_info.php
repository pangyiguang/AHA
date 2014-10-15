<?php
$on = DB::select('select id,title,link_url from collect_will where type=1 order by id asc limit 1',array(),'one');
if ($on && $on['title'] && $on['link_url']) {
    $header=array();
    $content = Http::curl($on['link_url'], 'GET', $header,NULL,NULL,5,FALSE);
    if ($content['body']) {
        $content = $content['body'];
        preg_match('@div class="left">([\s\S]+)<div class="mb10bgw bordert">@Ui', $content, $content);
        DB::query('delete from collect_will where id=' . $on['id']);
        if(isset($content[1]) && $content[1]){
            preg_match('@<div class="article singleye" title="(.+)">@Ui', $content[1], $title);
            preg_match('@<a.+rel="tag">(.+)</a>@Ui', $content[1], $tags);
            preg_match('@<p>([\s\S]+)</p>@Ui', $content[1], $texts);
            preg_match_all('@<img.+src="(.+)".+/>@Ui', $content[1], $pictures);
            preg_match_all('@<embed.+src="(.+)"@Ui', $content[1], $videos);
            if($texts && $texts[1]){
                if(strpos($texts[1], '<embed ')===false && strpos($texts[1], '<img ')===false){
                    $texts=trim(str_replace(array('看搞笑笑话就上：','搞笑哦！','搞笑哦','gaoxiaoo.com'), '', $texts[1]));
                }else{
                    $texts='';
                }                
            }else{
                $texts='';
            }
            
            if($tags && $tags[1]){
                $tags=trim($tags[1]);
            }else{
                $tags='';
            }
            
            if($title && $title[1]){
                $title=trim(str_replace(array('-搞笑哦'), '', $title[1]));
            }else{
                $title='';
            }
            
            if($pictures && $pictures[1]){
                $t=array();
                foreach ($pictures[1] as $value) {
                    $value=deal_gaoxiaoo_pic($value, $header);
                    if($value){
                        $t[]=$value;
                    }
                }
                if($t){
                    $pictures=  implode('||', $t);
                }else{
                    $pictures='';
                }
            }else{
                $pictures='';
            }
            
            if($videos && $videos[1]){
                $videos=trim(implode('||', $videos[1]));
            }else{
                $videos='';
            }
            if($title && ($texts || $pictures || $videos)){
                $insert=array(
                    'tags'=>$tags,
                    'title'=>$title,
                    'texts'=>$texts,
                    'pictures'=>$pictures,
                    'videos'=>$videos,
                    'addtime'=>time()
                );
                DB::insert('gaoxiao_draft',$insert);
            }           
        }
    }            
}

echo '<meta http-equiv="refresh" content="3" />';