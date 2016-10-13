<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-11-24
 * Time: 13:59
 */
namespace ReceiveOrder\Controller;
use Admin\Controller\AdminController;
class FastfoodController extends AdminController{

    protected function _initialize(){
        parent::_initialize();
        if(!is_administrator())
            A('Admin/Contract')->ispayedCheck();
    }
    public function index(){
        $m = M('Canpin');
        $n = M('Canming');
        $s = M('Store');
        $sl = M('Seat_location');
        $seat_id = I('seat_id');
        $seat_name = I('seat_name');

        $shop_id = I('store_id');
        $uid = session('user_auth.uid');

        $store_id = $s->where('uid = '.$uid)->getField('store_id',true);
        $store_id = implode(',',$store_id);
        $list_location = $sl->where('uid = '.$uid)->select();

        if(!empty($store_id)){
            $list_s = $s->where('store_id in ('.$store_id.')')->select();
            if(empty($shop_id)){
                $shop_id = $list_s[0]['store_id'];
            }
            $list = $m->where('store_id = '.$shop_id)->select();
            $list_cm = $n->where('store_id = '.$shop_id)->select();
        }

        $this->assign('list_cm',$list_cm);
        $this->assign('list',$list);
        $this->assign('shop_id',$shop_id);
        $this->assign('list_s',$list_s);
        $this->assign('time',time());
        $this->assign('list_location',$list_location);
        $this->assign('remarks',C('ORDER_REMARKS'));
        $this->assign('source',C('ORDER_SOURCE'));
        $this->assign('seat_id',$seat_id);
        $this->assign('seat_name',$seat_name);
        $this->display();
    }
    /*
     * 堂食下单
     */
    public function setOrder(){
        C('TOKEN_ON',false);
        $n = M('Order');
        $od = M('Order_detail');
        $gs = M('Goods_count');
        $sn = M('Seat_num');
        $s = M('Store');

        $cm_id = I('post.ids');
        $num = I('post.num');
        $newdj = I('post.newdj');
        $newzj = I('post.newzj');
        $store_id = I('post.store_id');
        $seat_id = I('seat_id');

        $n->startTrans();

        $now_time = strtotime(date("Y-m-d",time()));
        $order_count_where['xd_time'] = array('between',array($now_time,$now_time+86400));
        $order_count_where['from_operation'] = 5;
        $order_count_where['store_id'] = $store_id;
        $order_count = $n->where($order_count_where)->count();
        $count = $order_count+1;

        $store_sn = $s->where('store_id = '.$store_id)->getField('store_sn');

        if($n->create()){
            $n->uid = session('user_auth.uid');
            $n->order_sn = createOrdersn();
            $n->xd_time = time();
            $n->status = 3;
            $n->pay_type = 0;
            $n->send_time = time();
            $n->from_operation = 5;
            $n->order_count = $count;
            $n->store_sn = $store_sn;
            $n->founder_id = FID;
            $order = $n->add();

            $list_od = $n->where('order_id = '.$order)->find();
            $new_num = 0;
            for($i=0;$i<count($cm_id);$i++){
                $arr[$i]['order_id'] = $order;
                $arr[$i]['cm_id'] = $cm_id[$i];
                $arr[$i]['price'] = $newdj[$i];
                $arr[$i]['quantity'] = $num[$i];
                $arr[$i]['total_money'] = $newzj[$i];
                $arr[$i]['xd_time'] = $list_od['xd_time'];

                $new_num += $this->updateGoodsSale($gs,$store_id,$cm_id[$i],$num[$i]);//菜品数量写入统计表

            }
            $list_d = $od->addAll($arr);
            if(!empty($seat_id)){
                $seat_num = $sn->where('id = '.$seat_id)->setField('status',1);//订单成功以后更新桌台状态
            }

            if($new_num == count($cm_id) && ($seat_num > 0) && ($list_d > 0) && ($order > 0)){
                $n->commit();
                action_log('planOrder_action','Order,Seat_num',$order.",".$seat_id,is_login());
                $this->ajaxReturn(1);
            }else{
                $n->rollback();
                $this->ajaxReturn(0);
            }
        }else{
            $this->error($n->getError());
        }

    }
    /*
     * 外卖下单
     */
    public function setUser(){
        C('TOKEN_ON',false);
        $m = M('User_address');
        $n = M('Order');
        $od = M('Order_detail');
        $s = M('Store');
        $gs = M('Goods_count');
        $cm_id = I('post.ids');
        $num = I('post.num');
        $newdj = I('post.newdj');
        $newzj = I('post.newzj');
        $phone = I('post.phone');
        $store_id = I('post.store_id');
        $canju_total = I('post.canju_total');
        $send_time = I('post.send_time');

        $detail_address = I('post.detail_address');

        $store_sn = $s->where('store_id = '.$store_id)->getField('store_sn');

        $now_time = strtotime(date("Y-m-d",time()));
        $order_count_where['xd_time'] = array('between',array($now_time,$now_time+86400));
        $order_count_where['from_operation'] = array('in',array(1,2,3,4));
        $order_count_where['store_id'] = $store_id;
        $order_count = $n->where($order_count_where)->count();
        $count = $order_count+1;

        $m->startTrans();

        if($m->create() && $n->create()){

            $m->uid = session('user_auth.uid');

            $where_address['phone'] = $phone;
            $where_address['detail_address'] = $detail_address;

            $user_address = $m->where($where_address)->find();

            if($user_address <= 0){
                $list = $m->add();
            }else{
                /*$data['username'] = I('post.username');
                $data['gender'] = I('post.gender');
                $data['detail_address'] = I('post.detail_address');

                $m->where($where_address)->save($data);*/

                $list = $user_address['address_id'];
            }

            $n->uid = session('user_auth.uid');
            $n->order_sn = createOrdersn();
            $n->xd_time = time();
            $n->status = 3;
            $n->pay_type = 0;
            if($send_time != "立即送达"){
                $send_time = strtotime($send_time);
            }else{
                $send_time = 0;
            }
            $n->send_time = $send_time;
            $n->sh_mode = 2;
            $n->from_operation = 4;
            $n->address_id = $list;
            $n->canju_total = round($canju_total,2);
            $n->order_count = $count;
            $n->store_sn = $store_sn;
            $n->founder_id = FID;
            $order = $n->add();

            $list_od = $n->where('order_id = '.$order)->find();
            $new_num = 0;
            for($i=0;$i<count($cm_id);$i++){
                $arr[$i]['order_id'] = $order;
                $arr[$i]['cm_id'] = $cm_id[$i];
                $arr[$i]['price'] = $newdj[$i];
                $arr[$i]['quantity'] = $num[$i];
                $arr[$i]['total_money'] = $newzj[$i];
                $arr[$i]['xd_time'] = $list_od['xd_time'];

                $new_num += $this->updateGoodsSale($gs,$store_id,$cm_id[$i],$num[$i]);//菜品数量写入统计表

            }
            $list_d = $od->addAll($arr);

            if($new_num == count($cm_id) && ($list > 0) && ($list_d > 0) && ($order > 0)){
                $m->commit();
                action_log('planOrder_action','User_address,Order',$list.",".$order,is_login());
                $this->ajaxReturn(1);
            }else{
                $m->rollback();
                $this->ajaxReturn(0);
            }
        }else{
            $this->ajaxReturn(0);
        }
    }
    /*
     * 菜品写入统计表
     */
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
    /*
     * 订单结账
     */
    public function checkOut(){
        $m = M('Order');
        $n = M('Seat_num');
        $order_id = I('order_id');
        $yh_price = I('yh_price');
        $seat_id = I('seat_id');
        $real_money_customer = I('real_money_customer');
        $total = I('total');

        $m->startTrans();

        empty($real_money_customer) && $this->error('请输入实收金额');
        !preg_match("/^[0-9]+(\.[0-9]{1,2})*$/",$real_money_customer) && $this->error('实收格式不正确');
        if(!empty($yh_price)){
            !preg_match("/^[0-9]+(\.[0-9]{1,2})*$/",$yh_price) && $this->error('减免格式不正确');
        }

        $data['real_money'] = $real_money_customer;
        $data['yh_price'] = $yh_price;
        $data['status'] = 5;

        $list = $m->where('order_id = '.$order_id)->setField($data);
        $list_sn = $n->where('id = '.$seat_id)->setField('status',0);

        if($list > 0 && $list_sn > 0){
            $m->commit();
            action_log('checkOut_action','Order,Seat_num',$order_id.",".$seat_id,is_login());
            $this->success('结账成功',U('selectSeat'));
        }else{
            $m->rollback();
            $this->error('结账失败');
        }

    }

