<?php
/**
 * myxs_fodder模块APP接口定义
 *
 * @author myxinshang
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Myxs_fodderModulePhoneapp extends WeModulePhoneapp {
	public function doPageTest(){
		global $_GPC, $_W;
		$errno = 0;
		$message = '返回消息';
		$data = array();
		return $this->result($errno, $message, $data);
	}
	
	
}