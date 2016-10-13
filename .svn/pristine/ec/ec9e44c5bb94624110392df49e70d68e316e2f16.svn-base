<?php
/**
 * Created by PhpStorm.
 * User: wxm
 * Date: 2015/9/9
 * Time: 16:04
 */

namespace Dispatch\Controller;

use Admin\Controller\AdminController;

class DispatchController extends AdminController
{
    private $station;
    private $person;
    private $order;

    /*
     * $o 接受操作订单的条件
     * $p 接受操作配送员的条件
     * */
    private function getOrder($o = array(), $p = array(), $s = array())
    {

        $user = M('admin_user');
        $persons = M('delivery_person');;
        $stores = M('store');
        $orders = M('order');
        $details = M('order_detail');
        $uid = is_login();
        $re = $user->where("id = $uid AND status = 1")->field('user_type,founder_id,id,push_msg_id')->find();

        //配送员条件
        if (!empty($p)) {
            if (!empty($p['p_name'])) $where_p['person_name|person_bianhao '] = $p['p_name'];
            if (!empty($p['phone'])) $where_p['phone'] = $p['phone'];
            if (!empty($p['person_id'])) $where_p['person_id'] = $p['person_id'];
        }
        //店铺条件
        if (!empty($s)) {
            if (!empty($s['s_name'])) $where1['store_name|store_name_py'] = $s['s_name'];
        }
        //订单条件
        $time = empty($o['time']) ? date('Y-m-d') : $o['time'];
        $to_time = strtotime($time);
        $end_time = $to_time + 86399;
        $where2['confirm_time '] = array(array('gt', $to_time), array('lt', $end_time));
        $where2['sh_mode'] = 2;
        if (!empty($o)) {
            if (!empty($o['status'])) $where2['status'] = $o['status'];
            if (!empty($o['ord.order_id'])) $where2['ord.order_id'] = $o['ord.order_id'];
        }

        //判断是否注册公司
        if ($re['user_type'] == 'company') {

            //注册公司下的所有配送员
            $where['per.founder_id'] = $re['founder_id'];
            $person = $persons->alias('per')->where($where_p)->where($where)->select();
//            dump($person);
            $this->person = $person;

            //注册公司下的商家
            $where1['u.founder_id'] = $re['founder_id'];
            $where1['user_type'] = 'company_member';
            $info = $user->alias('u')->where($where1)->getField('id', true);

            if ($info) {
                $info1 = implode(',', $info);
                $where3['uid'] = array('in', $info1);
                $store = $stores->where($where3)->select();

                //订单
                $ord = array();
                foreach ($store as $kkkk => $vvvv) {
                    $where2['ord.store_sn'] = $vvvv['store_sn'];
                    $order = $orders->alias('ord')
                        ->join('left join __USER_ADDRESS__ AS uadd on uadd.address_id = ord.address_id')
                        ->field('ord.*,uadd.username,uadd.gender,uadd.lng as uadd_lng,uadd.lat as uadd_lat,uadd.detail_address,uadd.phone')
                        ->where($where2)
                        ->select();
                    $order_detail = $details->alias('orde')
                        ->join('left join __ORDER__ AS ord on orde.order_id = ord.order_id')
                        ->join('left join __CANMING__ AS cm on cm.cm_id = orde.cm_id')
                        ->field('cm.cm_id,cm.cm_name,cm.cm_desc,orde.*')
                        ->where($where2)
                        ->select();

                    if (!empty($order)) {
                        foreach ($order as & $vvvvv) {
                            $vvvvv['store_name'] = $vvvv['store_name'];
                            $jwd = explode(",", $vvvv['lng_lat']);
                            $vvvvv['lng'] = $jwd[0];
                            $vvvvv['lat'] = $jwd[1];
                            $vvvvv['tel'] = $vvvv['tel'];
                            $vvvvv['store_rebate'] = $vvvv['store_rebate'];
                            $vvvvv['address_detail'] = $vvvv['address_detail'];
                            $vvvvv['sh_time'] = $vvvv['sh_time'];
                            foreach ($order_detail as $key => $value) {
                                if ($value['order_id'] == $vvvvv['order_id']) {
                                    $vvvvv['order_detail'][] = $value;
                                }
                            }
                            array_push($ord, $vvvvv);
                        }
                    };
                }
//                dump($ord);
                $this->order = $ord;
            }

        }

        //判断是否普通用户
        if ($re['user_type'] == 'company_member') {

            //注册公司下的所有配送员
            $where['per.uid'] = is_login();
            $person = $persons->alias('per')->where($where_p)->where($where)->select();
            $this->person = $person;

            //注册公司下的商家
                $where3['founder_id'] = $re['founder_id'];
                $where3['dispatch_push_id']=$re['push_msg_id'];
                $store = $stores->where($where3)->select();

                //订单
                $ord = array();
                foreach ($store as $kkkk => $vvvv) {

                    $where2['ord.store_sn'] = $vvvv['store_sn'];
                    $order = $orders->alias('ord')
                        ->join('left join __USER_ADDRESS__ AS uadd on uadd.address_id = ord.address_id')
                        ->field('ord.*,uadd.username,uadd.gender,uadd.lng as uadd_lng,uadd.lat as uadd_lat,uadd.detail_address,uadd.phone')
                        ->where($where2)
                        ->select();
                    $order_detail = $details->alias('orde')
                        ->join('left join __ORDER__ AS ord on orde.order_id = ord.order_id')
                        ->join('left join __CANMING__ AS cm on cm.cm_id = orde.cm_id')
                        ->field('cm.cm_id,cm.cm_name,cm.cm_desc,orde.*')
                        ->where($where2)
                        ->select();

                    if (!empty($order)) {
                        foreach ($order as & $vvvvv) {
                            $vvvvv['store_name'] = $vvvv['store_name'];
                            $jwd = explode(",", $vvvv['lng_lat']);
                            $vvvvv['lng'] = $jwd[0];
                            $vvvvv['lat'] = $jwd[1];
                            $vvvvv['tel'] = $vvvv['tel'];
                            $vvvvv['store_rebate'] = $vvvv['store_rebate'];
                            $vvvvv['address_detail'] = $vvvv['address_detail'];
                            $vvvvv['sh_time'] = $vvvv['sh_time'];
                            foreach ($order_detail as $key => $value) {
                                if ($value['order_id'] == $vvvvv['order_id']) {
                                    $vvvvv['order_detail'][] = $value;
                                }
                            }
                            array_push($ord, $vvvvv);
                        }
                    };
                }
//                dump($ord);
                $this->order = $ord;
        }
    }


