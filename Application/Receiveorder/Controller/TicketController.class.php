<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-01-18
 * Time: 9:29
 */
namespace Receiveorder\Controller;

use Admin\Controller\AdminController;
class TicketController extends AdminController{

    public function index(){
        $m = M('Order');
        $n = M('Order_detail');

        $order_id = I('post.id');

        $list = $m->alias('a')
            ->join("LEFT JOIN __USER_ADDRESS__ AS u ON a.address_id = u.address_id")
            ->join('LEFT JOIN __STORE__ AS s ON a.store_id = s.store_id')
            ->field('a.*,u.username,u.gender,u.phone,u.detail_address,s.store_name,s.tel')
            ->where('order_id = '.$order_id)->find();

        $list['zongjia'] = $list['total']+$list['ps_cost']+$list['canju_total']-$list['yh_price'];

        $list_detail = $n->alias('a')
            ->join('LEFT JOIN __CANMING__ AS c ON a.cm_id = c.cm_id')
            ->field('a.*,c.cm_name')
            ->where('a.order_id = '.$order_id)->select();

        if($list['pay_type'] == 0){
            $list['pay_type'] = "货到付款";
        }else if($list['pay_type'] == 1){
            $list['pay_type'] = "在线支付";
        }
        if($list['gender'] == 1){
            $list['gender'] = "先生";
        }else if($list['gender'] == 2){
            $list['gender'] = "女士";
        }

        $this->assign('list',$list);
        $this->assign('list_detail',$list_detail);
        $this->assign('time',time());
        $this->display();
    }
}