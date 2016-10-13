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

namespace Receiveorder\Controller;
use Admin\Controller\AdminController;
use Common\Util\Think\Page;
class OrderController extends AdminController
{

    protected function _initialize(){
        parent::_initialize();
        if(!is_administrator())
            A('Admin/Contract')->ispayedCheck();
    }
    /*
     * 订单查询列表
     * */
    public function refuse(){
        $m = M('Order');
        $s = M('Store');
        $od = M('Order_detail');
        $ad = M('Admin_user');
        $as =M('Assigned_store');

        $uid = is_login();
        $stime = strtotime(I('stime'));
        $etime = strtotime(I('etime'));
        $order_sn = I('order_sn');
        $status = I('status');
        $pay_type = I('pay_type');
        $send_time = I('send_time');
        $confirm_time = I('confirm_time');
        $store_id = I('store_id');
        $today = strtotime(date('Ymd',time()));

        $where['uid'] = $where_as['user_id'] = $uid;

        $as_count = $as->where($where_as)->count();

        if(is_company($uid)){
            if(empty($store_id)){
                $map['founder_id'] = FID;
                $new_store_id = $s->where($map)->getField('store_id',true);
                if(!empty($new_store_id)){
                    $store_id = array('in',$new_store_id);
                }else{
                    $store_id = null;
                }
            }
        }else if($as_count > 0){
            if(empty($store_id)){
                $new_store_id = $s->where($where)->getField('store_id',true);
                if(!empty($new_store_id)){
                    $as_store_id = $as->where($where_as)->getField('store_id',true);
                    $as_store_id = array_unique(array_merge($as_store_id,$new_store_id));
                }else{
                    $as_store_id = $as->where($where_as)->getField('store_id',true);
                }
                $store_id = array('in',$as_store_id);
            }
        }else{
            $store_id = $s->where($where)->getField('store_id');
        }

        if(!empty($store_id)) {
            if(is_company($uid)){
                $map['founder_id'] = FID;
                $new_store_id = $s->where($map)->getField('store_id',true);
                $data2['a.store_id'] = array('in',$new_store_id);
            }else if($as_count > 0){
                $new_store_id = $s->where($where)->getField('store_id',true);
                if(!empty($new_store_id)){
                    $as_store_id = $as->where($where_as)->getField('store_id',true);
                    $as_store_id = array_unique(array_merge($as_store_id,$new_store_id));
                }else{
                    $as_store_id = $as->where($where_as)->getField('store_id',true);
                }
                $data2['a.store_id'] = array('in',$as_store_id);
            }else{
                $data2['a.store_id'] = $store_id;
            }
            $data['a.store_id'] = $store_id;
            $list_store = $s->alias('a')->where($data2)->select();   //根据当前登录uid查询店铺名称

            $qiantai = $s->where($where)->getField('is_qiantai_print');

            if(!empty($stime)){
                $data['a.xd_time'] = array('egt',$stime);
            }
            if(!empty($etime)){
                $data['a.xd_time'] = array('elt',$etime+86399);
            }
            if(!empty($stime) && !empty($etime)){
                $data['a.xd_time'] = array('between',array($stime,$etime+86400));
            }else{
                $data['a.xd_time'] = array('between',array($today,$today+86400));
            }
            if(!empty($order_sn)){
                $data['a.order_sn'] = array('like','%'.$order_sn.'%');
            }
            if(!empty($status)){
                if($status == 10){
                    $data['a.status'] = array('in',array(9,10));
                }else{
                    $data['a.status'] = $status;
                }
            }else{
                $data['a.status'] = array('egt',4);
            }
            if(!empty($pay_type)){
                $data['a.pay_type'] = $pay_type;
            }
            if(!empty($send_time)){
                if($send_time == 1){
                    $data['a.send_time'] = array('gt',$today+86401);
                }else if($send_time == 2){
                    $data['a.send_time'] = array('between',array($today,$today+86400));
                }

            }
            if(!empty($confirm_time)){
                switch($confirm_time){
                    case 1:$data['a.confirm_time'] = array('lt',time()-30*60);
                    case 2:$data['a.confirm_time'] = array('lt',time()-40*60);
                    case 3:$data['a.confirm_time'] = array('lt',time()-40*60);
                    case 4:$data['a.confirm_time'] = array('lt',time()-40*60);
                }
                $data['a.ord_peisong_status'] = array('neq',4);
                $data['a.source'] = 1 ;
            }

            $count = $m->alias('a')->where($data)->count();
            $sum = $m->alias('a')->where($data)->sum('total');
            $yh_price = $m->alias('a')->where($data)->Sum('yh_price');
            $ps_cost = $m->alias('a')->where($data)->Sum('ps_cost');
            $canju_total = $m->alias('a')->where($data)->Sum('canju_total');
//
            $sum_total = $sum+$ps_cost+$canju_total-$yh_price;
            $Page = new \Think\Page($count,10);
            //设置分页显示方式

            $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
            $Page->setConfig('first','首页');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('last','末页');
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
            $Page->lastSuffix = false;//分页最后的总页数不显示

            $show = $Page->show();

            $list_detail = $od->alias('a')
                ->join('LEFT JOIN __CANMING__ AS c ON a.cm_id = c.cm_id')
                ->field('a.*,c.cm_name')
                ->select();

            $list = $m->alias('a')
                ->join("LEFT JOIN __USER_ADDRESS__ AS b ON a.address_id = b.address_id")
                ->join("LEFT JOIN __STORE__ AS d ON a.store_id = d.store_id")
                ->field("a.*,b.*,d.store_name,d.tel")->order('order_id desc')
                ->where($data)->order('a.xd_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();

            foreach($list as $k=>$v){
                if($v['status']==4&&$v['ord_peisong_status']!=4){
                    $fwq_time=time();  //当前服务器时间
                    $yq_time=$fwq_time-$v['confirm_time'];   //接单后用去时间
                    $shengyu_time=45*60-$yq_time;   //用去时间与45分钟对比，大于0说明在45分钟内，小于0说明超出45分钟
                    $list[$k]['shengyu_time']=$shengyu_time;

                }
                if($v['status']==4&&$v['ord_peisong_status']==4){
                    $ps_time=$v['person_complete_time']-$v['confirm_time'];   //配送用去时间
                    $dingge_time=45*60-$ps_time;    //决定最后定格的时间
                    $list[$k]['dingge_time']=$dingge_time;
                }
//           dump($list[$k]['dingge_time']);
                foreach($list_detail as $a=>$b){
                    if($v['order_id'] == $b['order_id']){
                        $list[$k]['child'][] = $b;
                    }
                }
            }

            $this->assign('stime',$stime);
            $this->assign('etime',$etime);
            $this->assign('order_sn',$order_sn);
            $this->assign('status',$status);
            $this->assign('pay_type',$pay_type);
            $this->assign('send_time',$send_time);
            $this->assign('store_id',$store_id);
            $this->assign('count',$count);
            $this->assign('list',$list);
            $this->assign('page',$show);
            $this->assign('sum',$sum_total);
            $this->assign('qiantai',$qiantai);
            $this->assign('period',$confirm_time);
            $this->assign('store',$list_store);
            $this->assign('time',time());

        }else{
            $this->assign('store_id',null);
        }

        $this->assign('is_print',is_print());
        $this->display();
    }
 //查询
    public function query(){
       $post = I('post.');
       $order = M('order');
        $s =M('store');
       $order_detail = M('order_detail');
       if($post['send_time'] == 1){
            $diyi = strtotime(date('Y-m-d',time()))+86401;
            $dier = "";
       }else if($post['send_time'] == 2){
            $diyi = strtotime(date('Y-m-d',time()));
            $dier = strtotime(date('Y-m-d',time()))+86400;
       }else{
            $diyi = "";
            $dier = "";
       }
       if($post['status'] == 10){
           $no = array(array('in','9,10'));
       }else{
           $no = array(array('in',$post['status']));
       }
        $uid = session('user_auth.uid');
        $store_ids = $s->where('uid = '.$uid)->getField('store_id',true);
        $store_ida = $s->Field('store_id')->select();
        $ids = array_column($store_ida, 'store_id');
        if($post['store_id'] == 1){
            if($uid == 1){
                $store_id1 = array('in',$ids);
            }else{
                $store_id1 = array('in',$store_ids);
            }
        }
//       $where1['uid'] = $uid;
//       $restaurant_allot = M('restaurant_allot')->where($where1)->field('store_id')->find();
       $jiesutime = strtotime($post['stime']);
       $aaa = strtotime($post['etime'])+86399;
       $array = array();
       foreach($post as $v => $n){
            if($post[$v] != null || $post[$v] != ""){
                switch($v){
                    case 'status':
                        $v = "a.status";
                        $array[$v] = $no;
                        break;
                    case 'store_id':
                        $v = 'a.store_id';
                        $array[$v] = $store_id1;
                        break;
                    case 'stime':
                        $v = "a.xd_time";
                        $n = array('egt',strtotime($n));
                        $array[$v] = $n;
                        break;
                    case 'etime':
                        $v = "a.xd_time";
                        $n = array(array('elt',$aaa),array('egt',$jiesutime));
                        $array[$v] = $n;
                        break;
                    case 'order_sn':
                        $v = "a.order_sn";
                        $n = array('like',"%". $n . "%");
                        $array[$v] = $n;
                        break;
                    case 'pay_type':
                        $v = "a.pay_type";
                        $array[$v] = $n;
                        break;
                    case 'send_time':
                        $v = 'a.send_time';
                        $n = array(array('egt',$diyi),array('elt',$dier));
                        $array[$v] = $n;
                        break;
                }
            }
       }
       $order_id = $order->alias('a')->join("LEFT JOIN __USER_ADDRESS__ AS b ON a.address_id = b.address_id")
                                      ->join("LEFT JOIN __RESTAURANT_ALLOT__ AS c ON b.uid = c.uid")
                                      ->join("LEFT JOIN __STORE__ AS d ON a.store_id = d.store_id")
                                      ->where($array)
                                      ->field("a.*,b.*,c.store_id,d.store_name,d.tel")->order('order_id desc')
                                      ->select();
       foreach($order_id as $v => $n){
            $where["order_id"] = $n['order_id'];
            $order_detail_id = $order_detail->where($where)->select();
            foreach($order_detail_id as $a => $b){
                $where2['cm_id'] = $b['cm_id'];
                $canming = M('canming')->where($where2)->field('cm_name')->find();
                $order_detail_id[$a]['cm_name'] = $canming;
            }
            $order_id[$v]['xiangqing'] = $order_detail_id;
            $order_id[$v]['xd_time'] = date('Y-m-d H:i',$n['xd_time']);
            $order_id[$v]['zongjia'] = $n['total']+$n['ps_cost']+$n['canju_total']-$n['yh_price'];  //
            $order_id[$v]['send_time'] = date('Y-m-d H:i',$n['send_time']);
            $order_id[$v]['now_time'] = date("m月d日 H:i:s",time());
            $order_id[$v]['ticketname'] = C('TICKETNAME');
            if($order_id[$v]['gender'] == 1){
                $order_id[$v]['gender'] = $order_id[$v]['username']."先生";
            }else{
                $order_id[$v]['gender'] = $order_id[$v]['username']."女士";
            }
            if($order_id[$v]['pay_type'] == 1){
                $order_id[$v]['pay_type'] = "在线支付";
            }else{
                $order_id[$v]['pay_type'] = "货到付款";
            }
       }
        $this->ajaxReturn($order_id);
}

  //订单记录推送
    public function pushOrders(){
        $id = I('id');
        $or = M('order');
        $au = M('Admin_user');
        $orderMsg=$or->alias('a')
            ->join('LEFT JOIN __STORE__ AS s ON a.store_id = s.store_id')
            ->field('a.*,s.store_sn')
            ->where("a.order_id = $id")->find();
        $where['order_id']=$id;
        $where1['address_id']=$orderMsg['address_id'];
        $canming = M('canming')->alias('c')
            ->join('RIGHT JOIN __ORDER_DETAIL__ AS d ON c.cm_id = d.cm_id')
            ->where($where)->field('c.cm_name,c.cm_desc,d.*')->select();
        $add = M('user_address')->where($where1)->find();
        $orderMsg['order_detail'] = $canming;
        $orderMsg['address'] = $add;
        $push_dispatch_id = C('PUSH_DISPATCH_ID');
        $post_data=array(
            'order'=>$orderMsg,
        );
        $diaodu = C('WEBURL');
        $url ='http://'.$diaodu.'/index.php?s=/Dispatch/OrderInfo/orderMsg';
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',//or GET
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context =stream_context_create($options);
        $result = json_decode(file_get_contents($url, false, $context),true);
        if($result == true){
            push_msg_client($push_dispatch_id);
            $data['info'] = "推送成功";
            $data['value'] = 1;
            $save['dispatch_status'] = 1;
            $or->where($where)->save($save);
            action_log('edit_action','order',$id,is_login());
        }else{
            $data['info'] = "推送失败";
            $data['value'] = 0;
        }
        $this->ajaxReturn($data);
    }
    //商家拒单
    public function confirm1(){
        $id = I('id');
        $reject_reason = I('reject_reason');
        $data['reject_reason'] = $reject_reason;
        $data['reject_time'] = time();
        $status = I('status');
      if($status == 1){
          $data['status'] = 10;
      }else{
          $data['status'] = 9;
      }
        $data['ord_peisong_status'] = 5;
        if(M('order')->where('order_id ='.$id)->save($data)){
            action_log('refuseOrder_action','order',$id,is_login());
            $save['judan'] = "拒单成功";
            $save['status'] = 1;
        }else{
            $save['judan'] = "拒单失败";
            $save['status'] = 0;
        }
        $this->ajaxReturn($save);
    }
    //完成订单
    public function complete(){
        $id = I('id');
        $where['status'] = 5;
        $where['complete_time'] = time();
        $where['ord_peisong_status'] = 4;
//        $where['ps_time'] = time();
        if(M('order')->where('order_id ='.$id)->save($where)){
            action_log('finish_action','order',$id,is_login());
            $save['wancheng'] = "完成订单";
            $save['status'] = 1;
        }
        $this->ajaxReturn($save);
    }



    //接单管理
    public function receive(){

        if(is_administrator()){
            $this->display('empty');
            exit;
        }
        $ordid=I("get.ordid");
        $type=I("get.type",0,'intval');

        $store=M("RestaurantAllot")->getFieldByUid(UID,"store_id");
        !empty($store)?$m=$store:$m=0;

        if($type==1){
            $where = '(s.status=2 OR s.status=3) AND s.check_status=1 AND s.jd_status=0 AND s.store_id in ('.$ordid.')';
        }else{
            $where = '(s.status=2 OR s.status=3 ) AND s.check_status=1 AND s.jd_status=0 AND s.store_id in ('.$m.')';
        }

        $User = M('order'); // 实例化User对象

        $li = $User->alias('s')
            ->join('LEFT JOIN wm_user_address AS cc ON s.address_id=cc.address_id')
            ->join('LEFT JOIN wm_store AS ss ON s.store_id = ss.store_id')
            ->join('LEFT JOIN wm_member AS m ON s.uid=m.uid')
            ->Field('s.*,m.push_msg_id,cc.username,cc.lng,cc.lat,cc.detail_address,cc.phone,ss.store_rebate,ss.store_name,ss.phone AS store_phone,ss.lng AS store_lng,ss.lat AS store_lat')
            ->order('xd_time desc')->where($where)->select();
        if(!empty($li)){
            foreach($li as & $v){
                $res=distanceBetween($v['lng'],$v['lat'],$v['store_lng'],$v['store_lat']);
                $v['xd_time']=date("Y-m-d H:i:s",$v['xd_time']);
                $v['send_time_to']=date("Y-m-d H:i:s",$v['send_time']);
                $v['distance']=$res;
            }
        }
        $list = $this->getDetail($li);
        //dump($list);
        $this->assign("list",$list);
        $this->assign("_storestr",$m);
        $this->assign("curr",strtotime(date("Y-m-d",strtotime("+1 day"))));
        if($type==1){
            $this->ajaxReturn($list);
        }else{
            $this->meta_title="待处理订单";
            $this->display();
        }
    }

    /*
     * 修改订单状态为商家拒接
     * status=9 商家拒单（用户货到付款）
     * status=10 商家拒单（用户在线支付后，商家拒单，转到退款）
     */
    public function setReject(){
        $id = I('post.id');
        $user = M("order");
        $reason=I('post.reject_reason');
        $paytype=I('post.paytype');
        $pushid=I('post.pushid');
        $paytype==1?$status=10:$status=9;
        $user->reject_time=time();

        $user->status=$status;
        $user->reject_reason="$reason";

        $list = $user->where("order_id=".$id)->save();
        push_msg_client($pushid);
        //$this->delTempOrder($id,true);
        action_log('edit_action','order',$id,is_login());
        $this->ajaxReturn($list);
    }
    /*
    * 在中制作中订单
    * */
    public function confirm(){
        $id = session('store_id');
        if(!empty($id)){
            $where = "s.status = 4 AND s.store_id = $id";
            $this->setStatus($where);
        }else{
            $data = "您还没有选择店铺，请先点击右上角选择店铺。";
            $this->assign('pd_dp', $data);
        }

        $this->meta_title="确认订单";
        $this->display();
    }
    /*
     * 菜品制作完成
     * status=14
*/
    public function setOrder(){
        $id = I('post._id',0,'intval');

        $user = M("order");
        $user->status="14";
        $user->making_time=time();
        $map['order_id']=$id;
        $list = $user->where($map)->save();
        if($list!==false){
            M("DeliveryDetail")->where($map)->setField('status',2);
            $this->ajaxReturn(array("status"=>1));
        }else{
            $this->ajaxReturn(array("status"=>0));
        }
    }


    /*
     * 修改订单状态为确认退款已完成
     * status=12 完成退款，此单已完成
     */
    public function setRefund(){
        $id = I('post._id');
        $user = M("order");
        $user->status="12";

        $list = $user->where("order_id=".$id)->save();

        $this->ajaxReturn($list);
    }
    public function getDetail($or){

        $User = M('order_detail');
        foreach ( $or  as & $v1 ) {
            $list = $User->alias('s')
                ->join('LEFT JOIN wm_canming AS c ON s.cm_id=c.cm_id')
                ->Field('s.*,c.cm_name')
                ->where("order_id=".$v1["order_id"])->select();
            if($list != null){
                $v1["detail"]=$list;
            }
            //dump($v1);
        }
        return $or;
    }
    function setStatus($where,$pingjia=false){
        $m = M("Order");
        $count = $m->alias('s')->where($where)->count();

        $Page = new \Think\Page($count,15);
        //设置分页显示方式
        $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
        $Page->setConfig('first','首页');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('last','末页');
        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $Page->lastSuffix = false;//分页最后的总页数不显示
        $show = $Page->show();

        $dp = M("Delivery_person")->select();

        $li = $m->alias('s')
            ->join('LEFT JOIN wm_ucenter_member AS c ON s.uid=c.id')
            ->join('LEFT JOIN wm_user_address AS cc ON s.address_id=cc.address_id')
            ->join('LEFT JOIN wm_store AS store ON s.store_id=store.store_id')
            ->join('LEFT JOIN wm_delivery_detail AS dd ON s.order_id = dd.order_id')
            ->Field('s.*,c.username,cc.*,dd.person_id,dd.status AS delivery_status,store.store_rebate,store.store_name')->order('xd_time desc')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        //把配送员姓名和配送员电话写入到$li数组中
        /*foreach($li as $k=>$v){
            if($pingjia) $li[$k]['pingjia']=M("Pingjia")->where("order_id=".$v['order_id'])->find();
            foreach($dp as $a){
                if($v['person_id'] == $a['person_id']){
                    $li[$k]['person_name'] = $a['person_name'];
                    $li[$k]['person_phone'] = $a['phone'];
                }
            }
        }*/

        $list = $this->getDetail($li);
        //dump($list);
        $this->assign('list',$list);
        $this->assign('page',$show);
    }
    /*
     * 分配配送员
     * */
    public function peiSong(){
        $storeid=I("storeid",0,'intval');
        $shmode=I("shmode",0,'intval');
        $m = M('Delivery_person');
        $dd = M('Delivery_detail');
        //注册商家
        //$where['store_id']=$storeid;
        //$store=M("Store")->where($where)->find();
        if(!empty($shmode) && $shmode==2) {
            //平台配送
            $this->ajaxReturn(array("status"=>0));
        }elseif(!empty($shmode) && $shmode==1){
            //商家自己配送
            $map['uid']=UID;
            $map['is_who']=0;
            $list_p=$m->where($map)->select();
            $str="";
            foreach($list_p as $vv){
                $str.=$vv['person_id'].",";
            }
            $map1['person_id']=array("in",rtrim($str,","));
            if(!empty($list_p)){
                $count=$dd->where($map1)->select();
            }else{
                $this->ajaxReturn(array("status"=>2));
            }

        }

        //把当前配送员所拥有的订单数量写入到$list_p的count中
        if(!empty($list_p) && !empty($count)){
            foreach($list_p as $k=>$v){
                foreach($count as $c=>$d){
                    if($v['person_id'] == $d['person_id']){
                        $map['person_id']=$d['person_id'];
                        $map['year']=date("Y",time());
                        $map['month']=date("n",time());
                        $map['day']=date("j",time());
                        $list_p[$k]['count'] =$dd->where($map)->count();
                    }
                }
            }
        }

        $this->ajaxReturn(array("status"=>1,"list"=>$list_p));
    }
    /*
     * 分配订单到配送员并写入数据库
     * 接单
     * */
    public function setPeisong(){
        $id =I('order_id',0,'intval');
        $storeid=I('store_id',0,'intval');
        $personid=I('person_id',0,'intval');
        $sh_mode=I('sh_mode',0,'intval');
        $pushid=I('pushid');
        if(!empty($id)){
            if(!empty($storeid) && !empty($personid)){
                $data['person_id']=$personid;
                $data['store_id']=$storeid;
                $data['order_id']=$id;
                $data['year']=date("Y",time());
                $data['month']=date("n",time());
                $data['day']=date("j",time());
                $data['status']=1;
                M('Delivery_detail')->add($data);
            }
            $user = M("order");
            $user->status="4";
            $user->jd_status=1;//调度可以接
            $user->confirm_time=time();
            $list = $user->where("order_id=".$id)->save();
            if($list!==false){
                $msg=push_msg_client($pushid);
                push_msg_client(C('DIAODU_PUSHID'));
                $sh_mode==1?$type=true:$type=false;
                //$this->delTempOrder($id,$type);
                //$this->ajaxReturn(array('status'=>1));
                $this->ajaxReturn($msg);
            }
        }
    }
    /**
     * 正在配送中订单
     */
    public function delivering(){

        $id = M("RestaurantAllot")->getFieldByUid(UID,"store_id");
        if(!empty($id)){
            $where = " s.status = 4 AND s.ord_peisong_status<4 AND s.store_id in ($id)";
            $this->setStatus($where);
        }else{
            $data = "您还没有选择店铺，请先点击右上角选择店铺。";
            $this->assign('pd_dp', $data);
        }
        $this->assign("curr",strtotime(date("Y-m-d",strtotime("+1 day"))));
        $this->meta_title="正在配送中";
        $this->display();
    }

    public function sellerConfirmOrder(){
        $id=I("orderid",0,'intval');
        if(!empty($id)){
            $data['complete_time']=time();
            $data['status']=5;
            $map['order_id']=$id;
            $res=M("Order")->where($map)->save($data);
            if($res!==false){
                $this->ajaxReturn('success');
            }else{
                $this->ajaxReturn('fail');
            }
        }

    }

    /**
     * 获取订单临时表内容
     */
    public function getTempOrder(){
        $storeid=I("storelist");
        $map['store_id']=array("in",$storeid);
        $map['status']=1;
        $temp=M("OrderTemp")->where($map)->select();
        $res=array();
        if(!empty($temp)){
            foreach($temp as $v){
                $res[]=$v['order_id'];
            }
            $temp=implode(",",$res);
        }else{
            $temp=null;
        }
        $this->ajaxReturn($temp);
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

    /**
     * 申请退款
     * @param null $keywords time
     */
    public function refund($keywords=null){
        $s = M('store');
        $uid = session('user_auth.uid');
        if(is_administrator($uid)){
            $store_id = $s->getField('store_id',true);
        }elseif(is_company($uid)){
            $map['founder_id'] = FID;
            $storeids = $s->where($map)->getField('store_id',true);
            $store_id = $storeids?:'';
        }else{
            $store_id = $s->where('uid = '.$uid)->getField('store_id',true);
            if(empty($store_id)){
                $this->error('请联系管理员分配店铺');
            }
        }
        if(IS_POST){
            empty($keywords)?$curr=strtotime(date('Y-m-d',time())):$curr=strtotime($keywords);
            $tomorrow=$curr+86400;
            $where['status'] = array('in','8,10,15,17');
            $where['store_id'] = array('in',$store_id);
            $where['xd_time'] = array('gt',$curr);
            $where['xd_time'] = array('lt',$tomorrow);
            //$where = "status in(8,10,15,17) AND store_id in ($store_id) AND xd_time>$curr AND xd_time<$tomorrow";
        }else{
            $today=strtotime(date('Y-m-d',time()));
            $tomorrow=$today+86400;
            $where['status'] = array('in','8,10,15,17');
            $where['store_id'] = array('in',$store_id);
            $where['xd_time'] = array('gt',$today);
            $where['xd_time'] = array('lt',$tomorrow);
            //$where = "status in(8,10,15,17) AND store_id in ($store_id) AND xd_time>$today AND xd_time<$tomorrow";
        }

        $list=M('order')->where($where)->select();
        $this->assign('list',$list);
        $this->display();
    }

    /**
     * 修改退款订单状态
     */
    public function modifyRefundStatus(){
        $order_sn=I('order_sn');
        $statu=I('status',0,'intval');
        switch($statu){
            case 8:
                $st=11;
                break;
            case 10:
                $st=15;
                break;
            case 13:
                $st=16;
                break;
            case 11:
                $st=12;
                break;
            case 15:
                $st=17;
                break;
            case 16:
                $st=18;
                break;
            default:
                break;
        }
        $ord=M('Order');
        if(!empty($order_sn)){
            $ord->status=$st;
            $ord->back_time=time();
            $map['order_sn']=$order_sn;
            $res=$ord->where($map)->save();
            action_log('edit_action','order',$order_sn,is_login());
            if(!empty($res)&&$res!==false){

                $this->ajaxReturn($res);
            }
        }
    }
    public function getOrderDetail(){
        $ordid=I('ordid',0,'intval');
        if(!empty($ordid)){
            $res=M('OrderDetail')
                ->alias('ord')
                ->join('LEFT JOIN wm_canming AS cm ON ord.cm_id=cm.cm_id')
                ->join('LEFT JOIN wm_order AS o ON ord.order_id=o.order_id')
                ->join('LEFT JOIN wm_user_address AS a ON o.address_id=a.address_id')
                ->field('ord.*,cm.cm_name,a.*')
                ->where('ord.order_id='.$ordid)
                ->select();
            $this->ajaxReturn($res);

        }else{
            $this->ajaxReturn(null);
        }
    }
    public function getPingjiaDetail(){
        $ordid=I('ordid',0,'intval');
        if(!empty($ordid)){
            $res=M('Pingjia')->where('order_id='.$ordid)->find();
            $this->ajaxReturn($res);
        }else{
            $this->ajaxReturn(null);
        }
    }

    function pubOrder($from_operation){
        $m = M('Order');
        $n = M('Order_detail');
        $s = M('Store');
        $dk = M('Dangkou');

        $uid = session('user_auth.uid');
        $type = I('type');

        if(is_company($uid)){
            $map['founder_id'] = FID;
            $store_id = $s->where($map)->getField('store_id',true);
        }else{
            $store_id = $s->where('uid = '.$uid)->getField('store_id',true);
        }

        if(!empty($store_id)){
            $where['a.status'] = array('in',array(2,3));
            $where['a.store_id'] = array('in',$store_id);
            $where['a.from_operation'] = $from_operation;

            $list = $m->alias('a')
                ->join('LEFT JOIN __USER_ADDRESS__ AS u ON a.address_id = u.address_id')
                ->join('LEFT JOIN __STORE__ AS s ON a.store_id = s.store_id')
                ->join('LEFT JOIN __SEAT_NUM__ AS sn ON a.seat_id = sn.id')
                ->field('a.*,u.username,u.gender,u.phone,u.detail_address,s.store_name,s.tel,s.is_qiantai_print,s.is_kitchen_print,sn.name as seat_name')
                ->where($where)->order('a.xd_time desc')
                ->select();
            foreach($list as $k=>$v){
                $list[$k]['zongjia'] = $v['total']+$v['ps_cost']+$v['canju_total']-$v['yh_price'];
            }
            if(!empty($list)){
                $list_first = $list[0];

                $list_detail = $n->alias('a')
                    ->join('LEFT JOIN __CANMING__ AS c ON a.cm_id = c.cm_id')
                    ->field('a.*,c.cm_name,c.dangkou_id')
                    ->where('order_id = '.$list[0]['order_id'])->select();

                $list_dangkou = $dk->alias('a')
                    ->join('LEFT JOIN __PRINTER__ AS p ON a.dayinji_id = p.id')
                    ->field('a.*,p.printer_sn')
                    ->where('a.uid = '.$uid)->select();



                foreach($list_detail as $k=>$v){
                    if($v['order_id'] == $list[0]['order_id']){
                        $list_first['child'][] = $v;
                    }
                }

                foreach($list_dangkou as $k=>$v){

                    foreach($list_detail as $b){
                        if($v['id'] == $b['dangkou_id']){
                            $list_dangkou[$k]['dangkou'][] = $b;
                            continue;
                        }else{
                            $list_dangkou[$k]['dangkou'][] = null;
                        }
                    }

                    $list_dangkou[$k]['dangkou'] = array_values(array_filter($list_dangkou[$k]['dangkou']));
                    if(empty($list_dangkou[$k]['dangkou'])){
                        unset($list_dangkou[$k]);
                    }

                }

                $list_dangkou = array_values($list_dangkou);

                if($list[0]['status'] == 2){
                    $list_first['status'] = "已完成支付";
                }else if($list[0]['status'] == 3){
                    $list_first['status'] = "货到付款";
                }
                if($list[0]['gender'] == 1){
                    $list_first['gender'] = "先生";
                }else if($list[0]['gender'] == 2){
                    $list_first['gender'] = "女士";
                }
                if($list[0]['pay_type'] == 0){
                    $list_first['pay_type_text'] = "货到付款";
                }else if($list[0]['pay_type'] == 1){
                    $list_first['pay_type_text'] = "在线支付";
                }

                $this->assign('time',time());
                $this->assign('list_first',$list_first);
                $this->assign('list_dangkou',$list_dangkou);

            }
        }


        if($type == 1){
            $list_first['now_time'] = date("m月d日 H:i:s",time());
            $list_first['ticket'] = C('TICKETNAME');
            $list_first['send_time'] = date('Y-m-d H:i',$list_first['send_time']);
            $list_first['xd_time'] = date('Y-m-d H:i',$list_first['xd_time']);
            $list_first['dangkou'] = $list_dangkou;

            $arr[0] = $list;
            $arr[1] = $list_first;

            $this->ajaxReturn($arr);
        }else{
            $this->assign('list',$list);
            $this->assign('_storestr',$store_id);
            $this->assign('is_print',is_print());
            $this->display();
        }
    }
    public function neworder(){
        $f = array('in',array(1,2,3,4));

        $this->pubOrder($f);
    }
    /*
     * 堂室订单
     */
    public function roomorder(){
        $this->pubOrder(5);
    }
    /*
    * 订单详情
    */
    public function orderMore(){
        $id = I('post.id');
        $uid = session('user_auth.uid');
        $source = C('ORDER_SOURCE');

        $m = M('Order');
        $n = M('Order_detail');
        $dk = M('Dangkou');

        $where['a.order_id'] = $id;

        $list = $m->alias('a')
            ->join("LEFT JOIN __USER_ADDRESS__ AS u ON a.address_id = u.address_id")
            ->join('LEFT JOIN __STORE__ AS s ON a.store_id = s.store_id')
            ->join('LEFT JOIN __SEAT_NUM__ AS sn ON a.seat_id = sn.id')
            ->field('a.*,u.username,u.gender,u.phone,u.detail_address,s.store_name,s.tel,s.is_qiantai_print,s.is_kitchen_print,sn.name as seat_name')
            ->where($where)->find();

        $list_detail = $n->alias('a')
            ->join('LEFT JOIN __CANMING__ AS c ON a.cm_id = c.cm_id')
            ->field('a.*,c.cm_name,c.dangkou_id')
            ->where($where)->select();

        $list_dangkou = $dk->alias('a')
            ->join('LEFT JOIN __PRINTER__ AS p ON a.dayinji_id = p.id')
            ->field('a.*,p.printer_sn')
            ->where('a.uid = '.$uid)->select();

        foreach($list_detail as $k=>$v){
            if($v['order_id'] == $list['order_id']){
                $list['child'][] = $v;
            }

        }

        foreach($list_dangkou as $c=>$d){
            foreach($list_detail as $b){
                if($d['id'] == $b['dangkou_id']){
                    $list_dangkou[$c]['dangkou'][] = $b;
                    continue;
                }else{
                    $list_dangkou[$c]['dangkou'][] = null;
                }
            }
            $list_dangkou[$c]['dangkou'] = array_values(array_filter($list_dangkou[$c]['dangkou']));

            if(empty($list_dangkou[$c]['dangkou'])){
                unset($list_dangkou[$c]);
            }
        }

        $list['dangkou'] = array_values($list_dangkou);


        $list['xd_time'] = date('Y-m-d H:i',$list['xd_time']);
        $list['zongjia'] = $list['total']+$list['ps_cost']+$list['canju_total']-$list['yh_price'];
        if($list['send_time'] == 0){
            $list['send_time'] = "立即送达";
        }else{
            $list['send_time'] = date('Y-m-d H:i',$list['send_time']);
        }
        if($list['status'] == 2){
            $list['status'] = "已完成支付";
        }else if($list['status'] == 3){
            $list['status'] = "货到付款";
        }
        if($list['gender'] == 1){
            $list['gender'] = "先生";
        }else if($list['gender'] == 2){
            $list['gender'] = "女士";
        }
        if($list['pay_type'] == 0){
            $list['pay_type_text'] = "货到付款";
        }else if($list['pay_type'] == 1){
            $list['pay_type_text'] = "在线支付";
        }
        if($list['from_operation'] == 4){
            $list['from_operation_text'] = "外卖订单";
        }else if($list['from_operation'] == 5){
            $list['from_operation_text'] = "堂食订单";
        }

        $list['now_time'] = date("m月d日 H:i:s",time());
        $list['ticket'] = C('TICKETNAME');

        $this->ajaxReturn($list);
    }
    /*
     * 新订单商家拒单
     */
    public function judan(){
        $m = M('Order');

        $order_id = I('post.order_id');
        $pay_type = I('post.pay_type');
        $reject_reason = I('post.reject_reason');

        empty($reject_reason) && $this->error('请填写拒单理由');

        $data['reject_reason'] = $reject_reason;
        if($pay_type == 0){
            $data['status'] = 9;
        }else if($pay_type == 1){
            $data['status'] = 10;
        }

        $list = $m->where('order_id = '.$order_id)->setField($data);
        action_log('refuseOrder_action','order',$order_id,is_login());
        if($list > 0){
            $this->success("拒单成功");
        }else{
            $this->error("拒单失败");
        }


    }
    /*
     * 订单接单推送
     */
    public function pushOrder(){
        $id = I('id');
        $type = I('type');
        $or = M('order');
        $au = M('Admin_user');

        $data_order['status'] = 4;
        $data_order['confirm_time'] = time();
        $data_order['jd_status'] = 1;

        $list = $or->where('order_id = '.$id)->setField($data_order);
        action_log('edit_action','order',$id,is_login());
        $orderMsg=$or->alias('a')
            ->join('LEFT JOIN __STORE__ AS s ON a.store_id = s.store_id')
            ->field('a.*,s.store_sn')
            ->where("a.order_id = $id")->find();
        $where['order_id']=$id;
        $where1['address_id']=$orderMsg['address_id'];
        $canming = M('canming')->alias('c')
            ->join('RIGHT JOIN __ORDER_DETAIL__ AS d ON c.cm_id = d.cm_id')
            ->where($where)->field('c.cm_name,c.cm_desc,d.*')->select();
        $add = M('user_address')->where($where1)->find();
        if($orderMsg['from_operation'] != 4){
            $push_msg_id = $au->where('id = '.$orderMsg['uid'])->getField('push_msg_id');
        }
        $push_dispatch_id = M('Store')->where('store_id = '.$orderMsg['store_id'])->getField('dispatch_push_id');

        $orderMsg['order_detail'] = $canming;
        $orderMsg['address'] = $add;

        if($list > 0){
            if($type == 1){
                $post_data=array(
                    'order'=>$orderMsg,
                );
                $diaodu = C('WEBURL');
                $url ='http://'.$diaodu.'/index.php?s=/Dispatch/OrderInfo/orderMsg';
                $postdata = http_build_query($post_data);
                $options = array(
                    'http' => array(
                        'method' => 'POST',//or GET
                        'header' => 'Content-type:application/x-www-form-urlencoded',
                        'content' => $postdata,
                        'timeout' => 15 * 60 // 超时时间（单位:s）
                    )
                );

                $context =stream_context_create($options);
                $result = json_decode(file_get_contents($url, false, $context),true);
                if(!empty($push_dispatch_id)){
                    push_msg_client($push_dispatch_id);
                }

                if($result == true){
                    $or->where('order_id = '.$id)->setField('dispatch_status',1);
                    action_log('edit_action','order',$id,is_login());
                    $data['info'] = "接单并推送成功";
                    $data['value'] = 1;
                }else{
                    $data['info'] = "推送失败";
                    $data['value'] = 0;
                }

            }else if($type == 2){
                $data['info'] = "接单成功";
                $data['value'] = 1;
            }
            if($orderMsg['from_operation'] != 4){
                if(!empty($push_msg_id)){
                    push_msg_client($push_msg_id);
                }

            }
            if(!empty($push_dispatch_id)){
                push_msg_client($push_dispatch_id);
            }
        }else{
            $data['info'] = "接单失败";
            $data['value'] = 0;
        }


        $this->ajaxReturn($data);
    }

    /*
     *堂食订单记录
     */
    public function roomrecord(){
        $m = M('Order');
        $s = M('Store');
        $od = M('Order_detail');
        $ad = M('Admin_user');
        $as =M('Assigned_store');

        $uid = is_login();
        $stime = strtotime(I('stime'));
        $etime = strtotime(I('etime'));
        $order_sn = I('order_sn');
        $status = I('status');
        $pay_type = I('pay_type');
        $send_time = I('send_time');
        $store_id = I('store_id');
        $seat_keyword = I('seat_keyword');
        $today = strtotime(date('Ymd',time()));

        $where['uid'] = $where_as['user_id'] = $uid;

        $as_count = $as->where($where_as)->count();

        if(is_company($uid)){
            if(empty($store_id)){
                $map['founder_id'] = FID;
                $new_store_id = $s->where($map)->getField('store_id',true);
                if(!empty($new_store_id)){
                    $store_id = $new_store_id;
                }else{
                    $store_id = null;
                }
            }
        }else if($as_count > 0){
            if(empty($store_id)){
                $new_store_id = $s->where($where)->getField('store_id',true);
                if(!empty($new_store_id)){
                    $as_store_id = $as->where($where_as)->getField('store_id',true);
                    $as_store_id = array_unique(array_merge($as_store_id,$new_store_id));
                }else{
                    $as_store_id = $as->where($where_as)->getField('store_id',true);
                }
                $store_id = $as_store_id;
            }
        }else{
            $store_id = $s->where($where)->getField('store_id');
        }

        if(!empty($store_id)){
            if(is_company($uid)){
                $map['founder_id'] = FID;
                $new_store_id = $s->where($map)->getField('store_id',true);
                $data2['a.store_id'] = array('in',$new_store_id);
            }else if($as_count > 0){
                $new_store_id = $s->where($where)->getField('store_id',true);
                if(!empty($new_store_id)){
                    $as_store_id = $as->where($where_as)->getField('store_id',true);
                    $as_store_id = array_unique(array_merge($as_store_id,$new_store_id));
                }else{
                    $as_store_id = $as->where($where_as)->getField('store_id',true);
                }
                $data2['a.store_id'] = array('in',$as_store_id);
            }else{
                $data2['a.store_id'] = $store_id;
            }
            $data['a.store_id'] = $store_id;

            $list_store = $s->alias('a')->where($data2)->select();   //根据当前登录uid查询店铺名称

            $qiantai = $s->where($where)->getField('is_qiantai_print');

            if(!empty($stime) && !empty($etime)){
                $data['a.xd_time'] = array('between',array($stime,$etime));
            }else{
                $data['a.xd_time'] = array('between',array($today,$today+86400));
            }
            if(!empty($order_sn)){
                $data['a.order_sn'] = array('like','%'.$order_sn.'%');
            }
            if(!empty($status)){
                $data['a.status'] = $status;
            }else{
                $data['a.status'] = array('in',array(3,5));
            }
            if(!empty($pay_type)){
                $data['a.pay_type'] = $pay_type;
            }
            if(!empty($send_time)){
                if($send_time == 1){
                    $data['a.send_time'] = array('not between',array($today,$today+86400));
                }else if($send_time == 2){
                    $data['a.send_time'] = array('between',array($today,$today+86400));
                }

            }
            if(!empty($seat_keyword)){
                $data['s.name|s.seat_sn'] = array('like','%'.$seat_keyword.'%');
            }

            $data['a.store_id'] = array('in',$store_id);
            $data['a.from_operation'] = 5;


            $count = $m->alias('a')->join('LEFT JOIN __SEAT_NUM__ AS s ON a.seat_id = s.id')->where($data)->count();
            $sum_total = $m->alias('a')->join('LEFT JOIN __SEAT_NUM__ AS s ON a.seat_id = s.id')->where($data)->sum('total');
            $sum_real = $m->alias('a')->join('LEFT JOIN __SEAT_NUM__ AS s ON a.seat_id = s.id')->where($data)->sum('real_money');
            $Page = new \Think\Page($count,5);
            //设置分页显示方式

            $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
            $Page->setConfig('first','首页');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('last','末页');
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
            $Page->lastSuffix = false;//分页最后的总页数不显示

            $show = $Page->show();

            $list_detail = $od->alias('a')
                ->join('LEFT JOIN __CANMING__ AS c ON a.cm_id = c.cm_id')
                ->field('a.*,c.cm_name')
                ->select();


            $list = $m->alias('a')
                ->join('LEFT JOIN __SEAT_NUM__ AS s ON a.seat_id = s.id')
                ->field('a.*,s.name as seat_name')
                ->where($data)->order('a.xd_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();

            foreach($list as $k=>$v){
                foreach($list_detail as $a=>$b){
                    if($v['order_id'] == $b['order_id']){
                        $list[$k]['child'][] = $b;
                    }
                }
                $list[$k]['zhaoling'] = number_format($v['real_money']-($v['total']-$v['yh_price']),2);
            }

            $this->assign('stime',$stime);
            $this->assign('etime',$etime);
            $this->assign('order_sn',$order_sn);
            $this->assign('status',$status);
            $this->assign('pay_type',$pay_type);
            $this->assign('send_time',$send_time);
            $this->assign('store_id',$store_id);
            $this->assign('count',$count);
            $this->assign('list',$list);
            $this->assign('page',$show);
            $this->assign('sum_total',$sum_total);
            $this->assign('sum_real',$sum_real);
            $this->assign('qiantai',$qiantai);
            $this->assign('seat_keyword',$seat_keyword);
            $this->assign('store',$list_store);

        }else{
            $this->assign('store_id',null);
        }
        $this->assign('is_print',is_print());
        $this->display();
    }

    /*
     * 改变菜品
     */
    public function changeCai(){
        $m = M('Order');
        $od = M('Order_detail');

        $cm_id = I('cm_id');
        $order_id = I('order_id');
        $quantity = I('quantity');
        $price = I('price');
        $addType = I('addType');
        $arr = array();

        $where['order_id'] = $order_id;
        $where['cm_id'] = $cm_id;

        $data['quantity'] = $quantity;
        $data['total_money'] = number_format($quantity*$price,2,'.','');

        if(($addType == 2)){
            if($quantity == 0){
                if(!empty($order_id) && !empty($cm_id)){
                    $cm_price = $od->where($where)->getField('total_money');
                    $m->where('order_id = '.$order_id)->setDec('total',$cm_price);
                    action_log('edit_action','Order',$order_id,is_login());
                    $total = $m->where('order_id = '.$order_id)->getField('total');

                    $arr['cm_id'] = $cm_id;
                    $arr['order_id'] = $order_id;
                    $arr['status'] = -1;
                    $arr['total'] = $total;
                    $od->where($where)->delete();
                    action_log('delete_action','Order_detail',$cm_id,is_login());
                }else{
                    $arr['status'] = 0;
                    $arr['text'] = '订单id或者菜品id为空';
                }

                $this->ajaxReturn($arr);
            }else{
                $od->where($where)->setField($data);
                action_log('edit_action','Order_detail',$cm_id,is_login());
            }
        }else if($addType == 1){
            $is_cm = $od->where($where)->count();
            if($is_cm > 0){
                $od->where($where)->setInc('quantity',$quantity);
                $od->where($where)->setInc('total_money',$data['total_money']);
                action_log('edit_action','Order_detail',$cm_id,is_login());
            }else{
                $data['xd_time'] = $m->where('order_id = '.$order_id)->getField('xd_time');
                $data['order_id'] = $order_id;
                $data['cm_id'] = $cm_id;
                $data['price'] = $price;

                $od->data($data)->add();
                action_log('add_action','Order_detail',$cm_id,is_login());
            }

        }

        $total_money = $od->where('order_id = '.$order_id)->sum('total_money');
        $total = $m->where('order_id = '.$order_id)->setField('total',$total_money);
        action_log('edit_action','Order',$order_id,is_login());
        if($total > 0){
            if($addType == 2){
                $arr['total'] = $total_money;
                $arr['total_money'] = $data['total_money'];
                $arr['status'] = 1;
                $this->ajaxReturn($arr);
            }else if($addType == 1){
                $where1['a.order_id'] = $order_id;
                $where1['a.cm_id'] = $cm_id;
                $list = $od->alias('a')
                    ->join('LEFT JOIN __CANMING__ AS c ON a.cm_id = c.cm_id')
                    ->field('a.*,c.cm_name')
                    ->where($where1)->find();
                $list['total'] = $total_money;
                $list['status'] = $m->where('order_id = '.$order_id)->getField('status');
                $this->ajaxReturn($list);
            }
        }else{
            $arr['status'] = 0;
            $arr['text'] = '未知错误';
            $this->ajaxReturn($arr);
        }

    }
    /*
     * 查询菜品
     */
    public function selectCai(){
        $m = M('Canming');
        $n = M('Store');

        $uid = is_login();
        $keyword = I('keyword');

        $store_id = $n->where('uid = '.$uid)->getField('store_id');

        $where['store_id'] = $store_id;
        $where['cm_name_py|cm_name'] = array('like','%'.$keyword.'%');

        $list = $m->where($where)->select();

        $this->ajaxReturn($list);

    }

    public function getLocalNum()
    {
        $map['user_id'] = $map1['user_id'] = session('user_auth.uid');
        $map['status'] = $map1['status'] = 1;
        $map['printerto_local'] = 1;
        $num = M('Printer')->where($map)->find();
        if ($num) {
            $this->ajaxReturn(array('printer_sn' => $num['printer_sn'], 'nums' => $num['printer_local_num']));
        }else{
            $nums = M('Printer')->where($map1)->getField('printer_sn');
            $this->ajaxReturn(array('printer_sn' => $nums['printer_sn'], 'nums' =>1));
        }

    }
    //删除订单
    public function del(){

        $datatable=I('post.datatable');
        $id = I('post.order_id');
//        $this->ajaxReturn($id);
        $n=M($datatable);
        //判断id是否是数组类型
        if (is_array($id)) {
            //执行批量删除操作
            $where = 'order_id in (' . implode(',', $id) . ')';
        } else {
            //执行单挑删除操作
            $where = 'order_id =' . $id;
        }
        $list = $n->where($where)->delete();
        action_log('delete_action',$datatable,$id,is_login());
        if ($list > 0) {
            $save['delete'] = "删除成功，无法找回！";
            $save['status'] = 1;
        } else {
            $save['delete'] = "删除失败";
            $save['status'] = 0;
        }
        $this->ajaxReturn($save);
    }

}
