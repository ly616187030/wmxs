<?php

namespace Mobile\Controller;
use User\Controller\Home\UserController;
class UsersController extends UserController

{
    /*
 *查询用户是否被占用
 *
 */
    public function checkUsername($keyword){
        $where['username|email|mobile']=trim($keyword);
        $re=M('member_user')->where($where)->count();
        if($re>0){
            $res[0]='用户名已存在!';
            $res[1]=0;
        }else{
            $res[0]='用户可以注册!';
            $res[1]=1;
        }
        $this->ajaxReturn(array('info'=>$res[0],'status'=>$res[1]));
    }
}



