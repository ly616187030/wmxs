<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com>
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

/**
 * 订单控制器
 * 包括全部订单，待评价订单，订单追踪，催单，取消订单。
 */
class OrderlistController extends HomeController {
    /*创建订单方法*/
    protected function _initialize(){
        parent::_initialize();
        $this->assign('_controller',CONTROLLER_NAME);
    }
// 1.未支付订单(在线支付，未完成支付)
// 2.已支付订单（在线支付，完成支付）
// 3.餐到付款
// 4.商家已确认（商家接单）
// 5.完成订单（餐已送达，确认,等待评价）
// 6.完成评价(彻底完成订单)
// 7.取消订单（用户下单后，没有支付直接取消）
// 8.取消已付款订单（用户支付，顾客取消订单，转到退款）
// 9.商家拒单（用户货到付款）
// 10.商家拒单（用户在线支付后，商家拒单，转到退款）
// 11.退款审核中
// 12.完成退款，此单已完成
// 13.用户下单，商家接单，后用户取消订单
// 14.制作完成
    public function index(){
        $this->login();
        $uid = UID;
        $orderlist = M('order');//实例化order对象
        $count = $orderlist->where('uid=' . $uid)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $orderlist->join('LEFT JOIN wm_store ON wm_store.store_id = wm_order.store_id')
            ->where('wm_order.uid='.$uid)->limit($Page->firstRow.','.$Page->listRows)
            ->order('order_id desc')
            ->field('wm_order.*,wm_store.order_deft,wm_store.order_dct,wm_store.service_call')->select();//查询订单表中的数据
        $Page->setConfig('prev',' 上一页 ');
        $Page->setConfig('next','下一页');
        $Page->setConfig('theme', '%UP_PAGE% <span class="current">%NOW_PAGE%</span> %DOWN_PAGE%');
        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('count',$count);//将订单的总数量返回到HTML
        $this->assign("_position","我的订单");
        //查询待订单付款
        $fu['uid'] = $uid;
        $fu['status'] = 1;
        $daifu = $orderlist->where($fu)->count();
        $this->assign('daifu',$daifu);

        //查询待评价付款
        $pj['uid'] = $uid;
        $pj['status'] = 5;
        $pingjia = $orderlist->where($pj)->count();
        $this->assign('pingjia',$pingjia);

        $this->assign('list',$list);
        $this->assign('is_login',is_login());
        $this->display();
    }

        public function detail(){
            $this->login();
            $id = I('get.order_id');
            $where['order_id']=$id;
            $list = M('order')->where($where)->find();
            $this->assign('list',$list);
            $res = M('delivery_detail')->where($where)->find();
            $this->assign('res',$res);

            //通过订单ID查询餐名
             $detail = M('order_detail')
             ->alias('d')->where($where)
             ->join('LEFT JOIN wm_canming cm ON d.cm_id=cm.cm_id')
             ->select();
             $canpin = M('canpin')->select();
            foreach($canpin as $key=>$v){
                foreach($detail as $k=>$cv){
                    if($v['canpin_id'] == $cv['canping_id']){
                     $canpin[$key]['child'][] = $cv;
                    }
                }
                $canpin[$key]['count'] = count($list[$key]['child'],0);
             }
            $this->assign('canpin',$canpin);

            $shuliang = M('tableware')->find();
            $this->assign('shuliang',$shuliang);
            $this->assign('is_login',is_login());
            $this->display();
        }
        //将评价框中获取到的订单评价存入数据库
    public function createpj(){
        $pingjia = M('pingjia');//实例化评价表
        $order = M('order');
        $pj = I('post.');
        if($pj['pingfen']==''){
            $this->ajaxReturn('pingfen');
            return;
        }
        if($pj['pingfen1']==''){
            $this->ajaxReturn('pingfen');
            return;
        }
        if($pj['pingfen2']==''){
            $this->ajaxReturn('pingfen');
            return;
        }
        if($pj['content']==''){
            $this->ajaxReturn('content');
            return;
        }
        if($pj){
            $store = $order->where('order_id='.$pj['order_id'])->find();
            $data['order_id'] = $pj['order_id'];
            $data['store_id']=$store['store_id'];
            $data['uid']=$store['uid'];
            $data['content']=$pj['content'];
            $data['pingfen']=$pj['pingfen'];
            $data['pj_songda']=$pj['pingfen1'];
            $data['pj_service']=$pj['pingfen2'];
            $data['status']=1;
            $data['pj_time']=time();
            $add = $pingjia->add($data);
            $order->where('order_id='.$pj['order_id'])->setField('status', 6);
            if($add){$this->ajaxReturn(true);}
                else{$this->ajaxReturn(false);}


        }
    }
    public function pingjia(){
        $this->login();
        $uid = UID;
        $pj = M('pingjia');
        $where['p.uid']=$uid;
        $map['uid']=$uid;
        $count = $pj->where($map)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,5);// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $pj
        ->alias('p')
        ->join('LEFT JOIN wm_order o ON p.order_id=o.order_id')
        ->where($where)
        ->limit($Page->firstRow.','.$Page->listRows)
        ->order('p.pj_id desc')
        ->select();
        $Page->setConfig('prev',' 上一页 ');
        $Page->setConfig('next','下一页');
        $Page->setConfig('theme', '%UP_PAGE% <span class="current">%NOW_PAGE%</span> %DOWN_PAGE%');
        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('count',$count);
        $this->assign('list',$list);

