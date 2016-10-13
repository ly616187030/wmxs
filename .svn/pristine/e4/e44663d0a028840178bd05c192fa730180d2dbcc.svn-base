<?php

namespace Home\Controller;

use Think\Controller;

class RefundController extends HomeController

{

    protected function _initialize(){

        parent::_initialize();

        $this->assign('_controller', CONTROLLER_NAME);

    }

    public function refund(){
        $this->login();
        $uid = UID;
        $orderlist = M('order');//实例化order对象
        $map['wm_order.status']  = array('in','8,11,12,15,16,17,18');
        $map['wm_order.uid']=$uid;
        $count = $orderlist->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $orderlist->join('LEFT JOIN wm_store ON wm_store.store_id = wm_order.store_id')
            ->where($map)->limit($Page->firstRow.','.$Page->listRows)
            ->order('order_id desc')
            ->field('wm_order.*,wm_store.order_deft,wm_store.order_dct,wm_store.tel')->select();//查询订单表中的数据
        $Page->setConfig('prev',' 上一页 ');
        $Page->setConfig('next','下一页');
        $Page->setConfig('theme', '%UP_PAGE% <span class="current">%NOW_PAGE%</span> %DOWN_PAGE%');
        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('count',$count);//将订单的总数量返回到HTML
        $this->assign("_position","我的退款");
        $this->assign('list',$list);
        $this->assign('is_login',is_login());
        $this->display();
    }
     public function detail(){
            $id = I('get.order_id');
            $where['order_id']=$id;
            $list = M('order')->where($where)->find();
            $this->assign('list',$list);
            $res = M('delivery_detail')->where($where)->find();
            $this->assign('res',$res);

            //通过订单ID查询餐名
             $detail = M('order_detail')
             ->alias('d')->where($where)
             ->join('LEFT JOIN wm_canming cm ON d.cm_id=cm.cm_id')
             ->select();
             $canpin = M('canpin')->select();
            foreach($canpin as $key=>$v){
                foreach($detail as $k=>$cv){
                    if($v['canpin_id'] == $cv['canping_id']){
                     $canpin[$key]['child'][] = $cv;
                    }
                }
                $canpin[$key]['count'] = count($list[$key]['child'],0);
             }
            $this->assign('canpin',$canpin);

            $shuliang = M('tableware')->find();
            $this->assign('shuliang',$shuliang);
            $this->display();
        }
    
}