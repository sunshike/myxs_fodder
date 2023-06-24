<?php
/**
 * myxs_fodder熊掌号接口定义
 *
 * @author myxinshang
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Myxs_fodderModuleXzapp extends WeModuleXzapp {
	public function doPageTest(){
		global $_GPC, $_W;
		// 此处开发者自行处理
		include $this->template('test');
	}

}