    public function index()
    {
        $this->getOrder(array('status' => array('between', '2,10')));
        if ($_POST) {

            $fenlei_id = I('fenlei');
            $time = I('time');
            $this->getOrder(array('status' => array('between', '2,10'), 'time' => $time));
            $cx_order = $this->order;
            $cx_person = $this->person;
            //查询订单详情
//            dump($cx_order);
            foreach ($cx_order as $k => $v) {

                if ($v['status'] == 4 && $v['ord_peisong_status'] != 4) {
                    $fwq_time = time();  //当前服务器时间
                    $yq_time = $fwq_time - $v['confirm_time'];   //接单后用去时间
                    $shengyu_time = $v['sh_time'] * 60 - $yq_time;   //用去时间与45分钟对比，大于0说明在45分钟内，小于0说明超出45分钟
                    $v['shengyu_time'] = $shengyu_time;

                    $shengyu_abs = abs($shengyu_time);
                    $minute = 0; //时间默认值
                    $minute = floor($shengyu_abs / 60);

                    if ($shengyu_time >= 0) {
                        $v['shengyu_minute'] = $minute;
                    } else {
                        $v['shengyu_minute'] = (-$minute);
                    }
                }
                if ($v['status'] == 4 && $v['ord_peisong_status'] == 4) {
                    $ps_time = $v['ps_time'] - $v['confirm_time'];   //配送用去时间
                    $dingge_time = $v['sh_time'] * 60 - $ps_time;    //决定最后定格的时间
                    $v['dingge_time'] = $dingge_time;

                    $dingge_abs = abs($dingge_time);
                    $minute = 0; //时间默认值
                    $minute = floor($dingge_abs / 60);

                    if ($dingge_time >= 0) {
                        $v['dingge_minute'] = $minute;
                    } else {
                        $v['dingge_minute'] = (-$minute);
                    }
                }

                foreach ($cx_person as $kk => $vv) {
                    if ($v['person_id'] == $vv['person_id']) {
                        $v['p_lng'] = $vv['lng'];
                        $v['p_lat'] = $vv['lat'];
                        $v['person_name'] = $vv['person_name'];
                    }
                }
                if ($fenlei_id == 1 && $v['ord_peisong_status'] == 0 && $v['status'] == 4) {
                    $res[] = $v;
                }
                if ($fenlei_id == 2 && $v['ord_peisong_status'] == 1 && $v['status'] == 4) {
                    $res[] = $v;
                }
                if ($fenlei_id == 3 && $v['ord_peisong_status'] == 2 && $v['status'] == 4) {
                    $res[] = $v;
                }
                if ($fenlei_id == 4 && $v['ord_peisong_status'] == 4 && in_array($v['status'], array(4, 5, 6))) {
                    $res[] = $v;
                }
                if ($fenlei_id == 6 && in_array($v['status'], array(2, 3, 4, 5, 6, 7, 8, 9, 10))) {
                    $res[] = $v;
                }
                if ($fenlei_id == 7 && $v['ord_peisong_status'] == 3 && $v['status'] == 4) {
                    $res[] = $v;
                }
                if ($fenlei_id == 8 && in_array($v['status'], array(7, 8, 9, 10))) {
                    $res[] = $v;

                }
            }
            $this->ajaxReturn($res);
        }

        //查询配送员
        $cx_user = $this->person;
//        dump($cx_user);
        $where1['year'] = date('Y');
        $where1['month'] = date('m');
        $where1['day'] = date('d');
        $where1['status'] = array(array('eq', 2), array('eq', 3), array('eq', 4), 'or');
        $cx_de = M('delivery_detail')->where($where1)->field("status,person_id")->select();

        $zaixian = 0;
        $lixian = 0;
        foreach ($cx_user as $k => $v) {
            if ($v['ygzt'] == 0) {
                $zaixian++;
                foreach ($cx_de as $kk => $vv) {
                    if ($v['person_id'] == $vv['person_id']) {
                        $v['child'][] = $vv;

                        if ($vv['status'] == 2 || $vv['status'] == 3) {
                            $v['child1'][] = $vv;
                        }
                    }
                }
                $v['count'] = count($v['child'], 0);
                $v['count1'] = count($v['child1'], 0);
                $res[] = $v;
            } elseif ($v['ygzt'] == 1) {
                $lixian++;
            }
        }
//        dump($res);
        $this->assign('count', $zaixian);
        $this->assign('user', $res);
        $this->assign('li', $lixian);


        //查询订单
        $cx_order = $this->order;
        $zong_count = 0;//总单数
        $cx_quxiao = 0;//取消单数
        $cx_songda = 0;//送达单数
        $cx_jiedan = 0;//接单数
        $cx_fenpei = 0;//已分配单数
        $cx_weifenpei = 0;//未分配单数
        $cx_yiquhuo = 0;//已取货数
        $cx_yiquxiao = 0;//配送员已取消数
        foreach ($cx_order as $key => $v) {
//            dump($v['sh_time']);
            if ($v['status'] == 4 && $v['ord_peisong_status'] != 4) {
                $fwq_time = time();  //当前服务器时间
                $yq_time = $fwq_time - $v['confirm_time'];   //接单后用去时间
                $shengyu_time = $v['sh_time'] * 60 - $yq_time;   //用去时间与45分钟对比，大于0说明在45分钟内，小于0说明超出45分钟
                $v['shengyu_time'] = $shengyu_time;

                $shengyu_abs = abs($shengyu_time);
                $minute = 0; //时间默认值
                $minute = floor($shengyu_abs / 60);

                if ($shengyu_time >= 0) {
                    $cx_order[$key]['shengyu_minute'] = $minute;
                } else {
                    $cx_order[$key]['shengyu_minute'] = (-$minute);
                }
            }
            if ($v['status'] == 4 && $v['ord_peisong_status'] == 4) {
                $ps_time = $v['ps_time'] - $v['confirm_time'];   //配送用去时间
                $dingge_time = $v['sh_time'] * 60 - $ps_time;    //决定最后定格的时间
                $v['dingge_time'] = $dingge_time;

                $dingge_abs = abs($dingge_time);
                $minute = 0; //时间默认值
                $minute = floor($dingge_abs / 60);

                if ($dingge_time >= 0) {
                    $cx_order[$key]['dingge_minute'] = $minute;
                } else {
                    $cx_order[$key]['dingge_minute'] = (-$minute);
                }
            }
            $zong_count++;
            if ($v['ord_peisong_status'] == 5 && $v['status'] == 4) {
                $cx_quxiao++;
            }
            if ($v['ord_peisong_status'] == 4 && in_array($v['status'], array(4, 5, 6))) {
                $cx_songda++;
            }
            if ($v['ord_peisong_status'] == 2 && $v['status'] == 4) {
                $cx_jiedan++;
            }
            if ($v['ord_peisong_status'] == 1 && $v['status'] == 4) {
                $cx_fenpei++;
            }
            if ($v['ord_peisong_status'] == 0 && $v['status'] == 4) {
                $cx_weifenpei++;
            }
            if ($v['ord_peisong_status'] == 3 && $v['status'] == 4) {
                $cx_yiquhuo++;
            }
            if (in_array($v['status'], array(7, 8, 9, 10))) {
                $cx_yiquxiao++;
            }
        }
//        dump($cx_order);
        $this->assign('order', $cx_order);
        $this->assign('zongcount', $zong_count);
        $this->assign('quxiao', $cx_quxiao);
        $this->assign('songda', $cx_songda);
        $this->assign('jiedan', $cx_jiedan);
        $this->assign('fenpei', $cx_fenpei);
        $this->assign('weifenpei', $cx_weifenpei);
        $this->assign('yiquhuo', $cx_yiquhuo);
        $this->assign('yiquxiao', $cx_yiquxiao);

        $this->assign('user_auth', cookie('user_auth') ? unserialize(cookie('user_auth')) : session('user_auth'));
        $this->display();
    }


