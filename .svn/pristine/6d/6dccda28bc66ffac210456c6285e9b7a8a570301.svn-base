<?php

/**
 * 后台库存报表控制器
 * @author CHANGKAI <job_ck@126.com>
 */

namespace Platform\Controller;
use Admin\Controller\AdminController;

class BaobiaoController extends AdminController {
    /**
     * 后台平台库存报表index
     * @author CHANGKAI <job_ck@126.com>
     */
    public function index(){
        if(IS_POST){
        $storage_id = I('post.storage_id');
		$where['storage_id']=$storage_id;
		$res = M('ptkucun')->where($where)->select();
		$this->assign('list',$res);
		$time = date('Y-m-d');
        $this->assign('time',$time);
		$storage=M('storage')->select();
        $this->assign('storage',$storage);
		$storage1=M('storage')->where($where)->find();
		$this->assign('titleid',$storage1['storage_id']);
		$this->assign('title',$storage1['storage_name']);
        }
        else{
        $time = date('Y-m-d');
        $this->assign('time',$time);
		$storage=M('storage')->select();
        $this->assign('storage',$storage);
		$res = M('ptkucun')->select();
		$this->assign('list',$res);
		$this->assign('title','请选择仓库');
        }
        
        $this->meta_title='平台库存';
        $this->display();
    }
	
	
	
