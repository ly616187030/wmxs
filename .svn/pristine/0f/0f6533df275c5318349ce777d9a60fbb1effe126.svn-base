<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/1
 * Time: 17:41
 */
namespace Mobile\Controller;
use User\Controller\Home\CenterController;
class UsercenterController extends CenterController {
    public function index(){
        //用户中心信息
        if(is_member_login()){
            $id = is_member_login();
            $where['tj_id']=$id;
            $count = M('share_detail')->where($where)->count();
            $this->assign('tj_num',$count);
            $where1['tj_id']=$id;
            $where1['award']=2;
            $price = M('share_detail')->where($where1)->sum('award_price');
            if($price){
                $this->assign('price',$price);
            }else{
                $this->assign('price','0.00');
            }

            $phone = M('member_user')->getFieldByid($id,'mobile');
            $map['user_phone']=$phone;
            $map['expire_time']=array('egt',time());
            $map['status']=0;
            $countbao = M('user_bao')->where($map)->count();
            $this->assign('countbao',$countbao);
        }
        $qy_id=$_COOKIE['companyid'];

        //登陆页面logo
        $other_weixin=M('other_weixin');
        if($qy_id) {
            $other = $other_weixin->where($where1)->find();
        }
        $this->assign('reddd','reddd');
        $this->assign('qy_id',$qy_id);
        $this->assign('other',$other);
        $this->display();
    }
    public function nologin(){
        //没登录跳转的页面
        $uid = is_member_login();
        $model_order = M('order');
        $cx_order = $model_order->where("uid = $uid")->select();
        if($cx_order){
            $this->display('Usercenter/index');
        }else{
            $this->display();
        }


    }
    public function xianjin(){
        if(IS_POST){
            $flag = I('post.flag');
            if($flag==1){
                $share = M('share_detail'); // 实例化User对象
                $id = is_member_login();
                $where['tj_id']=$id;
                $where['award']=2;
                $where['grant_status']=0;
                $list = $share
                    ->where($where)
                    ->order('share_detail_id desc')
                    ->select();
                $this->ajaxReturn($list);
            }else{
                $share = M('share_detail'); // 实例化User对象
                $id = is_member_login();
                $where['tj_id']=$id;
                $where['award']=2;
                $where['grant_status']=1;
                $list = $share
                    ->where($where)
                    ->order('share_detail_id desc')
                    ->select();
                $this->ajaxReturn($list);
            }
        }else{
            $share = M('share_detail'); // 实例化User对象
            $id = is_member_login();
            $where['tj_id']=$id;
            $where['award']=2;
            $where['grant_status']=0;
            $list = $share
                ->where($where)
                ->order('share_detail_id desc')
                ->select();
            $this->assign('list',$list);
            $this->display();
        }
    }
}