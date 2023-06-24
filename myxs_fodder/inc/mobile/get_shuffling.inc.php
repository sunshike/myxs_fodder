<?php
$shuffling_position=$this->get('shuffling_position');
$shuffling_content = pdo_fetch("SELECT shuffling_content FROM " . tablename('myxs_fodder_shuffling') . " WHERE shuffling_position = :shuffling_position", array(':shuffling_position' => $shuffling_position));
$shuffling_content['content']=json_decode($shuffling_content['shuffling_content'],true);
foreach ($shuffling_content['content'] as $k=>$v){
    if(strpos(toimage($v),'http://') !== false){
        $shuffling_content['content'][$k]  = str_ireplace("http://","https://",toimage($v));
    }else{
        $shuffling_content['content'][$k] = toimage($v);
    }
}
$this->result(0,'',$shuffling_content);