    public function sele(){

        //获取得到的时间，以及当天时间的时间戳
        $time = I('time');
        $this->getOrder(array('time' => $time, 'status' => array('between', '2,9')));
        $cx_order = $this->order;
        $cx_person = $this->person;

        $page = intval($_POST['pageNum']); //当前页

        $total = count($cx_order);//总记录数
        $pageSize = 15; //每页显示数
        $totalPage = ceil($total / $pageSize); //总页数
        $startPage = $page * $pageSize; //开始记录

        //构造数组
        $arr['total'] = $total;
        $arr['pageSize'] = $pageSize;
        $arr['totalPage'] = $totalPage;

        $uid = is_login();
        $user = M('admin_user');
        $orders = M('order');
        $details = M('order_detail');
        $re = $user->where("id = $uid AND status = 1")->field('user_type,founder_id,id,push_msg_id')->find();

        //订单条件
        $to_time = strtotime($time);
        $end_time = $to_time + 86399;
        $where2['ord.confirm_time '] = array(array('gt', $to_time), array('lt', $end_time));
        $where2['ord.sh_mode'] = 2;
        $where2['ord.status'] = array('between', '2,9');

        //判断是否注册公司
        if ($re['user_type'] == 'company') {
            //注册公司下的商家
            $where8['u.founder_id'] = $re['founder_id'];
            $where8['user_type'] = 'company_member';
            $info = $user->alias('u')->where($where8)->getField('id', true);
            if ($info) {
                $info1 = implode(',', $info);
                $where2['s.uid'] = array('in', $info1);
                //订单
                $ord = array();
                $order = $orders->alias('ord')
                    ->join('left join __USER_ADDRESS__ AS uadd on uadd.address_id = ord.address_id')
                    ->join('left join __STORE__ AS s on s.store_sn = ord.store_sn')
                    ->field('ord.*,uadd.username,uadd.gender,uadd.lng as uadd_lng,uadd.lat as uadd_lat,uadd.detail_address,uadd.phone,s.store_name,s.lng_lat,s.tel,s.store_rebate,s.address_detail,s.sh_time')
                    ->where($where2)
                    ->limit($startPage, $pageSize)
                    ->select();
//                echo($orders->getLastSql());

                $order_detail = $details->alias('orde')
                    ->join('left join __ORDER__ AS ord on orde.order_id = ord.order_id')
                    ->join('left join __CANMING__ AS cm on cm.cm_id = orde.cm_id')
                    ->join('left join __STORE__ AS s on s.store_sn = ord.store_sn')
                    ->field('cm.cm_id,cm.cm_name,cm.cm_desc,orde.*')
                    ->where($where2)
                    ->select();

                if (!empty($order)) {
                    foreach ($order as & $vvvvv) {
                        $vvvvv['store_name'] = $vvvvv['store_name'];
                        $jwd = explode(",", $vvvvv['lng_lat']);
                        $vvvvv['lng'] = $jwd[0];
                        $vvvvv['lat'] = $jwd[1];
                        $vvvvv['tel'] = $vvvvv['tel'];
                        $vvvvv['store_rebate'] = $vvvvv['store_rebate'];
                        $vvvvv['address_detail'] = $vvvvv['address_detail'];
                        $vvvvv['sh_time'] = $vvvvv['sh_time'];

                        if ($vvvvv['status'] == 4 && $vvvvv['ord_peisong_status'] != 4) {
                            $fwq_time = time();  //当前服务器时间
                            $yq_time = $fwq_time - $vvvvv['confirm_time'];   //接单后用去时间
                            $shengyu_time = $vvvvv['sh_time'] * 60 - $yq_time;   //用去时间与45分钟对比，大于0说明在45分钟内，小于0说明超出45分钟
                            $vvvvv['shengyu_time'] = $shengyu_time;

                            $shengyu_abs = abs($shengyu_time);
                            $minute = 0; //时间默认值
                            $minute = floor($shengyu_abs / 60);

                            if ($shengyu_time >= 0) {
                                $vvvvv['shengyu_minute'] = $minute;
                            } else {
                                $vvvvv['shengyu_minute'] = (-$minute);
                            }
                        }
                        if ($vvvvv['status'] == 4 && $vvvvv['ord_peisong_status'] == 4) {
                            $ps_time = $vvvvv['ps_time'] - $vvvvv['confirm_time'];   //配送用去时间
                            $dingge_time = $vvvvv['sh_time'] * 60 - $ps_time;    //决定最后定格的时间
                            $v['dingge_time'] = $dingge_time;

                            $dingge_abs = abs($dingge_time);
                            $minute = 0; //时间默认值
                            $minute = floor($dingge_abs / 60);

                            if ($dingge_time >= 0) {
                                $vvvvv['dingge_minute'] = $minute;
                            } else {
                                $vvvvv['dingge_minute'] = (-$minute);
                            }
                        }
                        foreach ($order_detail as $key => $value) {
                            if ($value['order_id'] == $vvvvv['order_id']) {
                                $vvvvv['order_detail'][] = $value;
                            }
                        }
                        array_push($ord, $vvvvv);
                    }
                };
                $dingdan = $ord;
            }
        }


        //判断是否普通用户
        if ($re['user_type'] == 'company_member') {

                //注册公司下的商家
                $where2['s.founder_id'] = $re['founder_id'];
                $where2['s.dispatch_push_id']=$re['push_msg_id'];

                //订单
                $ord = array();
                $order = $orders->alias('ord')
                    ->join('left join __USER_ADDRESS__ AS uadd on uadd.address_id = ord.address_id')
                    ->join('left join __STORE__ AS s on s.store_sn = ord.store_sn')
                    ->field('ord.*,uadd.username,uadd.gender,uadd.lng as uadd_lng,uadd.lat as uadd_lat,uadd.detail_address,uadd.phone,s.store_name,s.lng_lat,s.tel,s.store_rebate,s.address_detail,s.sh_time')
                    ->where($where2)
                    ->limit($startPage, $pageSize)
                    ->select();

                $order_detail = $details->alias('orde')
                    ->join('left join __ORDER__ AS ord on orde.order_id = ord.order_id')
                    ->join('left join __CANMING__ AS cm on cm.cm_id = orde.cm_id')
                    ->join('left join __STORE__ AS s on s.store_sn = ord.store_sn')
                    ->field('cm.cm_id,cm.cm_name,cm.cm_desc,orde.*')
                    ->where($where2)
                    ->select();

                if (!empty($order)) {
                    foreach ($order as & $vvvvv) {
                        $vvvvv['store_name'] = $vvvvv['store_name'];
                        $jwd = explode(",", $vvvvv['lng_lat']);
                        $vvvvv['lng'] = $jwd[0];
                        $vvvvv['lat'] = $jwd[1];
                        $vvvvv['tel'] = $vvvvv['tel'];
                        $vvvvv['store_rebate'] = $vvvvv['store_rebate'];
                        $vvvvv['address_detail'] = $vvvvv['address_detail'];
                        $vvvvv['sh_time'] = $vvvvv['sh_time'];

                        if ($vvvvv['status'] == 4 && $vvvvv['ord_peisong_status'] != 4) {
                            $fwq_time = time();  //当前服务器时间
                            $yq_time = $fwq_time - $vvvvv['confirm_time'];   //接单后用去时间
                            $shengyu_time = $vvvvv['sh_time'] * 60 - $yq_time;   //用去时间与45分钟对比，大于0说明在45分钟内，小于0说明超出45分钟
                            $vvvvv['shengyu_time'] = $shengyu_time;

                            $shengyu_abs = abs($shengyu_time);
                            $minute = 0; //时间默认值
                            $minute = floor($shengyu_abs / 60);

                            if ($shengyu_time >= 0) {
                                $vvvvv['shengyu_minute'] = $minute;
                            } else {
                                $vvvvv['shengyu_minute'] = (-$minute);
                            }
                        }
                        if ($vvvvv['status'] == 4 && $vvvvv['ord_peisong_status'] == 4) {
                            $ps_time = $vvvvv['ps_time'] - $vvvvv['confirm_time'];   //配送用去时间
                            $dingge_time = $vvvvv['sh_time'] * 60 - $ps_time;    //决定最后定格的时间
                            $v['dingge_time'] = $dingge_time;

                            $dingge_abs = abs($dingge_time);
                            $minute = 0; //时间默认值
                            $minute = floor($dingge_abs / 60);

                            if ($dingge_time >= 0) {
                                $vvvvv['dingge_minute'] = $minute;
                            } else {
                                $vvvvv['dingge_minute'] = (-$minute);
                            }
                        }
                        foreach ($order_detail as $key => $value) {
                            if ($value['order_id'] == $vvvvv['order_id']) {
                                $vvvvv['order_detail'][] = $value;
                            }
                        }
                        array_push($ord, $vvvvv);
                    }
                };
                $dingdan = $ord;
        }
        $arr['list'] = $dingdan;
        $this->ajaxReturn($arr);
    }

