<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/12
 * Time: 17:09
 */

namespace Home\Controller;

class CopyuserController extends HomeController{

    public function index(){

        $map='id <400 OR id>25899';
        $map1='uid <400 OR uid>25899';

        $res=M('UcenterMember')->where($map)->field('id,username,password,email,mobile,status')->select();

        $res1=M('Member')->where($map1)->field('uid,nickname,push_msg_id,user_type')->select();
        $temp=array();
        foreach($res as $key=> $val){
            foreach($res1 as $k=>$v){
                if($v['uid']==$val['id']){
                    $temp[]=array('id'=>$val['id'],'user_type'=>$v['user_type'],'username'=>$val['username'],'password'=>$val['password'],'email'=>$val['email'],'mobile'=>$val['mobile'],'status'=>$val['status'],'push_msg_id'=>$v['push_msg_id'],'nickname'=>$v['nickname']);
                }
            }

        }
        //dump($temp);
        M('AdminUser')->addAll($temp);
    }
}