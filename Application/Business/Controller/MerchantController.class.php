<?php
namespace Business\Controller;

use Common\Controller\CommonController;

class MerchantController extends CommonController{


    protected function _initialize() {

    }
    
    public function login_post(){

        $user = I('post.post_user');
        $pass = I('post.post_pass');
        $login_ret = $this->login($user,$pass);
        if ($login_ret['id'] > -1) {

            $data['uid'] = $login_ret['id'];
            $Store = M('store');
            $ret_store = $Store->where($data)->find();
            if($ret_store == null){
                $error_data['id'] = -1;
                $error_data['error_data'] = '读取商家信息出错!';
            }
            $admin_user = M('admin_user');
            $dd['id'] = $login_ret['id'];;
            $ret_user = $admin_user->where($dd)->find();
            if($ret_user == null){
                $error_data['id'] = -1;
                $error_data['error_data'] = '用户信息读取信息出错!';
            }

            $data['id'] = $login_ret['id'];
            $data['store_id'] = $ret_store['store_id'];
            $data['store_name'] = $ret_store['store_name'];
            $data['phone'] = $ret_store['phone'];
            $data['push_msg_id'] = $ret_user['push_msg_id'];

            action_log("user_login", "user", $login_ret['id'], $login_ret['id']);//para1:行为名，para2:触发行为的表，para3:操作行为的数据id,para4:触发行为的用户id

            $this->ajaxReturn($data);

        } else {

            $error_data['id'] = $login_ret['id'];
            $error_data['error_data'] = $login_ret['error_data'];
            $this->ajaxReturn($error_data);

        }


    }


    //用户登陆内部方法
    public function login($username, $password, $map=null) {
        //去除前后空格
        $username = trim($username);

        //匹配登录方式
        if (preg_match("/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/", $username)) {
            $map['email'] = array('eq', $username);     // 邮箱登陆
        } elseif (preg_match("/^1\d{10}$/", $username)) {
            $map['mobile'] = array('eq', $username);    // 手机号登陆
        } else {
            $map['username'] = array('eq', $username);  // 用户名登陆
        }

        $map['status']   = array('eq', 1);
        $Users = M('admin_user');
        $user_info = $Users->where($map)->find(); //查找用户
        if (!$user_info) {

            $data['id'] = -1;
            $data['error_data'] = '用户不存在或被禁用！';

            return $data;

        } else {
            if (user_md5($password) !== $user_info['password']) {

                $data['id'] = -1;
                $data['error_data'] = '密码错误！';

                return $data;

            } else {

                $data['id'] = $user_info['id'];

                return $data;
            }
        }
    }
    

