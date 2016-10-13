<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\CommonController;
use Think\Verify;
/**
 * 后台唯一不需要权限验证的控制器
 * @author jry <598821125@qq.com>
 */
class PublicController extends CommonController {
    /**
     * 后台登陆
     * @author jry <598821125@qq.com>
     */
    public function login() {
        if(is_waps()){
            exit('<h4 style="padding:20px 30px;font-size:26px;">请使用PC电脑登录！</h4>');
        }
        if (IS_POST) {
            $username = I('username');
            $password = I('password');
            $remember_login = I('remember_login');
            if($username){
                cookie('login_name',$username,604800);
            }
            // 验证用户名密码是否正确
            $user_object = D('Admin/User');
            if (!$user_object->autoCheckToken($_POST)){
                // 令牌验证错误
                $this->error('表单令牌验证错误');
            }
                $user_info = $user_object->login($username, $password);
                if (!$user_info) {
                    $this->error($user_object->getError());
                }
            if($remember_login){
                cookie('remember_login',$username,604800);
            }else{
                cookie('remember_login',null);
            }

                // 验证管理员表里是否有该用户
                $account_object = D('Admin/Roleuser');
                $where['user_id'] = $user_info['id'];
                $account_info = $account_object->where($where)->find();

                if (!$account_info) {
                    $return['status'] = false;
                    $return['info'] ='该用户没有管理员权限'.$account_object->getError();
                    $this->ajaxReturn($return, 'JSON');
                }
                $uid = $user_object->auto_login($user_info);

                //dump(0 < $account_info['user_id'] && $account_info['user_id'] === $uid);
                // 设置登录状态
                // 跳转
                if (0 < $account_info['user_id'] && $account_info['user_id'] === $uid) {
                    $return['status'] = true;
                    $return['url'] = U('Admin/Index/index');
                    $return['info'] ='登录成功！';
                    cookie('login_name',null);
                    $this->ajaxReturn($return, 'JSON');
                } else {
                    $return['status'] = false;
                    $return['url'] = U('logout');
                    $return['info'] = '登录失败！';
                    $this->ajaxReturn($return, 'JSON');
                }
        } else {
            $this->assign('remeber_user',cookie('remember_login'));
            $this->assign('login_name',cookie('login_name'));
            $this->assign('meta_title', '管理员登录');
            $this->display();
        }
    }

    /**
     * 后台登陆
     * @author jry <598821125@qq.com>
     */
    public function register(){

        if(IS_POST){

            $data=I('post.');
            $user_object = D('Admin/User');

            if($user_object->create($data)){
                $user_id=$user_object->add();
                //添加用户到管理员表
                $account_object = D('Admin/Access');
                $account_object->uid=$user_id;
                $account_object->group= 12 ;
                $account_object->create_time=time();
                $account_object->status= 1 ;

                if($account_object->add()){
                    $this->success('注册成功！', U('login'));
                }else{
                    $this->error('注册失败！');
                }
            }else{
                $this->error($user_object->getError());
            }
        }else{
            $this->assign('meta_title', '体验注册');
            $this->display();
        }
    }

    /**
     * 注销
     * @author jry <598821125@qq.com>
     */
    public function logout() {
        action_log('user_logout', 'session', is_login(), is_login());
        session('user_auth', null);
        session('user_auth_sign', null);
        $this->success('退出成功！', U('login'));
    }

    /**
     * 图片验证码生成，用于登录和注册
     * @author jry <598821125@qq.com>
     */
    public function verify($vid = 1) {
        $verify = new Verify();
        $verify->length = 4;
        $verify->entry($vid);
    }

    /**
     * 检测验证码
     * @param  integer $id 验证码ID
     * @return boolean 检测结果
     */
    function check_verify($code, $vid = 1) {
        $verify = new Verify();
        return $verify->check($code, $vid);
    }

}
