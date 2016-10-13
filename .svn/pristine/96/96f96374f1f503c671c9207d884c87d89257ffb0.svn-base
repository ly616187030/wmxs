<?php
namespace Mobile\Controller;
use \Think\Controller;
class OrderlistController extends Controller
{
    public function index()
    {
        //显示用户订单
        $model_order = M('order');
        //$uid = is_member_login();
        $where['o.uid']=is_member_login();
        $where['o.founder_id']=$_SESSION['companyid'];
        $cx_order = $model_order->alias('o')
            ->join("LEFT JOIN __PINGJIA__ AS p ON o.order_id = p.order_id")
            ->join("JOIN __ORDER_DETAIL__ AS d ON d.order_id = o.order_id")
            ->join("JOIN __STORE__ AS s ON s.store_id = o.store_id")
            ->where($where)
            ->order('o.order_id desc')
            ->field("o.order_id,p.content,o.status 'o',s.store_name,o.total,o.xd_time,s.store_logo_id,o.canju_total,o.ps_cost,o.yh_price,o.juan_price")
            ->group("o.order_id")
            ->select();

        $this->assign('order', $cx_order);
        $this->assign('redd','redd');
        if(is_member_login()){
            $this->display();
        }else{
            $this->redirect('Usercenter/index');
        }

    }

    public function del(){
        //删除对应的订单
        $model_order = M('order');
        $model_detail = M('order_detail');
        $id = I('get.order_id');
        $uid = is_member_login();
        $del_order = $model_order->where("order_id = $id and uid = $uid")->delete();
        $del_detail = $model_detail->where("order_id = $id")->delete();
        if($del_order && $del_detail){
            echo true;
        }else{
            $this->ajaxReturn('删除失败');
        }

    }

