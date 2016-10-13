<?php
/**
 * 订餐 wm_order
 * order_id  订餐id
 * uid       用户id
 * order_sn  订单号
 * store_id  店铺id
 * ps_cost   配送费
 * juan_id   低价卷id
 * juan_price 低价卷金额
 * total      实付总计
 * send_time  送餐时间  0为立刻，其他为指定时间
 * order_message 客户留言
 * pay_type   支付方式
 * address_id  送餐地址id
 * xd_time     下单时间
 * status      订单状态
 */

namespace Dispatch\Controller;

use Admin\Controller\AdminController;
class OrderSettingController extends AdminController
{
    public function index(){

        /*$sids=M("RestaurantAllot")->getFieldByUid(UID,'store_id');

            if(!empty($sids)){

            $map['store_id']=array("in",$sids);

            $data1=M("Store")->alias("s")->join('LEFT JOIN wm_shangquan AS q ON s.sq_id=q.sq_id')->where($map)->field("s.*,q.sq_name")->select();

        }*/

        //if(IS_ROOT){

            $data1=M("Store")->alias("s")->join('LEFT JOIN wm_shangquan AS q ON s.sq_id=q.sq_id')->field("s.*,q.sq_name")->order('store_id')->select();

        //}

        $this->assign("list",$data1);

        $this->meta_title = '订单取消管理';

        $this->display();

    }

    public function edit(){

        if(IS_POST){
            $order_dct = I('post.order_dct');
            $order_deft = I('post.order_deft');
            $phone = I('post.service_call');
            $store_id = I('post.store_id');
            if($order_deft == 1 || $order_deft == 2){
                if(strlen($phone) <7){
                    $this->error('服务电话不能小于7位');
                }
            }
            $data['service_call'] = $phone;
            $data['order_dct'] = $order_dct;
            $data['order_deft'] = $order_deft;
            $data1['store_id'] = $store_id;
            $shop = M("store");
            if($shop->where($data1)->save($data)!==false){
                $this->success('修改成功', U('index'));
            }else{
                $this->error('修改失败');
            }
        }else{
            $id = I('get.store_id');
            $store = M('store')->where("store_id=".$id)->find();/* 获取数据 */
            $this->assign('data', $store);
            $this->meta_title = '设置默认订单取消';
            $this->display();
        }

    }


}
