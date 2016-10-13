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
        'name'        => 'Storemanagement',
        'title'       => '店面管理',
        'icon'        => 'glyphicon glyphicon-piggy-bank',
        'icon_color'  => '#F9B440',
        'description' => '商家模块',
        'developer'   => '包头市助友科技有限公司',
        'website'     => 'http://www.corethink.cn',
        'version'     => '1.1.0',
        'beta'        => 'false',
        'sort'        => '4',
        'dependences' => array(
            'Admin'   => '1.1.0',
        )
    ),

    // 后台菜单及权限节点配置
    'admin_menu' => array(
        '1' => array(
            'pid'   => '0',
            'title' => '店面管理',
            'icon'  => 'fa fa-archive',
        ),
        '2' => array(
            'pid'   => '1',
            'title' => '菜品管理',
            'icon'   => 'fa fa-folder-open-o',
        ),
        '3' => array(
            'pid'   => '2',
            'title' => '菜品菜单',
            'url'   => 'Storemanagement/CpStore/index',
        ),
        '4' => array(
            'pid'   => '2',
            'title' => '菜品列表',
            'url'   => 'Storemanagement/Canming/index',
        ),
        '5' => array(
            'pid'   => '2',
            'title' => '添加菜品',
            'url'   => 'Storemanagement/Canming/add',
        ),
        '23' => array(
            'pid'   => '2',
            'title' => '删除分类(p)',
            'url'   => 'Storemanagement/CanPin/del',
            'status'=> 'hide'
        ),
        '24' => array(
            'pid'   => '2',
            'title' => '编辑分类(p)',
            'url'   => 'Storemanagement/CanPin/edit',
            'status'=> 'hide'
        ),
        '25' => array(
            'pid'   => '2',
            'title' => '添加分类(p)',
            'url'   => 'Storemanagement/CanPin/add',
            'status'=> 'hide'
        ),
        '26' => array(
            'pid'   => '2',
            'title' => '编辑菜品(p)',
            'url'   => 'Storemanagement/canming/edit',
            'status'=> 'hide'
        ),
        '27' => array(
            'pid'   => '2',
            'title' => '删除菜品(p)',
            'url'   => 'Storemanagement/canming/del',
            'status'=> 'hide'
        ),
        '28' => array(
            'pid'   => '2',
            'title' => '添加菜品到该分类',
            'url'   => 'Storemanagement/CpStore/categoryList',
            'status'=> 'hide'
        ),
        '29' => array(
            'pid'   => '2',
            'title' => '删除菜品B(p)',
            'url'   => 'Storemanagement/CpStore/delCaipin',
            'status' => 'hide'
        ),
        '30' => array(
            'pid'   => '2',
            'title' => '设置状态(p)',
            'url'   => 'Storemanagement/CpStore/setStatus',
            'status' => 'hide'
        ),
        '31' => array(
            'pid'   => '2',
            'title' => '更改状态(p)',
            'url'   => 'Storemanagement/CpStore/changeCaipin',
            'status' => 'hide'
        ),


        '6' => array(
            'pid'   => '1',
            'title' => '桌台分类',
            'icon'   => 'fa fa-folder-open-o',
        ),
        '7' => array(
            'pid'   => '6',
            'title' => '桌台分类管理',
            'url'   => 'Storemanagement/Seatcategory/index',
        ),
        '8' => array(
            'pid'   => '6',
            'title' => '新增桌台分类',
            'url'   => 'Storemanagement/Seatcategory/add',
        ),
        '9' => array(
            'pid'   => '1',
            'title' => '桌台设置',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '10' => array(
            'pid'   => '9',
            'title' => '桌台管理',
            'url'   => 'Storemanagement/Seat/index',
        ),
        '11' => array(
            'pid'   => '9',
            'title' => '新增桌台',
            'url'   => 'Storemanagement/Seat/add',
        ),
        '12' => array(
            'pid'   => '1',
            'title' => '打印机设置',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '13' => array(
            'pid'   => '12',
            'title' => '打印机',
            'url'   => 'Storemanagement/Index/index',
        ),
        '14' => array(
            'pid'   => '1',
            'title' => '档口管理',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '15' => array(
            'pid'   => '14',
            'title' => '档口管理',
            'url'   => 'Storemanagement/Dangkou/index',
        ),
        '16' => array(
            'pid'   => '14',
            'title' => '新增档口',
            'url'   => 'Storemanagement/Dangkou/add',
        ),
        '17' => array(
            'pid'   => '14',
            'title' => '删除档口（权限）',
            'url'   => 'Storemanagement/Dangkou/del',
            'status' => 'hide'
        ),
        '18' => array(
            'pid'   => '12',
            'title' => '获取打印机(权限)',
            'url'  => 'Storemanagement/index/createPrint',
            'status' => 'hide'
        ),
        '19' => array(
            'pid'   => '12',
            'title' => '设置打印机(权限)',
            'url'  => 'Storemanagement/index/printerSet',
            'status' => 'hide'
        ),
        '20' => array(
            'pid'   => '12',
            'title' => '设置默认打印机(权限)',
            'url'  => 'Storemanagement/Index/setPrintDef',
            'status' => 'hide'
        ),
        '21' => array(
            'pid'   => '6',
            'title' => '删除桌台',
            'url'   => 'Storemanagement/Seatcategory/del',
            'status'=>'hide',
        ),
        '22' => array(
            'pid'   => '9',
            'title' => '删除桌台',
            'url'   => 'Storemanagement/Seat/del',
            'status'=>'hide',
        ),
    )
);
