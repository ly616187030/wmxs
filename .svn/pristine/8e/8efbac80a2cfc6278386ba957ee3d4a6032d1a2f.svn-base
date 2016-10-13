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
        'name'        => 'Settle',
        'title'       => '结算统计',
        'icon'        => 'fa fa-sitemap',
        'icon_color'  => '#F9B440',
        'description' => '结算模块',
        'developer'   => '包头市助友科技有限公司',
        'website'     => 'http://www.corethink.cn',
        'version'     => '1.1.0',
        'beta'        => 'false',
        'sort'        => '7',
        'dependences' => array(
            'Admin'   => '1.1.0',
        )
    ),

    // 后台菜单及权限节点配置
    'admin_menu' => array(
        '1' => array(
            'pid'   => '0',
            'title' => '结算统计',
            'icon'  => 'fa-usd',
        ),
        '2' => array(
            'pid'   => '1',
            'title' => '结算对账',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '3' => array(
            'pid'   => '2',
            'title' => '对账详单',
            'url'   => 'Settle/Settle/index',
        ),
        '4' => array(
            'pid'   => '2',
            'title' => '商家结算报表',
            'url'   => 'Settle/Settle/shopsettlement',
        ),
        '5' => array(
            'pid'   => '2',
            'title' => '骑手结算报表',
            'url'   => 'Settle/Settle/deliversettlement',
        ),
        '6' => array(
            'pid'   => '2',
            'title' => '站点对账导出EXCEL（权限）',
            'url'   => 'Settle/Settle/excel',
            'status'=>'hide'
        ),
        '7' => array(
            'pid'   => '2',
            'title' => '商家结算报表导出EXCEL（权限）',
            'url'   => 'Settle/Settle/shopexcel',
            'status'=>'hide'
        ),
        '8' => array(
            'pid'   => '2',
            'title' => '配送员结算报表导出EXCEL（权限）',
            'url'   => 'Settle/Settle/deliverexcel',
            'status'=>'hide'
        ),
        '9' => array(
            'pid'   => '3',
            'title' => '站点对账订单明细（权限）',
            'url'   => 'Settle/Settle/orderdetail',
            'status'=>'hide'
        ),
        '10' => array(
            'pid'   => '1',
            'title' => '数据统计',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '11' => array(
            'pid'   => '10',
            'title' => '日常运营数据统计',
            'url'   => 'Datastatistics/Datastatistics/dailyData',
        ),
        '12' => array(
            'pid'   => '10',
            'title' => '运营指标监控',
            'url'   => 'Datastatistics/Datastatistics/monitor',
        ),
        '13' => array(
            'pid'   => '10',
            'title' => '业务情况数据',
            'url'   => 'Datastatistics/Datastatistics/business',
        ),
        '14' => array(
            'pid'   => '10',
            'title' => '骑手效率统计',
            'url'   => 'Datastatistics/Datastatistics/efficiency',
        ),
        '15' => array(
            'pid'   => '2',
            'title' => '菜品销量统计',
            'url'   => 'Settle/Settle/salesVolume',
        ),
        '16' => array(
            'pid'   => '15',
            'title' => '菜品销量统计获取产品分类（权限）',
            'url'   => 'Settle/Settle/getFenlei',
            'status'=>'hide',
        ),
    )
);
