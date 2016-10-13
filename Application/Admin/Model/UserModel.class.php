<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Model;
use Think\Model;
/**
 * 用户模型
 * @author jry <598821125@qq.com>
 */
class UserModel extends Model {
    /**
     * 数据库表名
     * @author jry <598821125@qq.com>
     */
    protected $tableName = 'admin_user';

    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        //验证用户名
        array('nickname', 'require', '姓名不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),

        //验证用户名
        array('username', 'require', '请填写登录账号', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('username', '3,32', '用户名长度为1-32个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
        array('username', '', '用户名被占用', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('username', '/^(?!_)(?!\d)(?!.*?_$)[\w\一-\龥]+$/', '用户名只可含有数字、字母、下划线且不以下划线开头结尾，不以数字开头！', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),

        //验证密码
        array('password', 'require', '密码不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('password', '6,30', '密码长度为6-30位', self::MUST_VALIDATE, 'length', self::MODEL_INSERT),
        array('password', '/(?!^(\d+|[a-zA-Z]+|[~!@#$%^&*()_+{}:"<>?\-=[\];\',.\/]+)$)^[\w~!@#$%^&*()_+{}:"<>?\-=[\];\',.\/]+$/', '密码至少由数字、字符、特殊字符三种中的两种组成', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('repassword', 'password', '两次输入的密码不一致', self::EXISTS_VALIDATE, 'confirm', self::MODEL_INSERT),

        //验证密码
        array('password', 'require', '密码不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_UPDATE),
        array('password', '6,30', '密码长度为6-30位', self::EXISTS_VALIDATE, 'length', self::MODEL_UPDATE),
        array('password', '/(?!^(\d+|[a-zA-Z]+|[~!@#$%^&*()_+{}:"<>?\-=[\];\',.\/]+)$)^[\w~!@#$%^&*()_+{}:"<>?\-=[\];\',.\/]+$/', '密码至少由数字、字符、特殊字符三种中的两种组成', self::EXISTS_VALIDATE, 'regex', self::MODEL_UPDATE),
        array('repassword', 'password', '两次输入的密码不一致', self::EXISTS_VALIDATE, 'confirm', self::MODEL_UPDATE),

        //验证手机号码
       /* array('mobile', 'require', '手机号码不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('mobile', '/^1\d{10}$/', '手机号码格式不正确', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('mobile', '', '手机号被占用', self::MUST_VALIDATE, 'unique', self::MODEL_INSERT),*/

        array('mobile', 'require', '手机号码不能为空', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        array('mobile', '/^1\d{10}$/', '手机号码格式不正确', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
        array('mobile', '', '手机号被占用', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),

        //验证邮箱
       /* array('email', 'require', '邮箱不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('email', 'email', '邮箱格式不正确', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('email', '1,32', '邮箱长度为1-32个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('email', '', '邮箱被占用', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH),*/

        //详细地址验证
        //array('address', 'require', '详细地址不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),

        // 验证注册来源
        array('reg_type', 'require', '注册来源不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('user_type', 'require', '请选择用户类型', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        //权限
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('score', '0', self::MODEL_INSERT),
        array('money', '0', self::MODEL_INSERT),
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('password', 'user_md5', self::MODEL_INSERT, 'function'),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
    );

    /**
     * 用户性别
     * @author jry <598821125@qq.com>
     */
    public function user_gender($id){
        $list[0]  = '保密';
        $list[1]  = '男';
        $list[-1] = '女';
        return $id ? $list[$id] : $list;
    }

    /**
     * 用户登录
     * @author jry <598821125@qq.com>
     */
    public function login($username, $password, $map) {
        //去除前后空格
        $username = trim($username);

        //匹配登录方式
        if (preg_match("/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/", $username)) {
            $map['email'] = array('eq', $username);     // 邮箱登陆
        } elseif (preg_match("/^1\d{10}$/", $username)) {
            $map['mobile'] = array('eq', $username);    // 手机号登陆
        } else {
            $map['username'] = array('eq', $username);  // 用户名登陆
        }

        $map['status']   = array('eq', 1);
        $user_info = $this->where($map)->find(); //查找用户
        if (!$user_info) {
            $this->error = '用户不存在或被禁用！';
        } else {
            if (user_md5($password) !== $user_info['password']) {
                $this->error = '密码错误！';
            } else {
                action_log('user_login', 'admin_user', $user_info['id'], $user_info['id']);
                return $user_info;
            }
        }
        return false;
    }

    /**
     * 设置登录状态
     * @author jry <598821125@qq.com>
     */
    public function auto_login($user) {
        // 记录登录SESSION和COOKIES
        $auth = array(
            'uid'      => $user['id'],
            'username' => $user['username'],
            'nickname' => $user['nickname'],
            'avatar'   => $user['avatar'],
            'push_msg_id' => $user['push_msg_id'],
            'founder_id' => $user['founder_id']
        );
        $aa = cookie('remember_login');
        /*if(!empty($aa)){
            cookie('user_auth', serialize($auth),86400*7);
            cookie('user_auth_sign', $this->data_auth_sign($auth),86400*7);
        }*/
        //setcookie('backend_companyid',$user['founder_id'],time()+2592000);
        $_SESSION['backend_companyid'] = $user['founder_id'];
        session('user_auth', $auth);
        session('user_auth_sign', $this->data_auth_sign($auth));


        return $this->is_login();
    }

    /**
     * 数据签名认证
     * @param  array  $data 被认证的数据
     * @return string       签名
     * @author jry <598821125@qq.com>
     */
    public function data_auth_sign($data) {
        // 数据类型检测
        if (!is_array($data)) {
            $data = (array)$data;
        }
        ksort($data); //排序
        $code = http_build_query($data); // url编码并生成query字符串
        $sign = sha1($code);  // 生成签名
        return $sign;
    }

    /**
     * 检测用户是否登录
     * @return integer 0-未登录，大于0-当前登录用户ID
     * @author jry <598821125@qq.com>
     */
    public function is_login() {
        $session = session('user_auth');
        $user = cookie('user_auth')?cookie('user_auth'):$session;

        if(empty($session)){
            session('user_auth', unserialize(cookie('user_auth')));
            session('user_auth_sign', cookie(cookie('user_auth_sign')));
        }

        if (empty($user)) {
            return 0;
        } else {
            if(!is_array($user))$user=unserialize($user);

            if (session('user_auth_sign') == $this->data_auth_sign($user)||cookie('user_auth_sign')== $this->data_auth_sign($user)) {
                return $user['uid'];
            } else {
                return 0;
            }
        }
    }

    public function getTreeUser($id){
        $data = $this->select();
        static $group = array();
        foreach($data as $v){
            if($v['founder_id'] == $id){
                $group[] = $v['id'];
                $this->getTreeUser($v['id']);
            }
        }
        return $group;
    }

    public function userInfo($id){
        return $this->find($id);
    }
}