    public function lit(){
        $kanjia = M('kanjia');
        $list = $kanjia->alias('A')->join("LEFT JOIN wm_kanjiaren AS B ON A.user_id  = B.uid ")
            ->join('LEFT JOIN wm_store AS C ON A.store_id = C.store_id')
            ->join('LEFT JOIN wm_kanjia_admin AS D ON A.kj_id = D.kj_id')
            ->where("A.kanjia_id = B.kanjia_id")
            ->field('A.*,B.kanjia_price,B.nickname,B.headimgurl,B.kj_time,C.store_name,D.tc_name')->select();
        $this->assign('list',$list);

        $this->display();
    }
    public function del(){
        $map['kanjia_id'] = I('kanjia_id');
        if(M('kanjia')->where($map)->delete() && M('kanjiaren')->where($map)->delete()){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
    public function lei(){
        $id = I('id');
        $kanjia = M('kanjia');
        $cai = $kanjia->alias('a')->join("LEFT JOIN wm_kanjia_detail as b on a.kj_id = b.kanjia_id")
            ->join("LEFT JOIN wm_canming as c on b.cm_id = c.cm_id")
            ->join('LEFT JOIN wm_store as d on c.store_id = d.store_id')
            ->where('a.kanjia_id ='.$id)
            ->field('b.price,b.quantity,b.total_money,c.cm_name,c.danwei,c.big_img,d.store_name')->select();
        $this->ajaxReturn($cai);
    }
    /*
     * 查询菜品
     */
    public function findFood(){
        $keyword = I('post.keyword');
        $store_id = I('post.store_id');

        $m = M('Canming');

        $where['store_id'] = $store_id;
        $where['cm_name_py'] = array('like','%'.$keyword.'%');

        $list = $m->alias('a')
            ->join('LEFT JOIN wm_shangpin AS s ON a.sp_id = s.sp_id')
            ->field('a.*,s.sp_danjia')
            ->where($where)->select();

        $this->ajaxReturn($list);
    }
    /*
     * 查寻桌台
     */
    public function findSeat(){
        $m = M('Seat_num');

        $uid = is_login();
        $keyword = I('keyword');

        $where['uid'] = $uid;
        $where['status'] = 0;
        if(!empty($keyword)){
            $where['name|seat_sn'] = array('like','%'.$keyword.'%');
        }

        $list = $m->where($where)->select();

        $this->ajaxReturn($list);

    }
    /*
     * 选择桌台
     */
    public function selectSeat(){
        $m = M('Seat_location');
        $n = M('Seat_num');

        $uid = is_login();

        $list_location = $m->alias('a')->where('uid = '.$uid)->select();
        $list_num = $n->where('uid = '.$uid)->select();

        $this->assign('list_location',$list_location);
        $this->assign('list_num',$list_num);
        $this->assign('is_print',is_print());
        $this->display();
    }
    //批量添加菜品，请勿随意执行
    /*public function setGoods(){
        $m = M('Canming');
        $list = $m->where('store_id = 1')->select();

        foreach($list as $k=>$v){
            $list[$k]['store_id'] = '10';
            $list[$k]['cm_id'] = '';
        }
        foreach($list as $k=>$v){
            $dataList[] = $v;
        }
        $add = $m->addAll($dataList);
        var_dump($add);

    }*/

    /*
    * 订单详情
    */
    public function orderMore(){
        $m = M('Order');
        $n = M('Order_detail');
        $dk = M('Dangkou');
        $seat_id = I('post.seat_id');
        $seat_name = I('post.seat_name');
        $uid = is_login();

        $where['a.seat_id'] = $seat_id;
        $where['a.status'] = 3;
        $where['a.from_operation'] = 5;

        $list = $m->alias('a')
            ->join("LEFT JOIN __USER_ADDRESS__ AS u ON a.address_id = u.address_id")
            ->join('LEFT JOIN __STORE__ AS s ON a.store_id = s.store_id')
            ->field('a.*,u.username,u.gender,u.phone,u.detail_address,s.store_name,s.tel,s.is_qiantai_print,s.is_kitchen_print')
            ->where($where)->find();

        if($list != null){
            $detail_where['order_id'] = $list['order_id'];

            $list_detail = $n->alias('a')
                ->join('LEFT JOIN __CANMING__ AS c ON a.cm_id = c.cm_id')
                ->field('a.*,c.cm_name,c.dangkou_id')
                ->where($detail_where)->select();



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

            $list['seat_name'] = $seat_name;
            $list['zongjia'] = $list['total'];
            $list['xd_time'] = date('Y-m-d H:i:s',$list['xd_time']);
            $list['now_time'] = date("m月d日 H:i:s",time());
            $list['ticket'] = C('TICKETNAME');
        }

        $this->ajaxReturn($list);
    }

    public function getLocalNum(){
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
    public function getPerson($phone){
        if(!empty($phone)){
            $map['phone'] = $phone;
            $res = M('user_address')->where($map)->select();
            empty($res) && $res = null;
        }else{
            $res = null;
        }
        $this->ajaxReturn($res);
    }
}