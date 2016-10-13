<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-11-04
 * Time: 10:43
 */

namespace Mobile\Controller;
use Think\Controller;
use Think\Model;
class RedpacketsController extends Controller{

    public function index(){

        $m = M('Bao');
        $where['bao_status'] =0;
        $where['end_time'] = array('gt',time());
        $data = $m->where($where)->order('sort_order ASC')->find();
        if(IS_POST){
            $baoid=$data['bao_id'];
            $phone=I('user_phone');
            $baonum=$data['bao_price'];
            $user=M('UserBao');
            $map['bao_id']=$map1['bao_id']=$baoid;
            $map['user_phone']=$phone;
            $ishas=$user->where($map)->find();
            //$counts=$user->where($map1)->count();
            $counts=$data['remain_quantity'];
            if(empty($ishas) && $counts<$baonum){
                $price=mt_rand(intval($data['bao_lowest']),intval($data['bao_heighest']));
                $data1['bao_id']=$data['bao_id'];
                $data1['user_bao_price']=$price;
                $data1['user_condition']=$data['bao_condition'];
                $data1['condition_desc']=$data['bao_condition_desc'];
                $data1['expire_time']=$data['end_time'];
                $data1['user_phone']=$phone;
                    if($user->add($data1)){
                        $m->where('bao_id='.$data['bao_id'])->setInc('remain_quantity',1);
                        $this->ajaxReturn(array('status'=>1,'price'=>$price,'desc'=>$data['bao_condition_desc']));
                    }else{
                        $this->error('新增失败');
                    }

            }elseif($counts>=$baonum){
                $m->where($map1)->setField('bao_status',1);
                $this->ajaxReturn(array('status'=>2));

            }elseif(!empty($ishas)){
                $this->ajaxReturn(array('status'=>0,'price'=>$ishas['user_bao_price'],'desc'=>$ishas['condition_desc']));
            }
        }else{
            $this->assign('list',$data);
            $this->display();
        }
    }
}
?>