    //订单列表
    public function get_order_text(){
        $uid = I('post.uid'); //商家uid
        $store_id = I('post.store_id'); //店铺id
        $order_id = I('post.order_id'); // 1新任务  2未完成  3已完成 4已取消
        if($order_id == 1){
            $data['a.status'] = array('in','2,3');
            $data['a.xd_time'] = array(array('gt',strtotime(date('Y-m-d'))),array('lt',strtotime(date('Y-m-d',strtotime('+1 day')))),'and');
        }elseif($order_id == 2){
            $data['a.status'] = array('in','4');
            $data['a.xd_time'] = array(array('gt',strtotime(date('Y-m-d'))),array('lt',strtotime(date('Y-m-d',strtotime('+1 day')))),'and');
        }elseif($order_id == 3){
            $data['a.status'] = array('in','5,6');
            $data['a.xd_time'] = array(array('gt',strtotime(date('Y-m-d'))),array('lt',strtotime(date('Y-m-d',strtotime('+1 day')))),'and');
        }elseif($order_id == 4){
            $data['a.status'] = array('in','7,8,9,10,11,12,13,15,16,17,18');
            $data['a.xd_time'] = array(array('gt',strtotime(date('Y-m-d'))),array('lt',strtotime(date('Y-m-d',strtotime('+1 day')))),'and');
        }elseif($order_id == 5){
            $data['a.status'] = array('in','5,6');
            $data['a.xd_time'] = array(array('gt',strtotime(date('Y-m-d'))),array('lt',strtotime(date('Y-m-d',strtotime('+1 day')))),'and');
        }elseif($order_id == 6){
            $data['a.status'] = array('in','5,6');
            $data['a.xd_time'] = array(array('gt',strtotime(date('Y-m-d',strtotime('-1 day')))),array('lt',strtotime(date('Y-m-d'))),'and');
        }elseif($order_id == 7){
            $data['a.status'] = array('in','5,6');
            $data['a.xd_time'] = array(array('gt',strtotime(date('Y-m-d',strtotime('-2 day')))),array('lt',strtotime(date('Y-m-d',strtotime('-1 day')))),'and');
        }

        $data['a.store_id'] = $store_id;
        $Order = M('order');
        $ret = $Order->alias('a')
            ->join('LEFT JOIN wm_user_address as b ON a.address_id = b.address_id')
            ->field('a.*,b.*')
            ->where($data)->order('a.order_id DESC')->select();
        $array = Array();
        foreach($ret as $s1){
            if($s1['song_time_data'] != 0){

                $s1['song_time_data'] = date("Y-m-d H:i:s", $s1['send_time']);

            }else{
                $s1['song_time_data'] = "立即送达";
            }

            $s1['xia_time_data'] = date("Y-m-d H:i:s", $s1['xd_time']);
            $array[] = $s1;
        }


        $this->ajaxReturn($array);
    }

    //新任务获取当前订单详情
    public function get_order_details(){

        $oid = I('post.oid'); //订单id主键
        $data['a.order_id'] = $oid;
        $Order = M('order');
        $ret = $Order->alias('a')
            ->join('LEFT JOIN wm_order_detail as b ON a.order_id = b.order_id')
            ->join('LEFT JOIN wm_canming as c ON b.cm_id = c.cm_id')
            ->field('a.*,b.*,c.cm_name')
            ->where($data)->select();
        $this->ajaxReturn($ret);


    }

    //拒单
    public function set_judan(){
        $uid = I('post.uid'); //商家uid
        $oid = I('post.oid'); //订单id主键
        $code = I('post.code');
        $pay_type = I('post.pay_type');
        $data['order_id'] = $oid;
        $data['reject_reason'] = $code;
        $data['reject_time'] = time();

        if($pay_type == 0){
            $data['status'] = 9;
        }else{
            $data['status'] = 10;
        }
        $Order = M('order');


        $uid = $Order->where(array("order_id"=>$oid))->getField('uid');
        $admin_user = M('member_user');
        $push_msg_id = $admin_user->where(array("id"=>$uid))->getField('push_msg_id');
        if(strlen($push_msg_id) > 5){

            $ret = $Order->save($data);
            if($ret > 0){
                action_log("receivingEvent_action", "order", $oid, $uid);//para1:行为名，para2:触发行为的表，para3:操作行为的数据id,para4:触发行为的用户id

                push_msg_client($push_msg_id);

                $data['id'] = 1;
                $data['code'] = '拒单成功';
                $this->ajaxReturn($data);

            }else{
                $data['id'] = -1;
                $data['code'] = '拒单失败';
                $this->ajaxReturn($data);
            }


        }else{
            $data['id'] = -1;
            $data['code'] = '拒单失败';
            $this->ajaxReturn($data);
        }

    }

