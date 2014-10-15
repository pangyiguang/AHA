<?php
define('TIME_STMP',time());

$result=  DB::select('select * from gaoxiao_draft order by islocal desc,id desc limit 1');
if($result && $result['title'] && ($result['texts'] || $result['pictures'] || $result['videos'])){
	$tags_name=str_replace(array('  ','   '), ' ', $result['tags']);
	$sql_data_array=array(
		'uid'=>0,
		'title'=>trim($result['title']),
		'tags_index'=> Common::to_full_index_str($tags_name),
		'tags_name'=>$tags_name,
		'content'=>$result['texts']?trim($result['texts']):trim($result['title']),
		'pictures'=>$result['pictures'],
		'video'=>  $result['videos'],
		'up_num'=>0,
		'down_num'=>0,
		'favorite_num'=>0,
		'commment_num'=>0,
		'display'=>1,
		'addtime'=>TIME_STMP,
		'uptime'=>TIME_STMP
	);
	if(DB::insert('gaoxiao', $sql_data_array)){
		$tags_name=  explode(' ', $tags_name);
		dealTag($tags_name);
	}
	DB::query('delete from gaoxiao_draft where id=?', array($result['id']));
}

function dealTag($tags){
	if($tags){
		foreach ($tags as $value) {
			$chtag=DB::select('select id from gaoxiao_tag where tagname=?',array($value));
			if($chtag && $chtag['id']){
				DB::query('update gaoxiao_tag set xiao_num=xiao_num+1 where id=?',array($chtag['id']));
			}else{
				DB::insert('gaoxiao_tag',array('tagname'=>$value,'xiao_num'=>1,'addtime'=>TIME_STMP,'uptime'=> TIME_STMP));
			}
		}
	}
}