        $this->assign('is_login',is_login());
        $this->display();
    }

    public function delpingjia(){
        $id = I('post.pj_id');
        $pj = M('pingjia');
        $where['pj_id']=$id;
        if($id){
           $re = $pj->where($where)->delete();
            if($re){
                $this->ajaxReturn(true);
            }else{
                $this->ajaxReturn(false);
            }
        }else{
            $this->ajaxReturn('wu');
        }
    }
     public function daifu(){
        $uid = UID;
        $orderlist = M('order');//实例化order对象
        $where['wm_order.uid']=$uid;
        $where['wm_order.status']='1';
        $countquan=$orderlist->where('uid='.$uid)->count();
        $count = $orderlist->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $orderlist->join('LEFT JOIN wm_store ON wm_store.store_id = wm_order.store_id')
            ->where($where)->limit($Page->firstRow.','.$Page->listRows)
            ->order('order_id desc')
            ->field('wm_order.*,wm_store.order_deft,wm_store.order_dct,wm_store.service_call')->select();//查询订单表中的数据
        $Page->setConfig('prev',' 上一页 ');
        $Page->setConfig('next','下一页');
        $Page->setConfig('theme', '%UP_PAGE% <span class="current">%NOW_PAGE%</span> %DOWN_PAGE%');
        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('countquan',$countquan);//将订单的总数量返回到HTML
        $this->assign("_position","我的订单");
        //查询待订单付款
        $fu['uid'] = $uid;
        $fu['status'] = 1;
        $daifu = $orderlist->where($fu)->count();
        $this->assign('daifu',$daifu);

        //查询待评价付款
        $pj['uid'] = $uid;
        $pj['status'] = 5;
        $pingjia = $orderlist->where($pj)->count();
        $this->assign('pingjia',$pingjia);

        $this->assign('list',$list);
        
        $this->display();
    }   
     public function daiping(){
        $uid = UID;
        $orderlist = M('order');//实例化order对象
        $where['uid']=$uid;
        $where['status']='5';
        $countquan=$orderlist->where('uid='.$uid)->count();
        $count = $orderlist->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $orderlist->where($where)->limit($Page->firstRow.','.$Page->listRows)->order('order_id desc')->select();//查询订单表中的数据
        $Page->setConfig('prev',' 上一页 ');
        $Page->setConfig('next','下一页');
        $Page->setConfig('theme', '%UP_PAGE% <span class="current">%NOW_PAGE%</span> %DOWN_PAGE%');
        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('countquan',$countquan);//将订单的总数量返回到HTML
        $this->assign("_position","我的订单");
        //查询待订单付款
        $fu['uid'] = $uid;
        $fu['status'] = 1;
        $daifu = $orderlist->where($fu)->count();
        $this->assign('daifu',$daifu);

        //查询待评价付款
        $pj['uid'] = $uid;
        $pj['status'] = 5;
        $pingjia = $orderlist->where($pj)->count();
        $this->assign('pingjia',$pingjia);

        $this->assign('list',$list);
        
        $this->display();
    }
    public function quxiao(){
    $id = I('post.order_id');
    $yuanyin = I('post.yuanyin');
    $liyou = I('post.liyou');
    $time=time();
        $ord = M('order');
        $data['cancel_ord_time']=$time;
        $data['cancel_ord_reason']=$yuanyin.';'.$liyou;
        $where['order_id']=$id;
        $ra = $ord->where($where)->save($data);
        if($ra){
            if($id){
                $re = $ord->where($where)->setField('status',7);
            if($re){
                $this->ajaxReturn(true);
            }else{
                $this->ajaxReturn(false);
            }
            }else{
                $this->ajaxReturn('wu');
            }
        }
    }

    public function songda(){
        $id = I('post.order_id');
        $ord = M('order');
        $where['order_id']=$id;
            if($id){
                $re = $ord->where($where)->setField('status',5);
                if($re){
                    $this->ajaxReturn(true);
                }else{
                    $this->ajaxReturn(false);
                }
            }else{
                $this->ajaxReturn('wu');
            }

    }
}  