<?php

// +----------------------------------------------------------------------

// | OneThink [ WE CAN DO IT JUST THINK IT ]

// +----------------------------------------------------------------------

// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.

// +----------------------------------------------------------------------

// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>

// +----------------------------------------------------------------------



namespace Home\Controller;

use Think\Controller;
/*
 *店铺收藏
 */
class FavoriteController extends HomeController {

	protected function _initialize(){
		parent::_initialize();
		$this->assign('_controller',CONTROLLER_NAME);
	}
	//用户收藏店铺页面
	public function index(){
        $this->login();
        $where['f.uid']=UID;
        $where['f.status']=1;
        $shoucang = M('favorites_store')->alias('f')
        ->join('LEFT JOIN wm_store s ON f.store_id=s.store_id')
        ->where($where)
        ->field('s.store_id,s.store_name,s.qisong_price')
        ->select();
        $renqi = M('pingjia')->field('store_id,round((sum(pingfen)/count(pingfen))/5,2) renqi')->group('store_id')->select();
        foreach ($shoucang as $key => $value) {
          foreach ($renqi as $ke => $va) {
                if($value['store_id']==$va['store_id']){
                  $shoucang[$key]['renqi']=$va['renqi'];
                }
          }
        }
        $this->assign("shoucang",$shoucang);
        $this->assign("meta_title", "收藏夹");
        $this->assign('is_login',is_login());
        $this->display();
	}
}

