<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
// 模块信息配置
return array(
    // 模块信息
    'info' => array(
        'name'        => 'User',
        'title'       => '用户管理',
        'icon'        => 'fa fa-users',
        'icon_color'  => '#F9B440',
        'description' => '用户中心模块',
        'developer'   => '南京科斯克网络科技有限公司',
        'website'     => 'http://www.corethink.cn',
        'version'     => '1.1.0',
        'beta'        => 'false',
        'sort'        => '2',
        'dependences' => array(
            'Admin'   => '1.1.0',
        )
    ),

    // 用户中心导航
    'user_nav' => array(
        'center' => array(
            '0' => array(
                'title' => '修改信息',
                'icon'  => 'fa fa-edit',
                'url'   => 'Mobile/Usercenter/profile',
            ),
            '1' => array(
                'title' => '修改密码',
                'icon'  => 'fa fa-lock',
                'url'   => 'Mobile/Usercenter/password',
            ),
            '2' => array(
                'title' => '消息中心',
                'icon'  => 'fa fa-envelope-o',
                'url'   => 'Mobile/Messages/index',
            ),
        ),
        'main' => array(
            '0' => array(
                'title' => '我的地址',
                'icon'  => 'fa fa-map-marker',
                'url'   => 'Mobile/Myaddress/index?src=1',
            ),
           /* '1' => array(
                'title' => '我的代金券',
                'icon'  => 'fa fa-money',
                'url'   => 'Mobile/Voucher/index?src=1',
            ),*/
            '2' => array(
                'title' => '我的收藏',
                'icon'  => 'fa fa-bookmark',
                'url'   => 'Mobile/Favorite/index',
            ),
            '3' => array(
                'title' => '我的预定',
                'icon'  => 'fa fa-gift',
                'url'   => 'Mobile/Yuding/index',
            ),
            '4' => array(
                'title' => '我的砍价',
                'icon'  => 'fa fa-hand-o-right',
                'url'   => 'Mobile/kanjias/index',
            ),
            '5' => array(
                'title' => '分享红包',
                'icon'  => 'fa fa-share',
                'url'   => 'Mobile/Hongbao/index',
            ),
        ),
    ),

    // 模块配置
    'config' => array(
        'status' => array(
            'title'   => '是否开启',
            'type'    => 'radio',
            'options' => array(
                '1' => '开启',
                '0' => '关闭',
            ),
            'value' => '1',
        ),
        'reg_toggle' => array(
            'title'   => '注册开关',
            'type'    => 'radio',
            'options' => array(
                '1'   => '开启',
                '0'   => '关闭',
            ),
            'value'   => '1',
        ),
        'allow_reg_type' => array(
            'title'   => '允许注册类型',
            'type'    =>'checkbox',
            'options' => array(
                'username' => '用户名注册',
                'email'    => '邮箱注册',
                'mobile'   => '手机注册',
            ),
            'value'=> array(
                '0' => 'username',
            ),
        ),
        'deny_username' => array(
            'title'   => '禁止注册的用户名',
            'type'    =>'textarea',
            'value'   => '',
        ),
        'user_protocol' => array(
            'title'   => '用户协议',
            'type'    =>'kindeditor',
            'value'=>'',
        ),
        'behavior' => array(
            'title'   => '行为扩展',
            'type'   =>'checkbox',
            'options'=> array(
                'User' => 'User',
            ),
            'value'  => array(
                '0'  => 'User',
            ),
        ),
    ),

    // 后台菜单及权限节点配置
    'admin_menu' => array(
        '1' => array(
            'pid'   => '0',
            'title' => '用户管理',
            'icon'  => 'fa fa-user',
        ),
        '2' => array(
            'pid'   => '1',
            'title' => '用户管理',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '3' => array(
            'pid'   => '2',
            'title' => '用户设置',
            'icon'  => 'fa fa-wrench',
            'url'   => 'User/Admin/Default/module_config',
        ),
        '4' => array(
            'pid'   => '2',
            'title' => '用户统计',
            'icon'  => 'fa fa-area-chart',
            'url'   => 'User/Admin/Default/index',
        ),
        '5' => array(
            'pid'   => '2',
            'title' => '用户列表',
            'icon'  => 'fa fa-list',
            'url'   => 'User/Admin/User/index',
        ),
        '6' => array(
            'pid'   => '5',
            'title' => '新增',
            'url'   => 'User/Admin/User/add',
        ),
        '7' => array(
            'pid'   => '5',
            'title' => '编辑',
            'url'   => 'User/Admin/User/edit',
        ),
        '8' => array(
            'pid'   => '5',
            'title' => '设置状态',
            'url'   => 'User/Admin/User/setStatus',
        ),
        '9' => array(
            'pid'   => '1',
            'title' => '商家分配',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '10' => array(
            'pid'   => '9',
            'title' => '商家分配',
            'url'   => 'User/Admin/ClerkOrdering/index',
        ),
        '11' => array(
            'pid'   => '9',
            'title' => '添加',
            'url'   => 'User/Admin/ClerkOrdering/add',
            'status'=>  'hide'
        ),
        '12' => array(
            'pid'   => '2',
            'title' => '用户类型',
            'icon'  => 'fa fa-user',
            'url'   => 'User/Admin/Type/index',
        ),
        '13' => array(
            'pid'   => '2',
            'title' => '角色权限设置',
            'icon'  => 'fa fa-user',
            'url'   => 'User/Admin/Group/index',
        ),
        '14' => array(
            'pid'   => '2',
            'title' => '添加角色',
            'url'   => 'User/Admin/Group/add',
            'status'=> 'hide'
        ),
        '15' => array(
            'pid'   => '2',
            'title' => '编辑角色',
            'status'=> 'hide',
            'url'   => 'User/Admin/Group/edit',
        ),
        '16' => array(
            'pid'   => '2',
            'title' => '删除角色',
            'status'=> 'hide',
            'url'   => 'User/Admin/Group/del',
        ),
        '17' => array(
            'pid'   => '1',
            'title' => '部门',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '18' => array(
            'pid'   => '17',
            'title' => '部门列表',
            'url'   => 'User/Admin/Department/index',
        ),
    )
);