	/**
     * 后台商家库存报表index
     * @author CHANGKAI <job_ck@126.com>
     */
    public function shangjia(){
        if(IS_POST){
        $store_id = I('post.store_id');
		$where['store_id']=$store_id;
		$res = M('store_kucun')->where($where)->select();
		$this->assign('list',$res);
		$time = date('Y-m-d');
        $this->assign('time',$time);
		$storage=M('store')->select();
        $this->assign('storage',$storage);
		$storage1=M('store')->where($where)->find();
		$this->assign('titleid',$storage1['store_id']);
		$this->assign('title',$storage1['store_name']);
        }
        else{
        $time = date('Y-m-d');
        $this->assign('time',$time);
		$storage=M('store')->select();
        $this->assign('storage',$storage);
		$res = M('store_kucun')->select();
		$this->assign('list',$res);
		$this->assign('title','请选择商家');
        }
        
        $this->meta_title='商家库存';
        $this->display();
    }
    //审核订单对应餐具的用量，从商家库存中减去这些餐具的用量
    public function shenhe(){
        if($_GET){

            $flag = I('get.key');
            if($flag==0){
                $order_id=I('get.order_id');
                $where['order_id']=$order_id;
                $where['status']=array('in','4,5,6');
                $where['ord_peisong_status']=4;
                $where['is_shenhe']=0;
                $order = M('order')
                    ->where($where)
                    ->field('order_id,order_sn,store_id')
                    ->select();
                $detail=M('order_detail')
                    ->alias('d')
                    ->join('LEFT JOIN wm_canming c ON d.cm_id=c.cm_id')
                    ->join('LEFT JOIN wm_shangpin s ON c.sp_id=s.sp_id')
                    ->where("s.sp_id!=7")
                    ->field('d.order_id,d.cm_id,d.quantity,c.sp_id,s.sp_name')
                    ->select();
                foreach($order as $k=>$v){
                    foreach($detail as $ke=>$va){
                        if($v['order_id']==$va['order_id']){
                            $order[$k]['child'][] = $va;
                        }
                    }
                }
                foreach($order[0]['child'] as $k=>$v){
                    if(isset($data[$v['sp_id']])){
                        $data[$v['sp_id']]['quantity']=$data[$v['sp_id']]['quantity']+$v['quantity'];
                    }else{
                        $data[$v['sp_id']]=array('sp_id'=>$v['sp_id'],'sp_name'=>$v['sp_name'],'quantity'=>$v['quantity'],'store_id'=>$order[0]['store_id'],'order_id'=>$order[0]['order_id']);
                    }
                }
                $this->ajaxReturn($data);
            }else{
                $order_id=I('get.order_id');
                $where['order_id']=$order_id;
                $where['status']=array('in','4,5,6');
                $where['ord_peisong_status']=4;
                $where['is_shenhe']=0;
                $order = M('order')
                    ->where($where)
                    ->field('order_id,order_sn,store_id')
                    ->select();
                $detail=M('order_detail')
                    ->alias('d')
                    ->join('LEFT JOIN wm_canming c ON d.cm_id=c.cm_id')
                    ->join('LEFT JOIN wm_shangpin s ON c.sp_id=s.sp_id')
                    ->where("s.sp_id!=7")
                    ->field('d.order_id,d.cm_id,d.quantity,c.sp_id,s.sp_name')
                    ->select();
                foreach($order as $k=>$v){
                    foreach($detail as $ke=>$va){
                        if($v['order_id']==$va['order_id']){
                            $order[$k]['child'][] = $va;
                        }
                    }
                }
                foreach($order[0]['child'] as $k=>$v) {
                    if (isset($data[$v['sp_id']])) {
                        $data[$v['sp_id']]['quantity'] = $data[$v['sp_id']]['quantity'] + $v['quantity'];
                    } else {
                        $data[$v['sp_id']] = array('sp_id' => $v['sp_id'], 'quantity' => $v['quantity'], 'store_id' => $order[0]['store_id']);
                    }
                }
                $m=M('store_kucun');//或者是M();
                $m->startTrans();//在第一个模型里启用就可以了，或者第二个也行
                foreach($data as $key => $val){
                    $map['sp_id']=$val['sp_id'];
                    $map['store_id']=$val['store_id'];
                    $ra = $m->where($map)->field('quantity')->find();
                    if($ra['quantity']>=$val['quantity']){
                        $re = $m->where($map)->setDec('quantity',$val['quantity']);
                    }
                    else{
                        $this->ajaxReturn('商家库存不足！审核失败！');
                    }
                }
                if($re){
                    $m->commit();
                    M('order')->where("order_id=".$order_id)->setField('is_shenhe',1);
                    $this->ajaxReturn(true);//成功则提交
                }else{
                   $m->rollback();//不成功，则回滚
                }
            }
        }
        elseif($_POST){
            $store_id=I('post.store_id');
            $where['store_id']=$store_id;
            $where['status']=array('in','4,5,6');
            $where['ord_peisong_status']=4;
            $where['is_shenhe']=0;
            $order = M('order')
                ->where($where)
                ->field('order_id,order_sn,store_id')
                ->select();
            $detail=M('order_detail')
                ->alias('d')
                ->join('LEFT JOIN wm_canming c ON d.cm_id=c.cm_id')
                ->field('d.order_id,d.cm_id,d.quantity,c.sp_id')
                ->select();
            foreach($order as $k=>$v){
                foreach($detail as $ke=>$va){
                    if($v['order_id']==$va['order_id']){
                        $order[$k]['child'][] = $va;
                    }
                }
            }
            $this->assign('order',$order);
            $store=M('store')->select();
            $this->assign('store',$store);
            $this->assign('title','请选择商家');
        }
        else{
            $where['status']=array('in','4,5,6');
            $where['ord_peisong_status']=4;
            $where['is_shenhe']=0;
            $order = M('order')
                ->where($where)
                ->field('order_id,order_sn,store_id')
                ->select();
            $detail=M('order_detail')
                ->alias('d')
                ->join('LEFT JOIN wm_canming c ON d.cm_id=c.cm_id')
                ->field('d.order_id,d.cm_id,d.quantity,c.sp_id')
                ->select();
            foreach($order as $k=>$v){
                foreach($detail as $ke=>$va){
                    if($v['order_id']==$va['order_id']){
                        $order[$k]['child'][] = $va;
                    }
                }
            }
            $this->assign('order',$order);
            $store=M('store')->select();
            $this->assign('store',$store);
            $this->assign('title','请选择商家');
        }
        $this->meta_title='商家库存审核';
        $this->display();
    }
}