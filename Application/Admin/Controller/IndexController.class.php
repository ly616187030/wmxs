<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Util\Tree;
/**
 * 后台默认控制器
 * @author jry <598821125@qq.com>
 */
class IndexController extends AdminController {
    /**
     * 默认方法
     * @author jry <598821125@qq.com>
     */
    public function index(){


        //订单检测及结算
        if(!is_administrator())
            A('Contract')->settlementCheck();
        // 获取所有模块信息及后台菜单
        $uid=session('user_auth.uid');
        /*$auths=array();
        if(!is_administrator()){
            $res=M('AdminAccess')
                ->alias('a')
                ->join('LEFT JOIN __ADMIN_GROUP__ AS g ON a.group=g.id')
                ->field('g.menu_auth')
                ->where('uid='.$uid)->find();
            $jres=json_decode($res['menu_auth'],true);
            $mo='';
            foreach($jres as $key=>$val){
                $mo.=$key.',';
                $auths[$key]=$val;
            }
            $mo=rtrim($mo,',');
            $con['name']=array('in',$mo);
        }
        $con['status'] = 1;
        $system_module_list = D('Module')->where($con)->order('sort asc, id asc')->select();
        $tree = new tree();
        $menu_list = array();
        foreach ($system_module_list as $key => &$module) {
            $temp = $tree->list_to_tree(json_decode($module['admin_menu'], true));
            $menu_list[$module['name']] = $temp[0];
            $menu_list[$module['name']]['id']   = $module['id'];
            $menu_list[$module['name']]['name'] = $module['name'];
            $menu_list[$module['name']]['auth']=$auths[$module['name']];
        }

        // 如果模块顶级菜单配置了top字段则移动菜单至top所指的模块下边
        foreach ($menu_list as $key => &$value) {
            if ($value['top']) {
                if ($menu_list[$value['top']]) {
                    $menu_list[$value['top']]['_child'] = array_merge(
                        $menu_list[$value['top']]['_child'],
                        $value['_child']
                    );
                    unset($menu_list[$key]);
                }
            }
        }*/

        //dump($menu_list);exit;
        // 获取快捷链接
        $tree = new tree();
        $con = array();
        $menu_p=array();
        $menu_c=array();
        /*if(is_administrator()){
            $con['founder_id'] = UID;
        }else{
            $con['founder_id'] = FID;
        }
        $con['status'] = 1;
        $link_list=D('Link')->where($con)->select();
        foreach ($link_list as $key => &$value) {
            if (!stristr($value['url'], 'http://') && !stristr($value['url'], 'https://')) {
                $value['url'] = U($value['url']);
            }
        }
        $link_list = $tree->list_to_tree($link_list);*/
        $link_list = D('Link')->where('user_id ='.UID)->order('id asc')->select();
        foreach($link_list as $key=> &$value){
            $con['id']=array('in',json_decode($value['iid']));
            $menu_child=M('menu')->where($con)->select();
            $menu_parent=M('menu')->where('id ='.$value['parentid'])->find();
            array_push($menu_c,$menu_child);
            array_push($menu_p,$menu_parent);
        }
        foreach($menu_p as $k=>$v){
            if($v['parentid']!=0){
                if (!stristr($v['url'], 'http://') && !stristr($v['url'], 'https://')) {
                    $menu_p[$k]['url'] = U($v['url']);
                }
                foreach($menu_c[$k] as $kk=>&$vv){
                    if (!stristr($vv['url'], 'http://') && !stristr($vv['url'], 'https://')) {
                        $vv['url'] = U($vv['url']);
                    }
                    if($v['id']==$vv['parentid']){
                        $menu_p[$k]['_child'][]=$vv;
                    }
                }
            }
        }


        $user=M('admin_user');
        $Order=M('order');
        $Store=M('store');
        $Company=M('company');

        $uid=is_login();
        $re=$user->where("id = $uid AND status = 1")->field('user_type,founder_id,id,nickname')->find();

        $fids['founder_id'] = $re['founder_id'];
        $founderid['com.id'] =$re['founder_id'];
        //如果是注册公司
        if($re['user_type']=='company'){
            $info=$Store->where($fids)->getField('store_id',true);
            //首页信息获取
            $where['o.store_id']=array('in',$info);
            $infomation=$Company->alias('com')
                ->join('LEFT JOIN __SOFTWARE_VIP__ AS sv on sv.id=com.vip_id')
                ->join('LEFT JOIN __SMS_ACCOUNT__ AS sa on sa.co_name=com.c_name')
                ->where($founderid)
                ->field('com.c_name as name,sv.auth_type,sv.vip_name,sv.store_number,com.vip_id,com.expire_time,com.can_create_num,sa.balance,com.reg_time,com.rent_time,com.sale_id,com.software_install_id,com.id,com.qq,sv.order_quantity')
                ->find();

            if($info){
                //剩余订单条数计算
                $where9['store_id']=array('in',$info);
                $where9['status']=array('in','5,6');
                $dingdan=M('order')->where($where9)->count();
                if($dingdan<$infomation['order_quantity']){
                    $rest_dingdan=$infomation['order_quantity']-$dingdan;
                }else{
                    $rest_dingdan='over';
                }
                $this->assign('rest_dingdan', $rest_dingdan);
            }

            //判断是否按单收费 auth_type=2
            if($infomation['auth_type']==2){
                //首页合同已签、未签状态
                $company_contract=M('company_contract');
                $fidss['company_id']=$re['founder_id'];
                $hetong=$company_contract->where($fidss)->field('id')->find();
//            dump($hetong);exit;
                if($hetong){
                    $hetongzt=1;
                }else{
                    $hetongzt=2;
                }
                $info9=$Company->alias('com')
                    ->join('LEFT JOIN __COMPANY_CONTRACT__ AS contract on contract.company_id=com.id')
                    ->where($founderid)
                    ->find();
                $this->assign('hetongzt', $hetongzt);
                $this->assign('info9', $info9);
            }

            //判断是否标准版  auth_type=1
            if($infomation['auth_type']==1){
                $company_contract=M('company_contract');
                $condition5['expire_time']=array('gt',strtotime(date("y-m-d",time())));
                $condition5['company_id']=FID;
                $info_bzb=$company_contract->where($condition5)->field('expire_time,use_time')->find();
                if($info_bzb){
                    $state=1;
                }else{
                    $state=2;
                }
                $this->assign('info_bzb', $info_bzb);
                $this->assign('state', $state);
            }

        }

        //如果是普通用户
        if($re['user_type']=='company_member'){
            $foun['id'] =$re['founder_id'];
            $info=$Store->where('uid='.$uid)->field('store_id')->find();
            $where['o.store_id']=$info['store_id'];
            $infomation=$Company->where($foun)->field('c_name as name,vip_id,expire_time,sale_id,software_install_id,qq')->find();
        }

        if($infomation['sale_id']){
            //销售人员
            $info6=$user->where('id='.$infomation['sale_id'])->field('nickname')->find();
        }
        if($infomation['software_install_id']){
            //售后人员
            $info7=$user->where('id='.$infomation['software_install_id'])->field('nickname,qq')->find();
        }
        //到期剩余时间
        $infomation['sy_time']=$infomation['rent_time']*30*24*60*60+$infomation['reg_time'];
        if($info){
            $condition['o.status']=array('in',array(9,10));   //取消订单条件
            $condition1['o.status']=array('in',array(5,6));  //已完成订单条件
            $condition2['o.status']=array('in',array(2,3));  //待处理订单
            $condition3['o.status']=4;           //未完成订单
            //今天时间条件
            $today=strtotime(date("y-m-d",time()));
            $where1['o.xd_time']=array('between',array($today,$today+86400));

            //获得本周时间条件
            $ftime = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) - date ( "w" ) + 1, date ( "Y" ) );
            $ltime = mktime ( 23, 59, 59, date ( "m" ), date ( "d" ) - date ( "w" ) + 7, date ( "Y" ) );
            $where2['o.xd_time']=array('between',array($ftime,$ltime));

