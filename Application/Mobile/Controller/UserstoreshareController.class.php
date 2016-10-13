<?php

namespace Mobile\Controller;
use Think\Controller;
use Think\Template\Driver\Mobile;

class UserstoreshareController extends Controller{
     public function index(){
        $uid = Mis_login();
        if(Mis_login()){
            //查询用户名下的餐品
            $year = date("Y", time());
            $month = date("n", time());
            $canpin = M('favorites_cm')
                ->alias('f')
                ->join('LEFT JOIN wm_canming c ON f.cm_id=c.cm_id')
                ->join('LEFT JOIN wm_goods_count g ON f.cm_id=g.cm_id')
                ->where("f.uid= $uid")
                ->field('f.*,c.*,g.quantity')
                ->order('f.fa_c_id asc')
                ->select();
            $this->assign('canpin',$canpin);//赋值模板变量
            $this->assign('uid',$uid);
            $name=M('member')->where('uid='.$uid)->getField('nickname');
            $this->assign('name',$name);
            $this->display();
        }else{
            $this->redirect('User/login');
        }
    }
        
    }
