<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Mobile\Controller;
use User\Controller\Home\MessageController;
use Common\Util\Think\Page;
/**
 * 消息控制器
 * @author jry <598821125@qq.com>
 */
class MessagesController extends MessageController{
    /**
     * 用户个人主页
     * @author jry <598821125@qq.com>
     */
    public function home($uid) {
        $user_id = is_login();
        $user_info = D('User/User')->detail($uid);
        $user_type_info = D('User/type')->find($user_info['user_type']);
        if ($user_info['status'] !== '1') {
            $this->error('该用户不存在或已禁用');
        }
        if ($user_type_info['home_template']) {
            $template = $user_type_info['home_template'];
        } else {
            $template = 'home';
        }
        $this->assign('meta_title', $user_info['username'].'的主页');
        $this->assign('user_info', $user_info);
        $this->display($template);
    }
}
