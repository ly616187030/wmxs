<?php
/**
 * Created by PhpStorm.
 * Auther: <zhangpf41@163.com>
 * Date: 2015/6/26
 * Time: 17:40
 */

namespace Mobile\Controller;
use Think\Controller;
use Think\Model;
class OrderController extends Controller{

    /**
     * 确认下单,保存订单基本信息
     */
    public function doOrder(){
        $posts=I("post.");
        $uid=is_member_login();
        $pushid=M('Store')->getFieldByStoreId($posts['store_id'],'push_msg_id');
        $data['uid']=$uid;
        $snn=$this->buildOrderSn();
        $data['order_sn']=$snn;
        $data['store_id']=$posts['store_id'];
        $data['store_sn']=$posts['store_sn'];
        $data['ps_cost']=$posts['ps_cost'];
        $data['yh_price']=$posts['yh_price'];
        $data['total']=$posts['total'];
        $data['send_time']=$posts['send_time'];
        $data['pay_type']=$posts['pay_type'];
        $data['order_message']=$posts['order_message'];
        $data['address_id']=$posts['address_id'];
        $data['xd_time']=time();
        $data['status']=$posts['pay_type']==0?3:1;
        $data['sh_mode']=2;
        $data['founder_id'] = $_SESSION['companyid'];
//        $data['yajin'] = $posts['yajin'];
//        $data['people_num'] = 0;
        $data['canju_total']=$posts['canjutotal'];
        //$data['check_status']=$checktoggle==0?1:0;
//        $data['qiju_quantity']=$posts['quantity_guo'];
        $data['from_operation']=2;
//        $data['juan_id']=$posts['juan_id'];
//        $data['juan_price']=$posts['juan_price'];
        $data['cuxiao_id']=$posts['cuxiao_id'];

        //当前订单数量
        $order=M('order');
        $condition['from_operation']=array('in',array(1,2,3,4));
        $condition['store_id']=$data['store_id'];
        $condition['xd_time']=array('between',array(strtotime(date("y-m-d",time())),strtotime(date("y-m-d",time()))+86400));
        $info=$order->where($condition)->count();
        $data['order_count']=$info+1;

        //$kanjia_id = $posts['kanjia_id'];
        $model= new Model();
        $model->startTrans();
        $flag=false;
        $goods_count=0;
        $orderid=$model->table(C('DB_PREFIX').'order')->add($data);
        if(!empty($orderid)){
            //$this->ajaxReturn(array('info'=>$orderid,'status'=>1));
            $data1=A('Cart')->cartView();
            $num=count($data1[0]);
            $temp=array();
            foreach($data1[0] as $v){
                $temp[]=array("order_id"=>$orderid,"cm_id"=>$data1[0][$v],"price"=>$data1[2][$v],"quantity"=>$data1[3][$v],"total_money"=>$data1[2][$v]*$data1[3][$v],"xd_time"=>time());
                $n = $this->updateGoodsSale($model, $posts['store_id'], $data1[0][$v], $data1[3][$v]);
                $goods_count+=(int)$n;

            }
            $orderlist=$model->table(C('DB_PREFIX').'order_detail')->addAll($temp);
            //$ordertemp=$model->table(C('DB_PREFIX').'order_temp')->add(array("order_id"=>$orderid,"store_id"=>$data['store_id'],"sh_mode"=>$data['sh_mode']));
            !empty($data['juan_id']) && M('user_bao')->where('user_bao_id='.$data['juan_id'])->setField('status',1);
            if(!empty($orderlist) && $goods_count==$num){
                if($posts['pay_type'] == 0) push_msg_client($pushid);
                $model->commit();
                $flag=true;
                //session('kanjia_id',null);
                //session('ohter',null);
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>$model->getError()));
            }
            if(!$flag){
                $model->rollback();
            }
            $this->ajaxReturn(array("status"=>$flag,"order_id"=>$orderid,"order_sn"=>$snn,"total"=>$posts['total'],"order_sn"=>$snn));
        }else{
            $this->error($model->getError());
        }
    }
    /**
     * 获取手机号
     */
//    public function getPhone($address_id){
//        $res=M("UserAddress")->where("address_id=".$address_id)->getField('phone');
//        return $res;
//    }

    /**
     * 生成唯一订单编号
     */
    private function buildOrderSn(){
        $res= time().substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 4);
        /*$map['order_sn']=$res;
        $data_sn=M("Order")->where($map)->find();
        if(empty($data_sn)){
            return $res;
        }else if($res==$data_sn){
            $this->buildOrderSn();
        }*/
        return $res;
    }
    //插入或更新销量
    public function updateGoodsSale(&$model, $store_id, $cm_id, $num)
    {
        $data['store_id'] = $store_id;
        $map['cm_id']=$data['cm_id']=$cm_id;
        $map['month']=$data['month']=(int)date("n",time());
        $map['year']=$data['year']=(int)date("Y",time());
        $data['quantity']=$num;
        $res=$model->table('wm_goods_count')->where($map)->find();
        if(empty($res)){
            $res1=$model->table('wm_goods_count')->add($data);
            if(!empty($res1)){
                return 1;
            }else{
                return 0;
            }
        }else{
            $res2=$model->table('wm_goods_count')->where($map)->setInc('quantity',$num);
            if($res2!==false){
                return 1;
            }else{
                return 0;
            }
        }
    }
}