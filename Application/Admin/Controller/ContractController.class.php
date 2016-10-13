<?php
namespace Admin\Controller;
use Common\Controller\CommonController;
class ContractController extends CommonController
{

    protected function _initialize()
    {
        // 登录检测
        if (!$uuid = is_login()) { //还没登录跳转到登录页面
            $this->redirect('Admin/Public/login');
        }

        define('UID', $uuid);
        define('FID', session('user_auth.founder_id'));
    }
    /**
     * 检测免费订单数量，及订单结算
     */
    public function settlementCheck(){
        $com = M('Company');
        $fids['c.id'] = FID;
        $agree = $com
            ->alias('c')
            ->join('__SOFTWARE_VIP__ AS s ON c.vip_id=s.id')
            ->field('c.agreement_status,s.order_quantity,c.banben_type,c.reg_time,c.zhengshi_type,c.shiy_type,c.sy_content,s.auth_type,s.rent_time')
            ->where($fids)
            ->find();
        //按单收费
        if($agree['auth_type'] == 2){
            if($agree['agreement_status'] == 1){
                $where['company_id'] = FID;
                $where['expire_time'] = array('gt',time());
                $res = M('CompanyContract')->where($where)->find();
                if($res) {
                    $nowriqi = intval(date('d', time()));//今天的日期
                    $date['ben_month_tou'] = mktime(0, 0, 0, date("m"), 1, date("Y"));//当前月份的头
                    $date['ben_month_wei'] = mktime(23, 59, 59, date("m"), date("t"), date("Y"));//当前月份的尾
                    $date['now_month_tou'] = mktime(0, 0, 0, date('m', strtotime('-1 month')), 1, date("Y"));//上月份的头
                    $date['now_month_wei'] = mktime(23, 59, 59, date('m', strtotime('-1 month')), date("t", strtotime('-1 month')), date("Y"));//上月份的尾
                    $date['prev_month_tou'] = mktime(0, 0, 0, date('m', strtotime('-2 month')), 1, date("Y"));//上上月份的头
                    $date['prev_month_wei'] = mktime(23, 59, 59, date('m', strtotime('-2 month')), date("t", strtotime('-2 month')), date("Y"));//上上月份的尾
                    //$arr = array(2,3,4,5);//指定日期范围
                    //if(in_array($nowriqi,$arr)){
                    $settlewhere['founder_id'] = FID;
                    $settlewhere['sett_status'] = 0;
                    $settlement = M('order_settlement')->where($settlewhere)->find();
                    $name1['id'] = FID;
                    $name = M('company')->where($name1)->getField('c_name');
                    $contract123 = $com->find(FID);
                    $sign_time1['company_id'] = FID;
                    $sign_time = M('company_contract')->where($sign_time1)->getField('sign_time');//查询合同签约时间，合同签约当月不进行统计
                    $this->assign('contractlist123',$contract123);
                    if ($settlement) {//存在未支付的数据时弹出支付
                        $this->assign('jiesuan', true);
                        $this->assign('riqi', $nowriqi);
                        $this->assign('settlement', $settlement);
                        $this->assign('ctime', date('Y年m月',strtotime('last month',$settlement['ctime'])));
                        $this->assign('name', $name);
                        $this->assign('www', $_SERVER['SERVER_NAME']);
                        $this->assign('ordsn', createOrdersn());
                        $this->assign('fid', FID);
                        $this->assign('uid', UID);
                        $this->assign('link', U('Admin/index/index'));
                    } else {//不存在未支付的数据则统计数据，本月有效订单为0以及本月已统计时则不添加数据
                        $data['founder_id'] = FID;//企业ID

                        if($sign_time>=$date['now_month_tou']&&$sign_time<=$date['now_month_wei']){//如果合同时间在上月的范围内，那么统计上月订单从合同时间开始，到上月末
                            $this_valid['complete_time'] = array('between', array($sign_time, $date['now_month_wei']));
                            $this_valid['status'] = array('egt',5);
                            $this_valid['founder_id'] = FID;
                            $this_valid_order = M('order')->where($this_valid)->count();
                            $data['this_valid_order'] = $this_valid_order;//本月有效订单

                            $this_all['xd_time'] = array('between', array($sign_time, $date['now_month_wei']));
                            $this_all['founder_id'] = FID;
                            $this_all_valid_order = M('order')->where($this_all)->count();
                            $data['this_all_valid_order'] = $this_all_valid_order;//本月全部订单
                        }else{//如果合同时间不在上月的范围内，那么统计上月订单从上月初开始，到上月末
                            $this_valid['complete_time'] = array('between', array($date['now_month_tou'], $date['now_month_wei']));
                            $this_valid['status'] = array('egt',5);
                            $this_valid['founder_id'] = FID;
                            $this_valid_order = M('order')->where($this_valid)->count();
                            $data['this_valid_order'] = $this_valid_order;//本月有效订单

                            $this_all['xd_time'] = array('between', array($date['now_month_tou'], $date['now_month_wei']));
                            $this_all['founder_id'] = FID;
                            $this_all_valid_order = M('order')->where($this_all)->count();
                            $data['this_all_valid_order'] = $this_all_valid_order;//本月全部订单
                        }


                        $last_valid['complete_time'] = array('between', array($date['prev_month_tou'], $date['prev_month_wei']));
                        $last_valid['status'] = array('egt',5);
                        $last_valid['founder_id'] = FID;
                        $last_valid_order = M('order')->where($last_valid)->count();
                        $data['last_valid_order'] = $last_valid_order;//上月有效订单

                        $last_all['xd_time'] = array('between', array($date['prev_month_tou'], $date['prev_month_wei']));
                        $last_all['founder_id'] = FID;
                        $last_all_valid_order = M('order')->where($last_all)->count();
                        $data['last_all_valid_order'] = $last_all_valid_order;//上月全部订单

                        $all_valid['status'] = array('egt',5);
                        $all_valid['founder_id'] = FID;
                        $all_valid_order = M('order')->where($all_valid)->count();
                        $data['all_valid_order'] = $all_valid_order;//全部有效订单

                        $all['founder_id'] = FID;
                        $all_order = M('order')->where($all)->count();
                        $data['all_order'] = $all_order;//全部订单

                        $data['ctime'] = time();//生成时间
                        $data['dtime'] = strtotime('-1 month');//数据时间
                        $data['sett_time'] = 0;//结算时间
                        $data['sett_status'] = 0;//结算状态0未结1已结
                        $data['price'] = 0.1;//单价
                        $data['total_fee'] = $this_valid_order * 0.1;//金额

                        $whereere['founder_id'] = FID;
                        $whereere['ctime'] = array('between', array($date['ben_month_tou'], $date['ben_month_wei']));
                        $shengcheng = M('order_settlement')->where($whereere)->count();//查看支付订单表是否有当月的数据

                        if($sign_time<$date['ben_month_tou'] || $sign_time>$date['ben_month_wei']){//判断合同签订时间在本月之外
                            if($data['total_fee']>0 && $shengcheng==0){//金额不为0以及未生成时添加数据
                                M('order_settlement')->add($data);
                            }
                        }
                    }
                    //}
                }else{
                    $this->assign('agree',true);
                    $contractlist = $com->find(FID);
                    $this->assign('contractlist',$contractlist);
                    $this->assign('fid',FID);
                }
            }elseif(empty($agree['agreement_status']) && !is_agent()){
                $map['founder_id'] = FID;
                $map['status'] = array('egt',5);
                $quantity = M('Order')->where($map)->count();
                if($quantity >= $agree['order_quantity']){
                    $this->assign('contract',true);
                    $this->assign('agree',true);
                    $this->assign('fid',FID);
                }
            }
        }elseif($agree['auth_type'] == 1){//按租用
            //试用版
            if($agree['banben_type'] == 1){
                if($agree['shiy_type'] == 2){
                    //按天试用
                    $regtime=(int)$agree['reg_time'];
                    $canday = (int)$agree['sy_content'];
                    $canday = $canday*24*60*60;
                    $today = time();
                    if(($today-$regtime) > $canday){
                        $this->assign('contract',true);
                    }
                }elseif($agree['shiy_type'] == 1){
                    //按单试用
                    $map['founder_id'] = FID;
                    $map['status'] = array('in','5,6');
                    $quantity = M('Order')->where($map)->count();
                    $quantity = intval($quantity);
                    $num =(int)$agree['sy_content'];
                    if($quantity > $num){
                        $this->assign('contract',true);
                    }
                }
            }elseif($agree['banben_type'] == 2){
                //正式使用用
                $regtime=(int)$agree['reg_time'];
                $year = (int) $agree['zhengshi_type'];
                $canday = $year*365*24*60*60;
                $today = time();
                if(($today-$regtime) > $canday){
                    $this->assign('contract',true);
                }
            }else if($agree['banben_type'] == 3){//购买后
                $map['company_id'] = FID;
                $map['status'] = 1;
                $res = M('company_contract')->where($map)->find();
                if($res){
                    $curr = time();
                    if($curr > $res['expire_time']){
                        $this->assign('contract',true);
                    }
                }
            }

        }

    }

