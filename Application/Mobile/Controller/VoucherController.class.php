<?php
namespace Mobile\Controller;
use \Think\Controller;
class VoucherController extends Controller{
    public function index(){
        if(!is_member_login()){
            redirect(U("Index/getFollow"));
        };
        $quan = M('user_bao');
        $uid = is_member_login();
        $flag=I('get.flag','');
        $time = strtotime(date('Y-m-d',time()));
        $where['wm_ucenter_member.id']=$uid;
        if($flag){
            if($flag==1){
                $where['wm_user_bao.expire_time']=array('egt',$time);
                $where['wm_user_bao.status']=0;
            }elseif($flag==2){
                $where['wm_user_bao.status']=1;
            }elseif($flag==3){
                $where['wm_user_bao.expire_time']=array('lt',$time);
                $where['wm_user_bao.status']=0;
            }
            $info =$quan->join('LEFT JOIN wm_ucenter_member ON wm_ucenter_member.mobile = wm_user_bao.user_phone')
                ->where($where)
                ->select();
            $this->ajaxReturn($info);
        }else{
            $where['wm_user_bao.expire_time']=array('egt',$time);
            $where['wm_user_bao.status']=0;
            $info =$quan->join('LEFT JOIN wm_ucenter_member ON wm_ucenter_member.mobile = wm_user_bao.user_phone')
                ->where($where)
                ->select();
            $this->assign('info',$info);
        }
        $this->assign('src',$_GET['src']);
        $this->display();
    }
}