            //获得本月时间条件
            $year = date("Y");
            $month = date("m");
            $allday = date("t");
            $start = strtotime($year."-".$month."-1");
            $end = strtotime($year."-".$month."-".$allday);
            $where3['o.xd_time']=array('between',array($start,$end));

            //今天数据
            $jin_num=$Order->alias('o')->where($where)->where($where1)->where($condition1)->count();
            $jin_jye=$Order->alias('o')->where($where)->where($where1)->where($condition1)->sum('total+ps_cost-yh_price');
            $jin_ddjj=round($jin_jye/$jin_num,2);
            $jin_qxdd=$Order->alias('o')->where($where)->where($where1)->where($condition)->count();
            $jin_qxddzj=$Order->alias('o')->where($where)->where($where1)->where($condition)->sum('total+ps_cost-yh_price');
            $jin_wcl=$Order->alias('o')->where($where)->where($where1)->where($condition2)->count();
            $jin_psz=$Order->alias('o')->where($where)->where($where1)->where($condition3)->count();
            $jin_ycl=$Order->alias('o')->where($where)->where($where1)->where($condition1)->count();

            //本周数据
            $zhou_num=$Order->alias('o')->where($where)->where($where2)->where($condition1)->count();
            $zhou_jye=$Order->alias('o')->where($where)->where($where2)->where($condition1)->sum('total+ps_cost-yh_price');
            $zhou_ddjj=round($zhou_jye/$zhou_num,2);
            $zhou_qxdd=$Order->alias('o')->where($where)->where($where2)->where($condition)->count();
            $zhou_qxddzj=$Order->alias('o')->where($where)->where($where2)->where($condition)->sum('total+ps_cost-yh_price');

