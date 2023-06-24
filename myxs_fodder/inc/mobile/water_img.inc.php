<?php
global $_GPC,$_W;
$content_id = intval($_GPC['id']);
$member = $this->getUserInfo('');

$WaterMess = pdo_fetch("select stat_bg from " . tablename('myxs_fodder_water_bg') . " where uniacid = :uniacid and uid=:uid", array(":uid"=>$member['member_id'],':uniacid'=>$this->uniacid));

$stat_bg = $WaterMess['stat_bg'];

$content = pdo_fetch("select content from ".tablename('myxs_fodder_content')."where content_id =:content_id",array(':content_id'=>$content_id));
$content['content'] = json_decode($content['content'],true);
$img_arr = array();
foreach ($content['content'] as $k => $v){
    $content['content'][$k] = toimage($v);
    $config = array(
        'image'=>array(
            array(
                'url'=>$stat_bg,     //水印图片地址
                'is_yuan'=>false,          //true图片圆形处理
                'stream'=>0,
                'left'=>0,               //小于0为小平居中
                'top'=>0,                 //等于0时上下居中
                'bottom' =>0,
                'right'=>0,
                'width'=>100,             //图像宽
                'height'=>100,            //图像高
                'opacity'=>50,            //透明度
            ),
        ),
        'background'=>toimage($v), //背景图
    );

    $filename = IA_ROOT . "/attachment/upload/img/". time() . "-" . date("Y-m-d") .$member['open_id'].'-'.$member['member_id'].'-'.$k.".waterimg.png";

    $results = $this->createPostertt($config,$filename);

    $results  = substr($results,13);
    $img = "https://".$results;

//            file_delete($results);
    $img_arr[$k] = $img;
}
$size = sizeof($img_arr);;
$this->result(0, '生成成功', array('status'=>true,'content'=>$img_arr,'size'=>$size));