    public function userName(){

        //获取得到的姓名
        $name = I('name');
        $time = I('time');
        $this_time = explode('-', $time);
        $year = $this_time[0];
        $month = $this_time[1];
        $day = $this_time[2];
        //如果查询到，模糊查询配送员姓名
        if ($name) {
            $this->getOrder('', array('p_name' => array('like', '%' . $name . '%'), 'is_who' => 1, 'ygzt' => 0, 'person_type' => 0));
            //查配送员和订单关系
            $where1['status'] = array(array('eq', 2), array('eq', 3), array('eq', 4), 'or');
            $where1['year'] = $year;
            $where1['month'] = $month;
            $where1['day'] = $day;
            $cx_user = $this->person;
            $cx_de = M('delivery_detail')->where($where1)->field("status,person_id")->select();
            foreach ($cx_user as $key => $val) {
                foreach ($cx_de as $k => $v) {
                    if ($val['person_id'] == $v['person_id']) {
                        $cx_user[$key]['child'][] = $v;
                        if ($v['status'] == 2 || $v['status'] == 3) {
                            $cx_user[$key]['child1'][] = $v;
                        }
                    }
                }
                $cx_user[$key]['count'] = count($cx_user[$key]['child'], 0);
                $cx_user[$key]['count1'] = count($cx_user[$key]['child1'], 0);
            }
            $this->ajaxReturn($cx_user);
        }

    }

    public function zaixian(){
        $num = I('person_status');
        $online=I('online');
        //实例化表
        $user = M('delivery_person');
        if($num == 1){
            //查配送员和订单关系
            $this->getOrder('',array('phone'=>array('in',json_decode($online,true))));
            $cx_user = $this->person;
            $where1['year'] = date('Y');
            $where1['month'] = date('m');
            $where1['day'] = date('d');
            $where1['status'] = array(array('eq',2),array('eq',3),array('eq',4),'or');
            $cx_de = M('delivery_detail')->where($where1)->field("status,person_id")->select();
            foreach ($cx_user as $key => $val) {
                foreach ($cx_de as $k => $v) {
                    if ($val['person_id'] == $v['person_id']) {
                        $cx_user[$key]['child'][]= $v;
                        if($v['status']==2||$v['status']==3){
                            $cx_user[$key]['child1'][]= $v;
                        }
                    }
                }
                $cx_user[$key]['count'] = count($cx_user[$key]['child'], 0);
                $cx_user[$key]['count1'] = count($cx_user[$key]['child1'], 0);
                $cx_user[$key]['color'] = 1;
            }
            session('cx_user1',$cx_user);
            $this->ajaxReturn($cx_user);
        }
        if($num == 2){
            //查询离线的配送员
            $this->getOrder('',array('phone'=>array('not in',json_decode($online,true))));
            $cx_user = $this->person;
            $where1['year'] = date('Y');
            $where1['month'] = date('m');
            $where1['day'] = date('d');
            $where1['status'] = array(array('eq',4),array('eq',5),'or');
            $cx_de = M('delivery_detail')->where($where1)->field("status,person_id")->select();
            foreach ($cx_user as $key => $val) {
                foreach ($cx_de as $k => $v) {
                    if ($val['person_id'] == $v['person_id']) {
                        $cx_user[$key]['child'][]= $v;
                        if($v['status']==2||$v['status']==3){
                            $cx_user[$key]['child1'][]= $v;
                        }
                    }
                }
                $cx_user[$key]['count'] = count($cx_user[$key]['child'], 0);
                $cx_user[$key]['count1'] = count($cx_user[$key]['child1'], 0);
                $cx_user[$key]['color'] = 0;
            }
            session('cx_user2',$cx_user);
            $this -> ajaxReturn($cx_user);
        }
        if($num == 3){
            //查询全部配送员
            $cx_user1=session('cx_user1');
            $cx_user2=session('cx_user2');
            foreach($cx_user1 as $key => $value){
                $cx_user1[$key]['color'] = 1;
            }
            foreach($cx_user2 as $key => $value){
                $cx_user2[$key]['color'] = 0;
            }
            $cx_user=array_merge($cx_user1,$cx_user2);
            $this -> ajaxReturn($cx_user);
        }
    }