    //新任务接单
    public function set_jiedan(){
        $uid = I('post.uid'); //商家uid
        $oid = I('post.oid'); //订单id主键
        $data['order_id'] = $oid;
        $data['confirm_time'] = time();
        $data['status'] = 4;
        $Order = M('order');

        $uid = $Order->where(array("order_id"=>$oid))->getField('uid');
        $admin_user = M('member_user');
        $push_msg_id = $admin_user->where(array("id"=>$uid))->getField('push_msg_id');
        if(strlen($push_msg_id) > 5){

            $ret = $Order->save($data);
            if($ret > 0){
                action_log("receivingEvent_action", "order", $oid, $uid);//para1:行为名，para2:触发行为的表，para3:操作行为的数据id,para4:触发行为的用户id

                push_msg_client($push_msg_id);

                $data['id'] = 1;
                $data['code'] = '接单成功';
                $this->ajaxReturn($data);

            }else{
                $data['id'] = -1;
                $data['code'] = '接单失败';
                $this->ajaxReturn($data);
            }


        }else{
            $data['id'] = -1;
            $data['code'] = '接单失败';
            $this->ajaxReturn($data);
        }





    }

    //未完成 完成订单
    public function set_wancheng(){
        $uid = I('post.uid'); //商家uid
        $oid = I('post.oid'); //订单id主键
        $data['order_id'] = $oid;
        $data['complete_time'] = time();
        $data['status'] = 5;
        $Order = M('order');


        $uid = $Order->where(array("order_id"=>$oid))->getField('uid');
        $admin_user = M('member_user');
        $push_msg_id = $admin_user->where(array("id"=>$uid))->getField('push_msg_id');
        if(strlen($push_msg_id) > 5){

            $ret = $Order->save($data);
            if($ret > 0){
                action_log("receivingEvent_action", "order", $oid, $uid);//para1:行为名，para2:触发行为的表，para3:操作行为的数据id,para4:触发行为的用户id

                push_msg_client($push_msg_id);

                $data['id'] = 1;
                $data['code'] = '操作成功';
                $this->ajaxReturn($data);

            }else{
                $data['id'] = -1;
                $data['code'] = '操作失败';
                $this->ajaxReturn($data);
            }


        }else{
            $data['id'] = -1;
            $data['code'] = '操作失败';
            $this->ajaxReturn($data);
        }


    }


    //抽屉统计
    public function get_order_number(){

        $Order = M('order');
        $store_id = I('post.store_id'); //店铺id
        $dd['store_id'] = $store_id;
        $dd['xd_time'] = array(array('gt',strtotime(date('Y-m-d'))),array('lt',strtotime(date('Y-m-d',strtotime('+1 day')))),'and');
        $dd['status'] = array('in','2,3');
        $ret_new = $Order->where($dd)->Count();

        $dd['store_id'] = $store_id;
        $dd['xd_time'] = array(array('gt',strtotime(date('Y-m-d'))),array('lt',strtotime(date('Y-m-d',strtotime('+1 day')))),'and');
        $dd['status'] = array('in','4');
        $ret_not = $Order->where($dd)->Count();

        $dd['store_id'] = $store_id;
        $dd['xd_time'] = array(array('gt',strtotime(date('Y-m-d'))),array('lt',strtotime(date('Y-m-d',strtotime('+1 day')))),'and');
        $dd['status'] = array('in','5,6');
        $ret_finish = $Order->where($dd)->Count();


        $dd['store_id'] = $store_id;
        $dd['xd_time'] = array(array('gt',strtotime(date('Y-m-d',strtotime('-1 day')))),array('lt',strtotime(date('Y-m-d'))),'and');
        $dd['status'] = array('in','5,6');
        $ret_yesterday = $Order->where($dd)->Count();

        //取前天
//        $dd['store_id'] = $store_id;
//        $dd['xd_time'] = array(array('gt',(strtotime(date('Y-m-d'))-2*24*60*60)),array('lt',strtotime(date('Y-m-d')-24*60*60)),'and');
//        $dd['status'] = array('in','5,6');
//        $ret_ago = $Order->where($dd)->Count();
        $dd['store_id'] = $store_id;
        $dd['xd_time'] = array(array('gt',strtotime(date('Y-m-d',strtotime('-2 day')))),array('lt',strtotime(date('Y-m-d',strtotime('-1 day')))),'and');
        $dd['status'] = array('in','5,6');
        $ret_ago = $Order->where($dd)->Count();




        //取本月订单数量
        $dd['store_id'] = $store_id;
        $dd['xd_time'] = array(array('gt',date('Y-m-01', strtotime(date("Y-m-d")))),array('lt',time()),'and');
        $dd['status'] = array('in','5,6');
        $this_month = $Order->where($dd)->Count();

        //取上月订单数量
        $dd['store_id'] = $store_id;
        $dd['xd_time'] = array(array('gt',date('Y-m-01', strtotime('-1 month'))),array('lt',date('Y-m-t', strtotime('-1 month'))),'and');
        $dd['status'] = array('in','5,6');
        $last_month = $Order->where($dd)->Count();

        //取本季订单数量
        $season = ceil((date('n'))/3);//当月是第几季度
        $dd['store_id'] = $store_id;
        $dd['xd_time'] = array(array('gt',date('Y-m-d H:i:s', mktime(0, 0, 0,$season*3-3+1,1,date('Y')))),array('lt',time()),'and');
        $dd['status'] = array('in','5,6');
        $this_season = $Order->where($dd)->Count();


        $ret_data['new'] = $ret_new;
        $ret_data['not'] = $ret_not;
        $ret_data['finish'] = $ret_finish;
        $ret_data['yesterday'] = $ret_yesterday;
        $ret_data['ago'] = $ret_ago;

        $ret_data['this_month'] = $this_month;
        $ret_data['last_month'] = $last_month;
        $ret_data['this_season'] = $this_season;

        $this->ajaxReturn($ret_data);

    }

