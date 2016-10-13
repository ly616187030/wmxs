<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace User\Controller\Home;
use Mobile\Controller\MobileController;
use Think\Hook;
use Think\Model;

/**
 * 用户控制器
 * @author jry <598821125@qq.com>
 */
class UserController extends MobileController{
    /**
     * 登陆
     * @author jry <598821125@qq.com>
     */
    public function login() {
        if (IS_POST) {
            $username = I('username');
            $password = I('password');
            $remember_login=I('remember_login');
            cookie('remember_login',$remember_login);
            if (!$username) {
                $this->error('请输入账号！');
            }
            if (!$password) {
                $this->error('请输入密码！');
            }
            $user_object = D('User/User');
            $uid = $user_object->login($username, $password);

            if ($uid) {
                $this->success('登录成功！', cookie('front'));
            } else {
                $this->error($user_object->getError());
            }
        } else {
            if (is_login()) {
                redirect(U(''.MODULE_NAME.'/Index/index'));
            }
            cookie('front',$_SERVER['HTTP_REFERER']);
            $this->assign('meta_title', '用户登录');
            $this->display();
        }
    }

    /**
     * 注销
     * @author jry <598821125@qq.com>
     */
    public function logout() {
        session('user_auth', null);
        session('user_auth_sign', null);
        cookie('user_auth', null);
        cookie('user_auth_sign', null);
        cookie('remember_login', null);
        //$this->success('退出成功！', Cookie('__forward__') ? : C('HOME_PAGE'));
        redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * 用户注册
     * @author jry <598821125@qq.com>
     */
    public function register() {
        if (IS_POST) {
            if (!C('user_config.reg_toggle')) {
                $this->error('注册已关闭！');
            }
            $reg_type = I('post.reg_type');
            if (!in_array($reg_type, C('user_config.allow_reg_type'))) {
                $this->error($reg_type);
            }
            switch ($reg_type) {
                case 'username': //用户名注册
                    $username = I('post.username');
                    $_POST['nickname'] = $username;

                    //图片验证码校验
                    if (!$this->check_verify(I('post.verify'))) {
                        $this->error('验证码输入错误！');
                    }

                    // 构造注册数据
                    $reg_data = array();
                    $reg_data['user_type'] = I('post.user_type');
                    $reg_data['nickname']  = I('post.username');
                    $reg_data['username']  = I('post.username');
                    $reg_data['password']  = I('post.password');
                    $reg_data['reg_type']  = I('post.reg_type');
                    break;
                case 'email': //邮箱注册
                    $username = I('post.email');
                    $_POST['username'] = 'U'.NOW_TIME;

                    //验证码严格加盐加密验证
                    if (user_md5(I('post.verify'), $username) !== session('reg_verify')) {
                        $this->error('验证码错误！');
                    }

                    // 构造注册数据
                    $reg_data = array();
                    $reg_data['user_type'] = I('post.user_type');
                    $reg_data['nickname']  = I('post.username');
                    $reg_data['username']  = I('post.username');
                    $reg_data['password']  = I('post.password');
                    $reg_data['email']     = I('post.email');
                    $reg_data['reg_type']  = I('post.reg_type');
                    break;
                case 'mobile': //手机号注册
                    $username = I('post.mobile');
                    $_POST['username'] = 'U'.NOW_TIME;

                    //验证码严格加盐加密验证
                    if (user_md5(I('post.verify'), $username) !== session('reg_verify')) {
                        $this->error('验证码错误！');
                    }

                    // 构造注册数据
                    $reg_data = array();
                    $reg_data['user_type'] = I('post.user_type');
                    $reg_data['nickname']  = I('post.username');
                    $reg_data['username']  = I('post.username');
                    $reg_data['password']  = I('post.password')?I('post.password'):'123abc';
                    $reg_data['mobile']    = I('post.mobile');
                    $reg_data['reg_type']  = I('post.reg_type');
                    $reg_data['push_msg_id']=$dates=date('YmdHis',time());
                    break;
            }
            $user_object = D('User/User');
            $data = $user_object->create($reg_data);
            if ($data) {
                $id = $user_object->add($data);
                if ($id) {
                    session('reg_verify', null);
                    $uid = $user_object->login($username, I('post.password'));
                    if(preg_match('/register/',$_SERVER['HTTP_REFERER'])){
                        $this->success('恭喜您，注册成功！',U(''));
                    }
                    $this->success('恭喜您，注册成功！',$_SERVER['HTTP_REFERER']);
                } else {
                    $this->error('注册失败'.$user_object->getError());
                }
            } else {
                $this->error('注册失败'.$user_object->getError());
            }
        } else {
            if (is_login()) {
                redirect(U(''.MODULE_NAME.'/Index/index'));
            }
            $user_type_list = D('User/Type')->where('status = 1')->select();
            $this->assign('user_type_list', $user_type_list);
            $this->assign('meta_title', '用户注册');
            $this->display();
        }
    }

    /**
     * 用户注册2
     * @author jry <598821125@qq.com>
     */
    public function register2() {
        $uid  = $this->is_login();
        if (IS_POST) {
            if ($_POST) {
                if (!$_POST['avatar']['src'] || !$_POST['avatar']['w'] || !$_POST['avatar']['h'] || $_POST['avatar']['x'] === '' || $_POST['avatar']['y'] === '') {
                    $this->error('参数不完整');
                }
                $result = D('Admin/Upload')->crop($_POST['avatar']);
                if ($result['error'] != 1) {
                    $user_object = D('User/User');
                    $result = $user_object->where(array('id' => $uid))->setField('avatar', $result['id']);
                    if ($result) {
                        $this->success('头像设置成功', U('register3'));
                    } else {
                        $this->error('头像设置失败'.$user_object->getError());
                    }
                } else {
                    $this->error('头像保存失败');
                }
            } else {
                $this->error('请选择文件');
            }
        } else {
            $this->assign('user_info', D('User/User')->detail($uid));
            $this->assign('meta_title', '设置头像');
            $this->display('register2');
        }
    }

    /**
     * 用户注册3
     * @author jry <598821125@qq.com>
     */
    public function register3() {
        if (IS_POST) {
            // 强制设置用户ID
            $uid = $this->is_login();
            $_POST['uid'] = $uid;
            $_POST = format_data();

            // 获取用户信息
            $user_object = D('User/User');
            $user_info = $user_object->find($uid);

            // 保存信息
            $type = $user_info['user_type'];
            $map['user_type'] = array('eq', $type);
            $count = D('User/Attribute')->where($map)->count();
            if ($count) {
                $user_type_name = D('User/Type')->where(array('id' => $user_info['user_type']))->getField('name');
                $user_extend_object = M('User'.ucfirst($user_type_name));
                $extend_data = $user_extend_object->create();
                $extend_info = $user_extend_object->find($uid);
                if ($extend_info) {
                    $result = $user_extend_object->save($extend_data);
                } else {
                    $result = $user_extend_object->add($extend_data);
                }
                if ($result === false) {
                    $this->error('扩展信息修改失败'.$user_extend_object->getError());
                } else {
                    $this->success('信息修改成功', U('User/Home/Center/profile'));
                }
            }
        } else {
            // 获取当前用户
            $user_object = D('User/User');
            $user_info = $user_object->detail($this->is_login());
            $type = $user_info['user_type'];
            $user_type_info = D('User/Type')->find($type);

            // 获取扩展字段
            $map['user_type'] = array('eq', $type);
            $attribute_list[$type] = D('User/Attribute')->where($map)->select();

            // 解析字段options
            $new_attribute_list_sort['user']['type'] = 'group';
            if ($attribute_list[$type]) {
                foreach ($attribute_list[$type] as $attr) {
                    $attr['options'] = parse_attr($attr['options']);
                    $new_attribute_list[$type][$attr['id']] = $attr;
                    $new_attribute_list[$type][$attr['id']]['value'] = $user_info[$attr['name']];
                }
                $new_attribute_list_sort['user']['options']['group_extend']['title'] = '完善'.$user_type_info['title'].'信息';
                $new_attribute_list_sort['user']['options']['group_extend']['options'] = $new_attribute_list[$type];
            }

            // 使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('完善信息')  // 设置页面标题
            ->setPostUrl(U(''))        // 设置表单提交地址
            ->setExtraItems($new_attribute_list_sort)
                ->setTemplate('register3')
                ->display();
        }
    }

    /**
     * 密码重置
     * @author jry <598821125@qq.com>
     */
    public function resetPassword() {
        if (IS_POST) {
            $reg_type = I('post.reg_type');
            switch ($reg_type) {
                case 'email':
                    $username = I('post.email');
                    $condition['email'] = I('post.email');
                    break;
                case 'mobile':
                    $username = I('post.mobile');
                    $condition['mobile'] = I('post.mobile');
                    break;
            }
            //验证码严格加盐加密验证
            if (user_md5(I('post.verify'), $username) !== session('reg_verify')) {
                $this->error('验证码错误！');
            }
            $user_object = D('User/User');
            $data = $user_object->create($_POST, 5); //调用自动验证
            if (!$data) {
                $this->error($admin_user_object->getError());
            }
            $result = $user_object
                ->where($condition)
                ->setField('password', user_md5($data['password'])); //重置密码
            $uid = $user_object->login($username, I('post.password')); //自动登录
            if ($uid) {
                $this->success('密码重置成功', C('HOME_PAGE'));
            } else {
                $this->error('密码重置失败');
            }
        } else {
            $this->assign('meta_title', '密码重置');
            $this->display();
        }
    }

    /**
     * 图片验证码生成，用于登录和注册
     * @author jry <598821125@qq.com>
     */
    public function verify($vid = 1) {
        $verify = new \Think\Verify();
        $verify->length = 4;
        $verify->entry($vid);
    }

    /**
     * 检测验证码
     * @param  integer $id 验证码ID
     * @return boolean 检测结果
     */
    function check_verify($code, $vid = 1) {
        $verify = new \Think\Verify();
        return $verify->check($code, $vid);
    }

    /**
     * 邮箱验证码，用于注册
     * @author jry <598821125@qq.com>
     */
    public function sendMailVerify(){
        //生成验证码
        $reg_verify = \Org\Util\String::randString(6,1);
        session('reg_verify', user_md5($reg_verify, I('post.email')));

        //构造邮件数据
        $mail_data['receiver'] = I('post.email');
        $mail_data['subject']  = '邮箱验证';
        $mail_data['content'] = '少侠/女侠好：<br>听闻您正使用该邮箱'.I('post.email').'【注册/修改密码】，请在验证码输入框中输入：
        <span style="color:red;font-weight:bold;">'.$reg_verify.'</span>，以完成操作。<br>
        注意：此操作可能会修改您的密码、登录邮箱或绑定手机。如非本人操作，请及时登录并修改
        密码以保证帐户安全 （工作人员不会向您索取此验证码，请勿泄漏！)';
        $result = D('Addons://Email/Email')->send($mail_data);

        //发送邮件
        if ($result) {
            $this->success('发送成功，请登陆邮箱查收！');
        } else {
            $this->error('发送失败！');
        }
    }