    public function information()
    {
        //获取接收到的数据
        $store = I('store');
        $person = I('person');
        $time = I('time');
        if ($store != '' && $person != '') {
            $this->getOrder(array('time' => $time, 'status' => array('between', '2,10')), array('p_name' => array('like', '%' . $person . '%')), array('s_name' => array('like', '%' . $store . '%')));
        }
        if ($store == '' && $person != '') {
            $this->getOrder(array('time' => $time, 'status' => array('between', '2,10')), array('p_name' => array('like', '%' . $person . '%')));
        }
        if ($store != '' && $person == '') {
            $this->getOrder(array('time' => $time, 'status' => array('between', '2,10')), array(), array('s_name' => array('like', '%' . $store . '%')));
        }
        $cx_order = $this->order;
        $cx_person = $this->person;
        foreach ($cx_order as $key => $v) {
            $detail = unserialize($v['order_detail']);
            $address = unserialize($v['address']);
            $cx_order[$key]['address'] = $address;
            $cx_order[$key]['order_detail'] = $detail;
            foreach ($cx_person as $kk => $vv) {
                if ($v['person_id'] == $vv['person_id']) {
                    $cx_order[$key]['p_lng'] = $vv['lng'];
                    $cx_order[$key]['p_lat'] = $vv['lat'];
                    $cx_order[$key]['person_name'] = $vv['person_name'];
                }
            }
        }
        $this->ajaxReturn($cx_order);
    }

    public function canming()
    {
        $canming = I('order_id');
        $time = I('time');
        if ($canming) {
            $cx_per_d = M('delivery_detail')->where('order_id =' . $canming)->select();
            $this->getOrder(array('ord.order_id' => $canming, 'time' => $time));
            $cx_order = $this->order;
            $cx_order = array_merge($cx_order, $cx_per_d);
//            dump($cx_order);
//            foreach ($cx_order as $key => $v) {
//                $detail=unserialize($v['order_detail']);
//                $address=unserialize($v['address']);
//                $cx_order[$key]['address']=$address;
//                $cx_order[$key]['order_detail']=$detail;
//            }
            $this->ajaxReturn($cx_order);
        } else {
            $this->ajaxReturn(false);
        }


    }

    public function addPerson()
    {
        $order_id = I('order_id');
        $store_sn = I('store_sn');
        $person_id = I('person_id');
        if ($order_id != null && $store_sn != null) {
            $per = M('delivery_detail');
            $per->startTrans();//开启事务

            $data['order_id'] = $order_id;
            $data['store_sn'] = $store_sn;
            $data['person_id'] = $person_id;
            $data['year'] = date('Y');
            $data['month'] = date('m');
            $data['day'] = date('d');
            $data['status'] = 1;
            $data['assign_time'] = time();
            $cx_order = $per->where("order_id = $order_id")->field('status,person_id')->find();
            if ($cx_order['status'] == 2 || $cx_order['person_id'] == $person_id) {
                $this->ajaxReturn("已经派送过了！");
            } else {
                $per->where("order_id = $order_id")->delete();
                action_log('delete_action', 'delivery_detail', "$order_id", is_login());
                $add_per = $per->add($data);
                $data1['person_id'] = $person_id;
                $data1['ord_peisong_status'] = 1;
                $data1['status'] = 4;
                $add_order = M('order')->where("order_id = $order_id")->save($data1);
                $cx_phone = M('delivery_person')->where("person_id = $person_id")->field('phone')->find();
                if ($add_per && $add_order) {
                    $per->commit();
                    vendor("phpTui.PushMsg#class");
                    $igt = new \PushMsg();
                    $igt->pushMsgToApp($cx_phone["clientId"]);
                    push_msg_client($cx_phone['phone']);
                    action_log('delivery_action', 'delivery_detail,order', "$add_per,$order_id", is_login());
                    $this->ajaxReturn(1);
                } else {
                    $per->rollback();
                    $this->ajaxReturn('参数错误');
                }
            }
        }
    }

    //获得配送员订单详情
    public function personinfo()
    {
        $person_id = I('person_id');

        $this->getOrder('', array('person_id' => $person_id));
        //查配送员和订单关系
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $cx_user = $this->person;

        $where['d.status'] = array(array('eq', 2), array('eq', 3), array('eq', 4), 'or');
        $where['d.year'] = $year;
        $where['d.month'] = $month;
        $where['d.day'] = $day;
        $cx_de = M('delivery_detail')->alias('d')
            ->join("JOIN __STORE__ AS s ON s.store_sn = d.store_sn")
            ->join("JOIN __ORDER__ AS o ON d.order_id = o.order_id")
            ->join("JOIN __USER_ADDRESS__ AS ua ON ua.address_id = o.address_id")
            ->where($where)
            ->field("d.status stu,d.person_id,s.store_name,s.store_sn,o.order_sn,s.lng_lat,o.total,o.yh_price,o.canju_total,o.ps_cost,o.order_id,o.juan_price,ua.detail_address,ua.lng ua_lng,ua.lat ua_lat")
            ->select();

        foreach ($cx_user as $key => $val) {
            foreach ($cx_de as $k => $v) {
                $jwd = explode(',', $v['lng_lat']);
                $cx_user[$key]['s_lng'] = $jwd[0];
                $cx_user[$key]['s_lat'] = $jwd[1];
                if ($val['person_id'] == $v['person_id']) {
//                        $address=unserialize($v['address']);
//                        $v['address']=$address;
                    $cx_user[$key]['child'][] = $v;
                    if ($v['stu'] == 2 || $v['stu'] == 3) {
                        $cx_user[$key]['child1'][] = $v;
                    }
                }
            }
            $cx_user[$key]['count'] = count($cx_user[$key]['child'], 0);
            $cx_user[$key]['count1'] = count($cx_user[$key]['child1'], 0);
        }
        if ($cx_user) {
            $this->ajaxReturn($cx_user);
        } else {
            $this->ajaxReturn(false);
        }
    }