    //统计
    public function get_statistics(){

        $store_id = I('post.store_id'); //店铺id

        $Order = M('order');
        //取本月订单数量
        $dd['store_id'] = $store_id;
        $dd['xd_time'] = array(array('gt',date('Y-m-01', strtotime(date("Y-m-d")))),array('lt',time()),'and');
        $dd['status'] = array('in','5,6');
        $this_month = $Order->where($dd)->Count();
        $this_month_day =number_format(($this_month/30),1);


        //取上月订单数量
        $dd['store_id'] = $store_id;
        $dd['xd_time'] = array(array('gt',date('Y-m-01', strtotime('-1 month'))),array('lt',date('Y-m-t', strtotime('-1 month'))),'and');
        $dd['status'] = array('in','5,6');
        $last_month = $Order->where($dd)->Count();
        $last_month_day =number_format(($last_month/30),1);


        //取本季订单数量
        $season = ceil((date('n'))/3);//当月是第几季度
        $dd['store_id'] = $store_id;
        $dd['xd_time'] = array(array('gt',date('Y-m-d H:i:s', mktime(0, 0, 0,$season*3-3+1,1,date('Y')))),array('lt',time()),'and');
        $dd['status'] = array('in','5,6');
        $this_season = $Order->where($dd)->Count();
        $this_season_day =number_format(($this_season/30),1);

        $data = array();

        $data['this_month'] = $this_month;
        $data['this_month_day'] = $this_month_day;
        $data['last_month'] = $last_month;
        $data['last_month_day'] = $last_month_day;
        $data['this_season'] = $this_season;
        $data['this_season_day'] = $this_season_day;

        $this->ajaxReturn($data);

    }

    //下载app
    public function downloadsjd(){
        $this->display('Merchant/downloadPsd');
    }

    //安卓原生循环监听新订单是否接单
    public function is_new_order(){

        $store_id = I('store_id'); //店铺id
        $Order = M('order');
        $dd['store_id'] = $store_id;
        $dd['xd_time'] = array(array('gt',strtotime(date('Y-m-d'))),array('lt',strtotime(date('Y-m-d',strtotime('+1 day')))),'and');
        $dd['status'] = array('in','2,3');
        $number = $Order->where($dd)->Count();
        echo $number;

    }


}