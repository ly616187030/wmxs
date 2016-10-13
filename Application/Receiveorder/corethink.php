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
        'name'        => 'Receiveorder',
        'title'       => '订单',
        'icon'        => 'fa-file-text',
        'icon_color'  => '#F9B440',
        'description' => '订单模块',
        'developer'   => '包头市助友科技有限公司',
        'website'     => 'http://www.corethink.cn',
        'version'     => '1.1.0',
        'beta'        => 'false',
        'sort'        => '5',
        'dependences' => array(
            'Admin'   => '1.1.0',
        )
    ),

    // 后台菜单及权限节点配置
    'admin_menu' => array(
        '1' => array(
            'pid'   => '0',
            'title' => '订单管理',
            'icon'  => 'fa-file-text',
        ),
        '2' => array(
            'pid'   => '1',
            'title' => '外卖订单',
            'icon'  => 'fa fa-folder-open-o',
        ),
        /*'3' => array(
            'pid'   => '1',
            'title' => '统计管理',
            'icon'  => 'fa fa-folder-open-o',
        ),*/
        '4' => array(
            'pid'   => '2',
            'title' => '外卖新订单',
            'url'   => 'ReceiveOrder/Order/neworder',
        ),
        /*'5' => array(
            'pid'   => '2',
            'title' => '配送中订单',
            'url'   => 'ReceiveOrder/Order/delivering',
        ),*/
        '6' => array(
            'pid'   => '2',
            'title' => '外卖订单记录',
            'url'   => 'ReceiveOrder/Order/refuse',
        ),
        /*'7' => array(
            'pid'   => '3',
            'title' => '订单完成情况',
            'url'   => 'ReceiveOrder/Statistics/completion',
        ),
        '8' => array(
            'pid'   => '3',
            'title' => '今日销量',
            'url'   => 'ReceiveOrder/Statistics/today',
        ),
        '9' => array(
            'pid'   => '3',
            'title' => '本周销量',
            'url'   => 'ReceiveOrder/Statistics/week',
        ),*/
        '10' => array(
            'pid'   => '6',
            'title' => '查询(权限)',
            'url'   => 'ReceiveOrder/Order/query',
            'status'=>'hide'
        ),
        '11' => array(
            'pid'   => '4',
            'title' => '订单详情',
            'url'   => 'ReceiveOrder/Order/orderMore',
        ),
        '12' => array(
            'pid'   => '4',
            'title' => '拒单',
            'url'   => 'ReceiveOrder/Order/judan',
        ),
        '13' => array(
            'pid'   => '4',
            'title' => '推送订单',
            'url'   => 'ReceiveOrder/Order/pushOrder',
        ),
        '14' => array(
            'pid'   => '6',
            'title' => '获取默认打印(权限)',
                'url'   => 'ReceiveOrder/Order/getLocalNum',
            'status' => 'hide'
        ),
        '15' => array(
            'pid'   => '6',
            'title' => '推送(权限)',
            'url'   => 'ReceiveOrder/Order/pushOrders',
            'status'=>'hide'
        ),
        '16' => array(
            'pid'   => '6',
            'title' => '完成(权限)',
            'url'   => 'ReceiveOrder/Order/complete',
            'status'=>'hide'
        ),
        '17' => array(
            'pid'   => '6',
            'title' => '拒单(权限)',
            'url'   => 'ReceiveOrder/Order/confirm1',
            'status'=>'hide'
        ),
        '18' => array(
            'pid'   => '2',
            'title' => '外卖订单退款',
            'url' => 'ReceiveOrder/Order/refund'
        ),
        '19' => array(
            'pid'   => '2',
            'title' => '修改退款订单(权限)',
            'url' => 'ReceiveOrder/Order/modifyRefundStatus',
            'status' => 'hide'
        ),
        '20' => array(
            'pid'   => '1',
            'title' => '堂食订单',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '21' => array(
            'pid'   => '20',
            'title' => '订单记录',
            'url' => 'ReceiveOrder/Order/roomrecord',
        ),
        '22' => array(
            'pid'   => '20',
            'title' => '修改菜单',
            'url' => 'ReceiveOrder/Order/changeCai',
            'status' => 'hide'
        ),
        '23' => array(
            'pid'   => '20',
            'title' => '查询菜品',
            'url' => 'ReceiveOrder/Order/selectCai',
            'status' => 'hide'
        ),
        '24' => array(
            'pid'   => '20',
            'title' => '添加菜品',
            'url' => 'ReceiveOrder/Order/addCai',
            'status' => 'hide'
        ),
        '25' => array(
            'pid'   => '1',
            'title' => '快速下单',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '26' => array(
            'pid'   => '25',
            'title' => '快速下单',
            'url'   => 'ReceiveOrder/Fastfood/index',
        ),
        '27' => array(
            'pid'   => '25',
            'title' => '查询菜品',
            'url'   => 'ReceiveOrder/Fastfood/findFood',
            'status'=> 'hide'
        ),
        '28' => array(
            'pid'   => '25',
            'title' => '快速外卖下单',
            'url'   => 'ReceiveOrder/Fastfood/setUser',
            'status'=> 'hide'
        ),
        '29' => array(
            'pid'   => '25',
            'title' => '更新菜品销量',
            'url'   => 'ReceiveOrder/Fastfood/updateGoodsSale',
            'status'=> 'hide'
        ),
        '30' => array(
            'pid'   => '25',
            'title' => '查找桌台',
            'url'   => 'ReceiveOrder/Fastfood/findSeat',
            'status'=> 'hide'
        ),
        '31' => array(
            'pid'   => '25',
            'title' => '选择桌台',
            'url'   => 'ReceiveOrder/Fastfood/selectSeat',
        ),
        '32' => array(
            'pid'   => '25',
            'title' => '电话弹窗获取用户信息(权限)',
            'url'   => 'ReceiveOrder/Fastfood/getPerson',
            'status'=> 'hide'
        ),
        '33' => array(
            'pid'   => '25',
            'title' => '快速堂食下单',
            'url'   => 'ReceiveOrder/Fastfood/setOrder',
            'status'=> 'hide'
        ),
        '34' => array(
            'pid'   => '25',
            'title' => '订单详情',
            'url'   => 'ReceiveOrder/Fastfood/orderMore',
            'status'=> 'hide'
        ),
        '35' => array(
            'pid'   => '25',
            'title' => '订单结账',
            'url'   => 'ReceiveOrder/Fastfood/checkOut',
            'status'=> 'hide'
        )

    )
);