    public function num()
    {
        $time = I('time');
//        $station_id=I('station_id');
        $this->getOrder(array('time' => $time, 'status' => array('between', '2,10')));
        $cx_order = $this->order;
        $zong_count = 0;//总单数
        $cx_quxiao = 0;//取消单数
        $cx_songda = 0;//送达单数
        $cx_jiedan = 0;//接单数
        $cx_fenpei = 0;//已分配单数
        $cx_weifenpei = 0;//未分配单数
        $cx_yiquhuo = 0;//已取货数
        $cx_yiquxiao = 0;//配送员已取消数
//        if($station_id){
//            foreach ($cx_order as $key => $v) {
//                if($v['station_id']==$station_id){
//                    $zong_count++;
//                }
//                if($v['ord_peisong_status']==5&&$v['status']==4&&$v['station_id']==$station_id){
//                    $cx_quxiao++;
//                }
//                if($v['ord_peisong_status']==4&&in_array($v['status'],array(4,5,6))&&$v['station_id']==$station_id){
//                    $cx_songda++;
//                }
//                if($v['ord_peisong_status']==2&&$v['status']==4&&$v['station_id']==$station_id){
//                    $cx_jiedan++;
//                }
//                if($v['ord_peisong_status']==1&&$v['status']==4&&$v['station_id']==$station_id){
//                    $cx_fenpei++;
//                }
//                if($v['ord_peisong_status']==0&&$v['status']==4&&$v['station_id']==$station_id){
//                    $cx_weifenpei++;
//                }
//                if($v['ord_peisong_status']==3&&$v['status']==4&&$v['station_id']==$station_id){
//                    $cx_yiquhuo++;
//                }
//                if(in_array($v['status'],array(7,8,9,10))&&$v['station_id']==$station_id){
//                    $cx_yiquxiao++;
//                }
//            }
//        }else{
        foreach ($cx_order as $key => $v) {
            $zong_count++;
            if ($v['ord_peisong_status'] == 5 && $v['status'] == 4) {
                $cx_quxiao++;
            }
            if ($v['ord_peisong_status'] == 4 && in_array($v['status'], array(4, 5, 6))) {
                $cx_songda++;
            }
            if ($v['ord_peisong_status'] == 2 && $v['status'] == 4) {
                $cx_jiedan++;
            }
            if ($v['ord_peisong_status'] == 1 && $v['status'] == 4) {
                $cx_fenpei++;
            }
            if ($v['ord_peisong_status'] == 0 && $v['status'] == 4) {
                $cx_weifenpei++;
            }
            if ($v['ord_peisong_status'] == 3 && $v['status'] == 4) {
                $cx_yiquhuo++;
            }
            if (in_array($v['status'], array(7, 8, 9, 10))) {
                $cx_yiquxiao++;
            }
        }
//        }
        $count['a'] = $zong_count;
        $count['c'] = $cx_songda;
        $count['d'] = $cx_jiedan;
        $count['e'] = $cx_fenpei;
        $count['f'] = $cx_weifenpei;
        $count['g'] = $cx_yiquxiao;
        $count['h'] = $cx_yiquhuo;
        $this->ajaxReturn($count);
    }


    public function dismap()
    {
        $order_id = I('order_id');
        if ($order_id) {
            $order = M('order');
            $cx_order = $order->alias('o')
                ->join("LEFT JOIN __DELIVERY_PERSON__ AS p ON p.person_id = o.person_id")
                ->join("LEFT JOIN __STORE__ as s ON o.store_sn = s.store_sn")
                ->where("o.order_id = $order_id")
                ->field("o.*,s.*,p.lng p_lng,p.lat p_lat,p.person_name")
                ->select();
            //查询订单详情
            //重组订单与订单详情
            foreach ($cx_order as $key => $v) {
//                $detail=unserialize($v['order_detail']);
//                $address=unserialize($v['address']);
//                $cx_order[$key]['address']=$address;
//                $cx_order[$key]['order_detail']=$detail;
            }
            //array_push()
            $this->assign('order', $cx_order);
        }
        $this->display();
    }

    public function map()
    {
        $person_id = I('person_id');
        $cx_person = M('delivery_person')->where("person_id = $person_id")->select();
        //dump($cx_person);die;
        $this->assign('or', $cx_person);
        $this->display();
    }

    public function timepeople()
    {
        //获取得到的时间
        $time = I('time');
        $this_time = explode('-', $time);
        $year = $this_time[0];
        $month = $this_time[1];
        $day = $this_time[2];
        if ($time) {
            $this->getOrder('', array('ygzt' => 0));
            $where1['status'] = array(array('eq', 2), array('eq', 3), array('eq', 4), 'or');
            $where1['year'] = $year;
            $where1['month'] = $month;
            $where1['day'] = $day;
            $cx_user = $this->person;
            $cx_de = M('delivery_detail')->where($where1)->field("status,person_id")->select();
            foreach ($cx_user as $key => $val) {
                foreach ($cx_de as $k => $v) {
                    $jwd = explode(',', $val['lng_lat']);
                    $cx_user[$key]['s_lng'] = $jwd[0];
                    $cx_user[$key]['s_lat'] = $jwd[1];
                    if ($val['person_id'] == $v['person_id']) {
                        $cx_user[$key]['child'][] = $v;
                        if ($v['status'] == 2 || $v['status'] == 3) {
                            $cx_user[$key]['child1'][] = $v;
                        }
                    }
                }
                $cx_user[$key]['count'] = count($cx_user[$key]['child'], 0);
                $cx_user[$key]['count1'] = count($cx_user[$key]['child1'], 0);
            }
            $this->ajaxReturn($cx_user);
        }
    }

