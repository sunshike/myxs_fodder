<?php
/**
 * myxs_fodder支付宝小程序接口定义
 *
 * @author myxinshang
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Myxs_fodderModuleAliapp extends WeModuleAliapp {
	public function doPageTest(){
		global $_GPC, $_W;
		// 此处开发者自行处理
        $content_count = intval(pdo_fetchcolumn("select count(*) from " . tablename('myxs_fodder_content') . " where uniacid=:uniacid and type='img' and content_status in (0,1,2) and is_check=0  ",array(':uniacid'=>$_W['uniacid'])));
        $start = max(1, intval($_GPC['s']));
        $end = 1;
//        if($content_count > 0){
//            $this->run($start,$end,$content_count);
//        }


	}
    /**
     * 生成图片
     * @param string $im 源图片路径
     * @param string $dest 目标图片路径
     * @param int $maxwidth 生成图片宽
     * @param int $maxheight 生成图片高
     */
    function resizeImage($im, $dest, $maxwidth, $maxheight) {
        $img = getimagesize($im);
        switch ($img[2]) {
            case 1:
                $im = @imagecreatefromgif($im);
                break;
            case 2:
                $im = @imagecreatefromjpeg($im);
                break;
            case 3:
                $im = @imagecreatefrompng($im);
                break;
        }

        $pic_width = imagesx($im);
        $pic_height = imagesy($im);
        $resizewidth_tag = false;
        $resizeheight_tag = false;
        if (($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight)) {
            if ($maxwidth && $pic_width > $maxwidth) {
                $widthratio = $maxwidth / $pic_width;
                $resizewidth_tag = true;
            }

            if ($maxheight && $pic_height > $maxheight) {
                $heightratio = $maxheight / $pic_height;
                $resizeheight_tag = true;
            }

            if ($resizewidth_tag && $resizeheight_tag) {
                if ($widthratio < $heightratio)
                    $ratio = $widthratio;
                else
                    $ratio = $heightratio;
            }


            if ($resizewidth_tag && !$resizeheight_tag)
                $ratio = $widthratio;
            if ($resizeheight_tag && !$resizewidth_tag)
                $ratio = $heightratio;
            $newwidth = $pic_width * $ratio;
            $newheight = $pic_height * $ratio;

            if (function_exists("imagecopyresampled")) {
                $newim = imagecreatetruecolor($newwidth, $newheight);
                imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
            } else {
                $newim = imagecreate($newwidth, $newheight);
                imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
            }

            imagejpeg($newim, $dest);
            imagedestroy($newim);
        } else {
            imagejpeg($im, $dest);
        }
    }

    /**
     * 图片压缩处理
     * @param string $sFile 源图片路径
     * @param int $iWidth 自定义图片宽度
     * @param int $iHeight 自定义图片高度
     * @return string  压缩后的图片路径
     */
    function getThumb($sFile,$iWidth,$iHeight){
        //图片公共路径
        $public_path = '';
        //判断该图片是否存在
        if(!file_exists($public_path.$sFile)) return $sFile;
        //判断图片格式(图片文件后缀)
        $extend = explode("." , $sFile);
        $attach_fileext = strtolower($extend[count($extend) - 1]);
        if (!in_array($attach_fileext, array('jpg','png','jpeg','gif'))){
            return '';
        }

        //压缩图片文件名称
        $sFileNameS = str_replace(".".$attach_fileext, "_".$iWidth.'_'.$iHeight.'.'.$attach_fileext, $sFile);
        //判断是否已压缩图片，若是则返回压缩图片路径
        if(file_exists($public_path.$sFileNameS)){
            return $sFileNameS;
        }

        //生成压缩图片，并存储到原图同路径下
        $this->resizeImage($public_path.$sFile, $public_path.$sFileNameS, $iWidth, $iHeight);
        if(!file_exists($public_path.$sFileNameS)){
            return $sFile;
        }
        return $sFileNameS;
    }
	public function run($start,$end,$total){
        global $_GPC, $_W;
        $system = pdo_fetch("select * from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$_W['uniacid'],':system_code'=>'system'));
        $system_content  = json_decode($system['system_content'],true);
        $wg_img = $system_content['wg_img'];

        $content = pdo_fetchall("select content,member_id,content_id from " . tablename('myxs_fodder_content') . " where uniacid=:uniacid and type='img' and content_status in (0,1,2) and is_check=0 order by content_id asc,content_status asc LIMIT ".($start - 1) * $end ."," . $end,array(':uniacid'=>$_W['uniacid']));
        if(!empty($content)){
            $aaa =array();
            $bbb =array();
            foreach ($content as $key=>$value){
                $content[$key]['content'] = json_decode($value['content'],true);
                foreach ($content[$key]['content'] as $keys => $values){
                    $background = toimage($values);
                    $round = mt_rand(1,100);
                    $img = file_get_contents($background);
                    $filePath = IA_ROOT . "/attachment/upload/img/".time()."-".$round.".jpg";
                    file_put_contents($filePath, $img);
                    $real_path=realpath($filePath);
                    $new_img = $this->getThumb($filePath,400,400);
                    $obj = new CURLFile($new_img);
                    $obj->setMimeType("image/jpeg");

                    $postdata['media']=$obj;

                    $access_token = $this->GetAccessToken();
                    $urls = 'https://api.weixin.qq.com/wxa/img_sec_check?access_token='. $access_token;
                    $a = $this->http_request($urls, $postdata);
                    @require_once (IA_ROOT . '/framework/function/file.func.php');

                    if($a['errcode'] != 0){
                        file_remote_delete($values);
                        $content[$key]['content'][$keys] = $wg_img;
                        $content[$key]['content2'][$keys] = $wg_img;
                        $aaa[$key]['k'] .=$keys.',';
                        $aaa[$key]['id'] = $value['content_id'];
                        $aaa[$key]['s'] = 0;
                        $aaa[$key]['c'] =  json_encode($content[$key]['content']);
                        $aaa[$key]['d'] =   json_encode($content[$key]['content2']);
                    }else{
                        $background =strstr($new_img,'upload/');
                        if(empty($_W['setting']['remote']['type'])){
                            $url = toimage($background);
                        }else{
                            @$filename = $background;
                            $remotestatus = file_remote_upload($background);
                            if (is_error($remotestatus)) {
                                file_delete($background);
                                $url = '';
                            } else {
                                file_delete($background);
                                $url = toimage($background);
                            }
                        }
                        $content[$key]['content'][$keys] = $values;
                        $content[$key]['content2'][$keys] = $url;
                        $aaa[$key]['id'] = $value['content_id'];
                        $aaa[$key]['c'] =  json_encode($content[$key]['content']);
                        $aaa[$key]['d'] =  json_encode($content[$key]['content2']);
                    }
                    $img1 = strstr($new_img,'upload/');
                    $img3 = strstr($filePath,'upload/');
                    file_delete($img1);
                    file_delete($img3);
                }
            }
            foreach ($aaa as $k=>$v){
                $ks = substr($v['k'],0,strlen($v['k'])-1);
                $data = array();
                if($v['s'] == 0){
                    $data['no_pass_id'] = $ks;
                }
                $data['is_check'] = 1;
                $data['content'] = $v['c'];
                $data['content2'] = $v['d'];
                pdo_update('myxs_fodder_content',$data,array('content_id'=>$v['id'],'uniacid'=>$_W['uniacid']));
            }

        }

        header("Location: https://we7.motuota.ltd/app/index.php?i=6&t=0&from=Aliapp&c=entry&m=myxs_fodder&a=Aliapp&do=Test");

    }
    private function http_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        $output = json_decode($output,true);
        return $output;
    }
    public function GetAccessToken() {
        global $_GPC, $_W;
        $type = intval($_GPC['type']);
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents("access_token.json"));
        $appid =$_W['account']['key'];
        $secret =$_W['account']['secret'];
        if ($data->expire_time < time()) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                $fp = fopen("access_token.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $access_token = $data->access_token;
        }
        if($type == 1){
            $this->result(0, '', array('status'=>true,'message'=>$access_token));
        }else{
            return $access_token;
        }
    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
}