    /**
     * 短信验证码，用于注册
     * @author jry <598821125@qq.com>
     */
    public function sendMobileVerify() {
        //生成验证码
        $reg_verify = \Org\Util\String::randString(6,1);
        session('reg_verify', user_md5($reg_verify, I('post.mobile')));

        //构造短信数据
        $phone = I('post.mobile');
        $fid=$_COOKIE['companyid'];
        $sms_account=M('sms_account');
        $info=$sms_account->where('founder_id='.$fid)->field('uid,pwd')->find();
        $uid=$info['uid'];
        $password=$info['pwd'];
        $re=\Org\Sms\Sms::sendSms($uid,$password,$phone,$reg_verify);
        if($re){
            $res[0]='发送成功';
            $res[1]='1';
        }else{
            $res[0]='发送失败';
            $res[1]='0';
        }

        $this->ajaxReturn(array('info'=>$res[0],'status'=>$res[1]));
    }

    public function emailOrPhone(){

        $regtype=I('regtype');

        $word=I("word");

        /* 调用UC登录接口登录 */

        $res=M('AdminUser')->where('mobile='.$word)->find();

        if(empty($res)){

            $this->ajaxReturn(array("status"=>1));

        }else{

            $this->ajaxReturn(array("status"=>0,"info"=>'mobile'));

        }

    }

}