    //骑手轨迹页面
    public function guiji()
    {
        //骑手轨迹
        $where['d.year'] = $year = date('Y');
        $where['d.month'] = $month = date('m');
        $where['d.day'] = $day = date('d');
        $where['d.status'] = 2;
        $person = M('delivery_detail');
        $cx_person = $person->alias('d')
            ->join('LEFT JOIN __DELIVERY_PERSON__ AS p ON p.person_id = d.person_id')
            ->join('LEFT JOIN __STORE__ AS s ON d.store_sn = s.store_sn')
            ->join('LEFT JOIN __ORDER__ AS o ON o.order_id = d.order_id')
            ->where($where)
            ->field('s.store_name,s.lng_lat,p.lng p_lng,p.lat p_lat,p.person_name,o.address')
            ->select();
        foreach ($cx_person as $k => $v) {
            $jwd = explode(',', $v['lng_lat']);
            $cx_person[$k]['s_lng'] = $jwd[0];
            $cx_person[$k]['s_lat'] = $jwd[1];
        }
        $this->assign('person', $cx_person);
        $this->display();
    }

    //实时监控页面
    function watch()
    {
        $uid = is_login();//测试完请把$uid改为UID
        $person = M('delivery_person');
        $person_info = $person->alias('p')->join("LEFT JOIN __ORDER__ AS o ON o.person_id = p.person_id")->where("p.uid=" . $uid)->select();
        // dump($person_info);
        //在线骑手
        $person_line_info = $person->alias('p')->join("LEFT JOIN __ORDER__ AS o ON o.person_id = p.person_id")->where("p.ygzt = 1 AND p.uid=" . $uid)->select();
        $person_line = count($person_line_info);
        $person_total = count($person_info);
        $person_bili = round($person_line / $person_total * 100, 2);
        $this->assign('person_line', $person_line);
        $this->assign('person_bili', $person_bili);
        $this->assign('person_line_info', $person_line_info);
        //超时单占比//订单量
        $stime = strtotime(date('Ymd', time()));
        $where['xd_time'] = array('between', array($stime, $stime + 86400));
        $where['p.uid'] = $uid;
        //$where['p.sq_id'] = $sq_id;
        $order = M('order')->alias('o')->join("LEFT JOIN __STORE__ AS s ON s.store_sn = o.store_sn")
            ->join("LEFT JOIN __DELIVERY_PERSON__ as p ON o.person_id = p.person_id")
            ->where($where)
            ->field('o.*,p.*,s.sh_time')
            ->select();
        $chaoshi_num = 0;
        foreach ($order as $k => $v) {
            if (($v['ps_time'] - $v['xd_time']) / 60 > $v['sh_time']) {
                $song_time = ($v['ps_time'] - $v['xd_time']) / 60;
                $map['o.order_id'] = $v['order_id'];
                $map['s.store_sn'] = $v['store_sn'];
                $map['s.sh_time'] = array('lt', $song_time);
                $re = M('order')->alias('o')->join("LEFT JOIN __STORE__ AS s ON s.store_sn = o.store_sn")
                    ->where($map)->count();
                $chaoshi_num += $re;
            }
        };
        $order_num = count($order);
        $chaoshi_bili = round($chaoshi_num / $order_num * 100, 2);
        $this->assign('chaoshi_bili', $chaoshi_bili);
        $this->assign('order_num', $order_num);
        //人均负载
        $average_fuzai = round($order_num / $person_line, 2);
        $this->assign('average_fuzai', $average_fuzai);
        //进单量曲线图
        //昨天
        $a = $stime - 86400 - 9900;
        //今天
        $aa = $stime - 9900;
        for ($i = 0; $i < 17; $i++) {
            $b = $a += 5400;
            $where['xd_time'] = array('between', array($b, $b + 5400));
            $yesterday[] = M('order')->alias('o')->join("LEFT JOIN __STORE__ AS s ON s.store_sn = o.store_sn")
                ->join("LEFT JOIN __DELIVERY_PERSON__ as p ON o.person_id = p.person_id")
                ->where($where)
                ->count();
            $bb = $aa += 5400;
            $where['xd_time'] = array('between', array($bb, $bb + 5400));
            $today[] = M('order')->alias('o')->join("LEFT JOIN __STORE__ AS s ON s.store_sn = o.store_sn")
                ->join("LEFT JOIN __DELIVERY_PERSON__ as p ON o.person_id = p.person_id")
                ->where($where)
                ->count();
        }
        $this->assign('yesterday', $yesterday);
        $this->assign('today', $today);
        $this->assign('person_info', $person_info);
        $this->display();
    }

    public function gaipai(){

        //接收到订单ID和现在配送员ID,返回页面
        $person_id = I('person_id');
        $order_id = I('order_id');
        $this->assign('order_id', $order_id);
        $this->assign('person_yid', $person_id);
        //查询此条订单ID的信息，在I页面上显示
        $order = M('order');
        $cx_order = $order->where('order_id = ' . $order_id)->select();
        $this->assign('order', $cx_order);
        //改派页面配送员选择下拉菜单内容
        $person = M('delivery_person');
        $where['founder_id'] = FID;
        $where['ygzt'] = 0;
        $where['person_id'] != $person_id;
        $cx_person = $person->where($where)->select();
        $this->assign('person', $cx_person);
        //改派页面的原配送员信息
        $cx_person = $person->where("person_id = $person_id")->select();
        $this->assign('yperson', $cx_person);
        if (IS_POST) {
            //接受到被改派的配送员ID
            $id = I('post.order_id');
            $info = M('order')->where("order_id = $id")->find();

            M('order')->startTrans();//开启事务
            //根据原来的订单状态修改订单表状态
            $ord['person_id'] = I('post.person_id');
            if ($info['ord_peisong_status'] == 2) {
                $ord['ord_peisong_status'] = 2;
            } elseif ($info['ord_peisong_status'] == 3) {
                $ord['ord_peisong_status'] = 3;
            }

            $save_order = M('order')->where("order_id = $id")->save($ord);

            //根据原来的状态修改配送详情表状态
            $data['person_id'] = I('post.person_id');
            $data['peson_yid'] = I('post.person_yid');
            $data['gp_content'] = I('post.gp_content');
            if ($info['ord_peisong_status'] == 2) {
                $data['status'] = 2;
            } elseif ($info['ord_peisong_status'] == 3) {
                $data['status'] = 3;
            }

            $data['assign_time'] = time();
            $data['ori_order_id'] = $order_id;
            $save_person = M('delivery_detail')->where("order_id = $id")->save($data);

            $gaipaiTel = M('delivery_person')->where('person_id=' . $data['person_id'])->field('phone')->find();
            if ($save_person && $save_order) {
                M('order')->commit();
                action_log('reassign_action', 'order,delivery_detail', "$id,$id", is_login());
                vendor("phpTui.PushMsg#class");
                $igt = new \PushMsg();
                $igt->pushMsgToApp($cx_person[0]["clientId"]);
                push_msg_client($gaipaiTel['phone']);
                $this->success('改派成功', U('Dispatch/index'));
            } else {
                M('order')->rollback();
                $this->error('改派失败');
            }
        }
        $this->display();
    }


