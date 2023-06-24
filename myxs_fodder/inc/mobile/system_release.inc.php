<?php
$member = $this->getUserInfo('');
$system = pdo_fetch("select system from " . tablename('myxs_fodder_system') . " where uniacid = :uniacid and system_code = :system_code", array(':uniacid'=>$member['uniacid'],':system_code'=>'release'));
$system_seting = json_decode($system['system'],true);
foreach ($system_seting as $key =>$value){
    if(strpos(toimage($value),'http://') !== false){
        $system_seting[$key]  = str_ireplace("http://","https://",toimage($value));
    }else{
        $system_seting[$key] = toimage($value);
    }
}
$this->result(0, '', array('status'=>$system_seting));