            //本月数据
            $yue_num=$Order->alias('o')->where($where)->where($where3)->where($condition1)->count();
            $yue_jye=$Order->alias('o')->where($where)->where($where3)->where($condition1)->sum('total+ps_cost-yh_price');
            $yue_ddjj=round($yue_jye/$yue_num,2);
            $yue_qxdd=$Order->alias('o')->where($where)->where($where3)->where($condition)->count();
            $yue_qxddzj=$Order->alias('o')->where($where)->where($where3)->where($condition)->sum('total+ps_cost-yh_price');

            //数据统计报表
            $res = '';
            $str = '';
            $order_arr = array();
            $order_avg = array();
            $order = '';
            $arr = array();

            //获得本年的时间戳范围
            //求得年份
            $year = @date("Y",time());
            //一年有多少天
            $days = ($year % 4 == 0 && $year % 100 != 0 || $year % 400 == 0) ? 366 : 365;
            //今年第一天的时间戳
            $first = strtotime("$year-01-01");
            //今年最后一天的时间戳
            $last = strtotime("+ $days days", $first);

            $datetime=I('post.date');
            if(!empty($datetime)){
                if($datetime==1){
                    $sstime = $ftime;
                    $new_time = ($ltime - $ftime) / 86400;
                }else if($datetime==2){
                    $sstime = $start;
                    $new_time = ($end - $start) / 86400;
//                        dump($datetime);
                }else if($datetime==3){
                    $sstime = $first;
                    $new_time = ($last - $first) / 86400;
                }
            }else{
                $sstime = $ftime;
                $new_time = ($ltime - $ftime) / 86400;
            }

            for ($i = 0; $i <= $new_time; $i++) {
                $res .= "'" . date('Y-m-d', $sstime) . "',";
                $where8['xd_time'] = array('between', array($sstime, $sstime + 86400));
                $where8['ord_peisong_status'] = 4;
                //如果是注册公司
                if($re['user_type']=='company'){
                    $info=$Store->where($fids)->getField('store_id',true);
                    $where8['store_id']=array('in',$info);
                }
                //如果是普通用户
                if($re['user_type']=='company_member'){
                    $info=$Store->where('uid='.$uid)->field('store_id')->find();
                    $where8['store_id']=$info['store_id'];
                }
                $arr[$i]['stime'] = $sstime;
                $sstime = $sstime + 86400;
                $count = $Order->where($where8)->count();
                $sum = $Order->where($where8)->sum('total+ps_cost-yh_price');
                $shopcount = $Order->distinct(true)->where($where8)->field('store_id')->select();

                $arr[$i]['count'] = $count;//订单总数
                $arr[$i]['sum'] = $sum;//订单金额总价
                $arr[$i]['avg'] = round($sum / $count, 2);//订单均价
                $arr[$i]['shop'] = count($shopcount);//交易商家数
                $arr[$i]['person'] = round($sum, 2);//交易额
                $arr[$i]['order'] = round($count, 2);//订单总数

                $str .= $arr[$i]['person'] . ',';
                $order .= $arr[$i]['order'] . ',';
            }