    public function agree($id,$type){
        $com = M('Company');
        if($type == 1){
            $data['agreement_status'] = 1;
            $data['agreement_time'] = time();
            $res = $com->where('id = '.$id)->save($data);
        }elseif($type == 2){
            $data['company_id'] = FID;
            $data['sign_time'] = time();
            $data['expire_time'] = strtotime('+1 year');
            $res = M('CompanyContract')->add($data);
        }
        if($res) $this->ajaxReturn('success');
    }

    public function ispayedCheck(){
        $com = M('Company');
        $fids['c.id'] = FID;
        $agree = $com
            ->alias('c')
            ->join('__SOFTWARE_VIP__ AS s ON c.vip_id=s.id')
            ->field('c.agreement_status,s.order_quantity')
            ->where($fids)
            ->find();
        if($agree['agreement_status'] == 1){
            $where['company_id'] = FID;
            $where['expire_time'] = array('gt',time());
            $res = M('CompanyContract')->where($where)->find();
            if($res) {
                $nowriqi = intval(date('d', time()));//今天的日期
                $date['ben_month_tou'] = mktime(0, 0, 0, date("m"), 1, date("Y"));//当前月份的头
                $date['ben_month_wei'] = mktime(23, 59, 59, date("m"), date("t"), date("Y"));//当前月份的尾
                $date['now_month_tou'] = mktime(0, 0, 0, date('m', strtotime('-1 month')), 1, date("Y"));//上月份的头
                $date['now_month_wei'] = mktime(23, 59, 59, date('m', strtotime('-1 month')), date("t", strtotime('-1 month')), date("Y"));//上月份的尾
                $date['prev_month_tou'] = mktime(0, 0, 0, date('m', strtotime('-2 month')), 1, date("Y"));//上上月份的头
                $date['prev_month_wei'] = mktime(23, 59, 59, date('m', strtotime('-2 month')), date("t", strtotime('-2 month')), date("Y"));//上上月份的尾
                //$arr = array(2,3,4,5);//指定日期范围
                //if(in_array($nowriqi,$arr)){
                $settlewhere['founder_id'] = FID;
                $settlewhere['sett_status'] = 0;
                $settlement = M('order_settlement')->where($settlewhere)->find();
                $name1['id'] = FID;
                $name = M('company')->where($name1)->getField('c_name');
                $contract123 = $com->find(FID);
                $sign_time1['company_id'] = FID;
                $sign_time = M('company_contract')->where($sign_time1)->getField('sign_time');//查询合同签约时间，合同签约当月不进行统计
                $this->assign('contractlist123',$contract123);
                if ($settlement) {//存在未支付的数据时弹出支付
                    $this->assign('jiesuan1', true);
                    $this->assign('riqi', $nowriqi);
                    $this->assign('settlement', $settlement);
                    $this->assign('ctime', date('Y年m月',strtotime('last month',$settlement['ctime'])));
                    $this->assign('name', $name);
                    $this->assign('www', $_SERVER['SERVER_NAME']);
                    $this->assign('ordsn', createOrdersn());
                    $this->assign('fid', FID);
                    $this->assign('uid', UID);
                    $this->assign('link', U('Admin/index/index'));
                } else {//不存在未支付的数据则统计数据，本月有效订单为0以及本月已统计时则不添加数据
                    $data['founder_id'] = FID;//企业ID

                    if($sign_time>=$date['now_month_tou']&&$sign_time<=$date['now_month_wei']){//如果合同时间在上月的范围内，那么统计上月订单从合同时间开始，到上月末
                        $this_valid['complete_time'] = array('between', array($sign_time, $date['now_month_wei']));
                        $this_valid['status'] = array('egt',5);
                        $this_valid['founder_id'] = FID;
                        $this_valid_order = M('order')->where($this_valid)->count();
                        $data['this_valid_order'] = $this_valid_order;//本月有效订单

                        $this_all['xd_time'] = array('between', array($sign_time, $date['now_month_wei']));
                        $this_all['founder_id'] = FID;
                        $this_all_valid_order = M('order')->where($this_all)->count();
                        $data['this_all_valid_order'] = $this_all_valid_order;//本月全部订单
                    }else{//如果合同时间不在上月的范围内，那么统计上月订单从上月初开始，到上月末
                        $this_valid['complete_time'] = array('between', array($date['now_month_tou'], $date['now_month_wei']));
                        $this_valid['status'] = array('egt',5);
                        $this_valid['founder_id'] = FID;
                        $this_valid_order = M('order')->where($this_valid)->count();
                        $data['this_valid_order'] = $this_valid_order;//本月有效订单

                        $this_all['xd_time'] = array('between', array($date['now_month_tou'], $date['now_month_wei']));
                        $this_all['founder_id'] = FID;
                        $this_all_valid_order = M('order')->where($this_all)->count();
                        $data['this_all_valid_order'] = $this_all_valid_order;//本月全部订单
                    }

                    $last_valid['complete_time'] = array('between', array($date['prev_month_tou'], $date['prev_month_wei']));
                    $last_valid['status'] = array('egt',5);
                    $last_valid['founder_id'] = FID;
                    $last_valid_order = M('order')->where($last_valid)->count();
                    $data['last_valid_order'] = $last_valid_order;//上月有效订单

                    $last_all['xd_time'] = array('between', array($date['prev_month_tou'], $date['prev_month_wei']));
                    $last_all['founder_id'] = FID;
                    $last_all_valid_order = M('order')->where($last_all)->count();
                    $data['last_all_valid_order'] = $last_all_valid_order;//上月全部订单

                    $all_valid['status'] = array('egt',5);
                    $all_valid['founder_id'] = FID;
                    $all_valid_order = M('order')->where($all_valid)->count();
                    $data['all_valid_order'] = $all_valid_order;//全部有效订单

                    $all['founder_id'] = FID;
                    $all_order = M('order')->where($all)->count();
                    $data['all_order'] = $all_order;//全部订单

                    $data['ctime'] = time();//生成时间
                    $data['dtime'] = strtotime('-1 month');//数据时间
                    $data['sett_time'] = 0;//结算时间
                    $data['sett_status'] = 0;//结算状态0未结1已结
                    $data['price'] = 0.1;//单价
                    $data['total_fee'] = $this_valid_order * 0.1;//金额

                    $whereere['founder_id'] = FID;
                    $whereere['ctime'] = array('between', array($date['ben_month_tou'], $date['ben_month_wei']));
                    $shengcheng = M('order_settlement')->where($whereere)->count();//查看支付订单表是否有当月的数据
                    if($sign_time<$date['ben_month_tou'] || $sign_time>$date['ben_month_wei']){//判断合同签订时间在本月之外
                        if($data['total_fee']>0 && $shengcheng==0){//金额不为0以及未生成时添加数据
                            M('order_settlement')->add($data);
                        }
                    }
                }
                //}
            }else{
                $this->assign('agree',true);
                $contractlist = $com->find(FID);
                $this->assign('contractlist',$contractlist);
                $this->assign('fid',FID);
            }
        }elseif(empty($agree['agreement_status'])){
            $map['founder_id'] = FID;
            $map['status'] = array('egt',5);
            $quantity = M('Order')->where($map)->count();
            if($quantity >= $agree['order_quantity']){
                $this->assign('contract',true);
                $this->assign('agree',true);
                $this->assign('fid',FID);
            }
        }
    }
}