    public function orderInfo()
    {
        $this->getOrder(array('status' => 4));
        $cx_person = $this->person;
        $info = $this->order;
        foreach ($info as $k => $v) {
//            $info[$k]['address']=unserialize($v['address']);

            if ($v['status'] == 4 && $v['ord_peisong_status'] != 4) {
                $fwq_time = time();  //当前服务器时间
                $yq_time = $fwq_time - $v['confirm_time'];   //接单后用去时间
                $shengyu_time = $v['sh_time'] * 60 - $yq_time;   //用去时间与45分钟对比，大于0说明在45分钟内，小于0说明超出45分钟
                $v['shengyu_time'] = $shengyu_time;

                $shengyu_abs = abs($shengyu_time);
                $minute = 0; //时间默认值
                $minute = floor($shengyu_abs / 60);

                if ($shengyu_time >= 0) {
                    $v['shengyu_minute'] = $minute;
                } else {
                    $v['shengyu_minute'] = (-$minute);
                }
            }
            if ($v['status'] == 4 && $v['ord_peisong_status'] == 4) {
                $ps_time = $v['ps_time'] - $v['confirm_time'];   //配送用去时间
                $dingge_time = $v['sh_time'] * 60 - $ps_time;    //决定最后定格的时间
                $v['dingge_time'] = $dingge_time;

                $dingge_abs = abs($dingge_time);
                $minute = 0; //时间默认值
                $minute = floor($dingge_abs / 60);

                if ($dingge_time >= 0) {
                    $v['dingge_minute'] = $minute;
                } else {
                    $v['dingge_minute'] = (-$minute);
                }
            }
            foreach ($cx_person as $kk => $vv) {
                if ($v['person_id'] == $vv['person_id']) {
                    $v['p_lng'] = $vv['lng'];
                    $v['p_lat'] = $vv['lat'];
                    $v['person_name'] = $vv['person_name'];
                }
            }
            $data[]=$v;
        }
        $this->ajaxReturn($data);
    }

    //拒单修改配送端状态
    public function judan()
    {

        $order_id = I('order_id');
        $reject_reason = I('reject_reason');
        $Order = M('order');
        $data['ord_peisong_status'] = '5';
        $data['reject_reason'] = $reject_reason;
        $data['status'] = '9';

        $info['status'] = '5';
        $a = M('delivery_detail')->data($info)->where('order_id=' . $order_id)->save() && $Order->data($data)->where('order_id=' . $order_id)->save();
        if ($a) {
            $save['judan'] = "拒单成功";
            $save['status'] = 1;
        } else {
            $save['judan'] = "拒单失败";
            $save['status'] = 0;
        }
        $this->ajaxReturn($save);
    }


    public function online(){
        $online=I('online');
        $this->getOrder('',array('person_id'=>0));
        $cx_user = $this->person;
//        dump($cx_user);
        $where1['year'] = date('Y');
        $where1['month'] = date('m');
        $where1['day'] = date('d');
        $where1['status'] = array(array('eq',2),array('eq',3),array('eq',4),'or');
        $cx_de = M('delivery_detail')->where($where1)->field("status,person_id")->select();

        $where2['year'] = date('Y');
        $where2['month'] = date('m');
        $where2['day'] = date('d');
        $where2['status'] = array(array('eq',4),array('eq',5),'or');
        $cx_li = M('delivery_detail')->where($where2)->field("status,person_id")->select();
        $zaixian=0;
        $lixian=0;
        foreach($cx_user as $k=>$v){

            if(in_array($v['phone'],json_decode($online,true))){
                $zaixian++;
                foreach ($cx_de as $kk => $vv) {
                    if ($v['person_id'] == $vv['person_id']) {
                        $v['child'][] = $vv;
                        if($vv['status']==2||$vv['status']==3){
                            $v['child1'][]= $vv;
                        }
                    }
                }
                $v['count'] = count($v['child'], 0);
                $v['count1'] = count($v['child1'], 0);
                $v['color'] = 1;
                $res1[]=$v;
            }else{
                $lixian++;
                foreach ($cx_li as $kk => $vv) {
                    if ($v['person_id'] == $vv['person_id']) {
                        $v['child'][] = $vv;
                        if($vv['status']==2||$vv['status']==3){
                            $v['child1'][]= $vv;
                        }
                    }
                }
                $v['count'] = count($v['child'], 0);
                $v['count1'] = count($v['child1'], 0);
                $v['color'] = 0;
                $res2[]=$v;
            }
            $zongji=$zaixian+$lixian;
            $res=array_merge($res1,$res2);
        }
        $this->ajaxReturn(array('zongji'=>$zongji,'zaixian'=>$zaixian,'lixian'=>$lixian,'res1'=>$res1,'res2'=>$res2,'res'=>$res));
    }


    //定位骑手查询
    public function personName()
    {

        $person_name = I('post.person_name');
        $delivery_person = M('delivery_person');

        //获得当前登陆用户的身份
        $user = M('admin_user');
        $uid = is_login();
        $re = $user->where("id = $uid AND status = 1")->field('user_type,founder_id,id')->find();

        //配送员查询条件
        $where['dp.ygzt'] = 0;
        if (!empty($person_name)) {
            $where['dp.person_name'] = array('like', array("$person_name%"));
        }
        //判断是否注册公司
        if ($re['user_type'] == 'company') {
            $where['dp.founder_id'] = $re['founder_id'];
            $cx_user = $delivery_person->alias('dp')->where($where)->select();
//            dump($cx_user);
        }
        //判断是否某个商家
        if ($re['user_type'] == 'company_member') {
            $where['dp.uid'] = $re['id'];
            $cx_user = $delivery_person->alias('dp')->where($where)->select();
        }

        //查配送员和订单关系
        $where1['year'] = date('Y');
        $where1['month'] = date('m');
        $where1['day'] = date('d');
        $where1['status'] = array(array('eq', 2), array('eq', 3), array('eq', 4), 'or');
        $cx_de = M('delivery_detail')->where($where1)->field("status,person_id")->select();
        foreach ($cx_user as $key => $val) {
            foreach ($cx_de as $k => $v) {
                if ($val['person_id'] == $v['person_id']) {
                    $cx_user[$key]['child'][] = $v;
                    if ($v['status'] == 2 || $v['status'] == 3) {
                        $cx_user[$key]['child1'][] = $v;
                    }
                }
            }
            $cx_user[$key]['count'] = count($cx_user[$key]['child'], 0);
            $cx_user[$key]['count1'] = count($cx_user[$key]['child1'], 0);
        }
        $this->ajaxReturn($cx_user);
    }
}