            foreach ($arr as $k => $v) {
                $order_arr[0] += $v['count'];
                $order_arr[1] += $v['sum'];
                $order_arr[2] += $v['avg'];
                $order_arr[3] += $v['shop'];
                $order_arr[4] += $v['person'];
                $order_arr[5] += $v['order'];
            }
            foreach ($order_arr as $k => $v) {
                $order_avg[$k] = round($order_arr[$k] / ($new_time + 1), 2);
            }
            session('arr', null);
            session('order_arr', null);
            session('order_avg', null);
            session('arr', $arr);
            session('order_arr', $order_arr);
            session('order_avg', $order_avg);

            //最新通知
            $message=M('message');
            $msg=$message->limit('5')->order('create_time desc')->select();
        }
        //返回消息数量开始
        $who['to_uid']=UID;
        $who['is_read']=0;
        $xiaoxi =  M('message')->where($who)->count();
        $time000 = time();
        $time111 = time()-172800;
        $who1['create_time']=array('between',array($time111,$time000));
        $who1['type']=1;
        $sysgg = M('message')->where($who1)->count();
        $who2['create_time']=array('between',array($time111,$time000));
        $who2['type']=2;
        $upgg = M('message')->where($who2)->count();
        $this->assign('xiaoxi',$xiaoxi);
        $this->assign('sysgg',$sysgg);
        $this->assign('upgg',$upgg);
        //返回消息数量结束
        $this->assign('res', rtrim($res, ','));
        $this->assign('str', rtrim($str, ','));
        $this->assign('order', rtrim($order, ','));
        $this->assign('arr', $arr);
        $this->assign('order_arr', $order_arr);
        $this->assign('order_avg', $order_avg);
        $this->assign('datetime', $datetime);

        $this->assign('user', $re);
        $this->assign('info', $infomation);
        $this->assign('info6', $info6);
        $this->assign('info7', $info7);
        $this->assign('msg', $msg);
        // 模板变量赋值
        $this->assign('_link_list', $menu_p);  // 后台快捷链接
        //$this->assign('_menu_list', $menu_list);  // 后台左侧菜单
        $this->assign('meta_title', "首页");
        $this->assign('jin_num', $jin_num);
        $this->assign('jin_jye', $jin_jye);
        $this->assign('jin_ddjj', $jin_ddjj);
        $this->assign('jin_qxdd', $jin_qxdd);
        $this->assign('jin_qxddzj', $jin_qxddzj);
        $this->assign('jin_wcl', $jin_wcl);
        $this->assign('jin_psz', $jin_psz);
        $this->assign('jin_ycl', $jin_ycl);
        $this->assign('zhou_num', $zhou_num);
        $this->assign('zhou_jye', $zhou_jye);
        $this->assign('zhou_ddjj', $zhou_ddjj);
        $this->assign('zhou_qxdd', $zhou_qxdd);
        $this->assign('zhou_qxddzj', $zhou_qxddzj);
        $this->assign('yue_num', $yue_num);
        $this->assign('yue_jye', $yue_jye);
        $this->assign('yue_ddjj', $yue_ddjj);
        $this->assign('yue_qxdd', $yue_qxdd);
        $this->assign('yue_qxddzj', $yue_qxddzj);
        $this->assign('agree2',true);
        $this->assign('founder_id',FID);
        $this->assign('downloadapp',true);
        if(is_administrator()||is_agent()){
            $this->assign('eee',1);
        }
        $this->display();
    }

    /**
     * 删除缓存
     * @author jry <598821125@qq.com>
     */
    public function removeRuntime() {
        $file = new \Common\Util\File();
        $result = $file->del_dir(RUNTIME_PATH);
        if ($result) {
            $this->success("缓存清理成功");
        } else {
            $this->error("缓存清理失败");
        }
    }

}