    public function detail()
    {
        //显示订单详情，以及订单状态
        $uid = is_member_login();
        empty($uid) && $this->redirect('Usercenter/index');
        $id = I('get.order_id',0,'intval');
        $model_detail = M('order_detail');
        $model_order = M('order');
        $model_pingjia = M('pingjia');
        $ordsn=I('ordsn');
        empty($id) && $id=$model_order->getFieldByOrderSn($ordsn,'order_id');

        $cx_detail = $model_detail
            ->join("LEFT JOIN wm_canming ON wm_canming.cm_id = wm_order_detail.cm_id")
            ->join("JOIN wm_store ON wm_store.store_id = wm_canming.store_id")
            ->where("order_id = $id")
            ->distinct(true)
            ->field("wm_store.store_name")
            ->select();
        $this->assign('detail', $cx_detail);
        $cx_detail = $model_detail
            ->join("LEFT JOIN wm_canming ON wm_canming.cm_id = wm_order_detail.cm_id")
            ->join("JOIN wm_store ON wm_store.store_id = wm_canming.store_id")
            ->where("wm_order_detail.order_id = $id")
            ->select();
        $this->assign('goods', $cx_detail);

        $cx_order = $model_order
            ->join("JOIN wm_user_address ON wm_user_address.address_id = wm_order.address_id")
            ->where("order_id = $id and wm_order.uid = $uid")
            ->select();
        $this->assign("order", $cx_order);
        $cx_orders = $model_order
            ->join("JOIN wm_user_address ON wm_user_address.address_id = wm_order.address_id")
            ->where("order_id = $id and wm_order.uid = $uid")
            ->field("wm_user_address.detail_address")
            ->find();
        $addr = explode("&",$cx_orders['detail_address']);
        $this->assign("orders", $addr);

        $cx_order = $model_order
            ->alias('ord')
            ->join('LEFT JOIN wm_delivery_detail AS d ON ord.order_id = d.order_id')
            ->where("ord.order_id = $id")
            ->limit(1)
            ->field('ord.*,d.status AS d_status,d.come_time,d.delivery_time')
            ->select();
        $this->assign("status", $cx_order);
        //dump($cx_order);
        $cx_pingjia = $model_pingjia->where("order_id = $id and uid = $uid")->select();
        $this->assign('pingjia',$cx_pingjia);

        $peisongfei=I('peisongfei');
        session('peisongfei',$peisongfei);
        $this->assign('peisongfei',session('peisongfei'));
        $this->display();
    }
    public function orderStatus(){
        $id = I('get.order_id');
        $status = I('get.status');
        $status1 = I('get.status1');
        $res = I('get.res');
        $order = M('order');
        $data['status'] = $status;
        $data1['status'] = $status1;
        if($status == 7){
            $data['cancel_ord_time'] = time();
            $this->delTempOrder($id,true);
            $xg_order = $order->where("order_id = $id")->save($data);
            if($xg_order){
                $this->ajaxReturn(true);
            }
        } elseif($status == 5){
            $data['complete_time'] = time();
            $xg_order = $order->where("order_id = $id")->save($data);
            if($xg_order){
                $this->ajaxReturn(true);
            }
        }elseif($status == 2){
            $data['pay_complete_time'] = time();
            $xg_order = $order->where("order_id = $id")->save($data);
            if($xg_order){
                $this->ajaxReturn(true);
            }
        }elseif($status == 8){
            $data['back_time'] = time();
            $xg_order = $order->where("order_id = $id")->save($data);
            if($xg_order){
                $this->ajaxReturn(true);
            }
        }elseif($res == 1){
            $xg_order = $order->where("order_id = $id")->save($data1);
            if($xg_order){
                $this->ajaxReturn(true);
            }
        }

    }
    public function pingjia(){
        //用户评价提交
        $uid = is_member_login();
        if($_POST){
            $data['pingfen'] = I('post.pingfen');
            $data['order_id'] = I('post.order_id');
            $data['pj_songda'] = I('post.pingfen1');
            $data['pj_service']=I('post.pingfen11');
            $data['content'] = I('post.content');
            $data['pj_time'] = time();
            $data['status'] = 1;
            $data['store_id'] = I('post.store_id');
            $data['uid'] = $uid;
            $ord['status'] = 6;
            $ord['pingjia_time'] = time();

            $model_pingfen = M('pingjia');
            $model_order = M('order');
            $save_pingjia = $model_pingfen->add($data);
            $save_order = $model_order->where("order_id =".$_POST['order_id'])->save($ord);
            if($save_pingjia && $save_order){
                $list_store_avg['pingfen'] = $model_pingfen->where('store_id ='.$data['store_id'])->avg('pingfen');//店铺平均分
                $pf = M('store')->where('store_id ='.$data['store_id'])->field('pingfen')->find();
                if($pf){
                    M('store')->where('store_id ='.$data['store_id'])->save($list_store_avg);
                }else{
                    M('store')->where('store_id ='.$data['store_id'])->add($list_store_avg);
                }
                echo true;
            }else{
                $this->ajaxReturn("提交失败");
            }
        }else{
            $model_pingjia = M('order');
            $cx_pingjia = $model_pingjia
                ->where("uid = $uid and order_id = ".$_GET['order_id'])
                ->find();
            $cx_detail = M('order_detail')
                ->join("JOIN wm_canming ON wm_canming.cm_id = wm_order_detail.cm_id")
                ->where(" order_id =".$_GET['order_id'])
                ->select();
            $this->assign('detail',$cx_detail);
            $this->assign('id',$cx_pingjia);
            $this->display();
        }


    }
    public function refund(){
        $order_id = 32/* $_GET['order_id']*/;
        $money = M('order')->where("order_id = $order_id")->field('total')->find();
        $this->assign('money',$money);
        $this->display();
    }
    /**
     * 删除临时订单，默认软删除，$type=true硬删除
     * @param int $ordid 订单id
     * @param bool $type
     * @return mixed
     */
    public function delTempOrder($ordid,$type=false){
        if(!empty($ordid) && $type){
            $num=M("OrderTemp")->where("order_id=".$ordid)->delete();
            return $num;
        }else if(!empty($ordid) && !$type){
            M("OrderTemp")->where("order_id=".$ordid)->setDec("status",2);
        }
    }
    //取消订单调转申请退款窗口
    public function cancelOrder(){
        $total=I('total',0,'floatval');
        $ordid=I('ordid');
        $stat=I('status');
        $this->assign('total',$total);
        $this->assign('status',$stat);
        $this->assign('ordid',$ordid);
        $this->display('refund');
    }
    public function cancelOrderReason(){

        $input=I('get.reason');
        $reason_test=I('get.reason_text');
        $orderid=I('get.orderid');
        $st=I('get.status');
        $str='';
        if(!empty($input)&&is_array($input)){
            foreach($input as $v){
                $str.=$v.';';
            }
        }
        $str.=$reason_test;
        $order=M('Order');
        $order->status=$st;
        $order->cancel_ord_time=time();
        $order->cancel_ord_reason=$str;
        $res=$order->where('order_id='.$orderid)->save();
        if($res!==false){
            $this->delTempOrder($orderid,true);
            $this->ajaxReturn($orderid);

        }else{
            $this->ajaxReturn('fail');
        }

    }
    public function queren(){
        $orderid = I('get.orderid');
        $order = M('order');
        $data['status']=5;
        $data['complete_time']=time();
        if(!empty($orderid)){
            $re = $order->where('order_id = '.$orderid)->save($data);
            $this->ajaxReturn($re);
        }

    }
    public function guojuBack(){
        $get = I('get.');
        $order = M('order');
        if(!empty($get)){
            $data['canju_status'] = 1;
            $data['canju_back_time'] = $get['backtime'];
            $orderid = $get['orderid'];
            $re = $order->where('order_id = '.$orderid)->save($data);
            $this->ajaxReturn($re);
        }
    }
}