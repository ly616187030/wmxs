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
        'name'        => 'Fastfood',
        'title'       => '快速下单',
        'icon'        => 'fa fa-sitemap',
        'icon_color'  => '#F9B440',
        'description' => '快速下单模块',
        'developer'   => '包头市助友科技有限公司',
        'website'     => 'http://www.corethink.cn',
        'version'     => '1.1.0',
        'beta'        => 'false',
        'dependences' => array(
            'Admin'   => '1.1.0',
        )
    ),

    // 后台菜单及权限节点配置
    'admin_menu' => array(
        '1' => array(
            'pid'   => '0',
            'title' => '快速下单',
            'icon'  => 'fa-pencil-square-o',
        ),
        '2' => array(
            'pid'   => '1',
            'title' => '快速下单',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '3' => array(
            'pid'   => '2',
            'title' => '快速下单',
            'url'   => 'Fastfood/Fastfood/index',
        ),
        '4' => array(
            'pid'   => '2',
            'title' => '查询菜品',
            'url'   => 'Fastfood/Fastfood/findFood',
            'status'=> 'hide'
        ),
        '5' => array(
            'pid'   => '2',
            'title' => '快速外卖下单',
            'url'   => 'Fastfood/Fastfood/setUser',
            'status'=> 'hide'
        ),
        '6' => array(
            'pid'   => '2',
            'title' => '更新菜品销量',
            'url'   => 'Fastfood/Fastfood/updateGoodsSale',
            'status'=> 'hide'
        ),
        '7' => array(
            'pid'   => '2',
            'title' => '查找桌台',
            'url'   => 'Fastfood/Fastfood/findSeat',
            'status'=> 'hide'
        ),
        '8' => array(
            'pid'   => '2',
            'title' => '选择桌台',
            'url'   => 'Fastfood/Fastfood/selectSeat',
        ),
        '9' => array(
            'pid'   => '2',
            'title' => '电话弹窗获取用户信息(权限)',
            'url'   => 'Fastfood/Fastfood/getPerson',
            'status'=> 'hide'
        ),
        '10' => array(
            'pid'   => '2',
            'title' => '快速堂食下单',
            'url'   => 'Fastfood/Fastfood/setOrder',
            'status'=> 'hide'
        ),
        '11' => array(
            'pid'   => '2',
            'title' => '订单详情',
            'url'   => 'Fastfood/Fastfood/orderMore',
            'status'=> 'hide'
        ),
        '12' => array(
            'pid'   => '2',
            'title' => '订单结账',
            'url'   => 'Fastfood/Fastfood/checkOut',
            'status'=> 'hide'
        )
    )
);
