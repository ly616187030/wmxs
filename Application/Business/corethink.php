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
        'name'        => 'Business',
        'title'       => '商户管理',
        'icon'        => 'glyphicon glyphicon-piggy-bank',
        'icon_color'  => '#F9B440',
        'description' => '商户模块',
        'developer'   => '包头市助友科技有限公司',
        'website'     => 'http://www.corethink.cn',
        'version'     => '1.1.0',
        'beta'        => 'false',
        'sort'        => '3',
        'dependences' => array(
            'Admin'   => '1.1.0',
        )
    ),

    // 后台菜单及P节点配置
    'admin_menu' => array(
        '1' => array(
            'pid'   => '0',
            'title' => '商户管理',
            'icon'  => 'fa fa-users',
        ),
        '2' => array(
            'pid'   => '1',
            'title' => '商户管理',
            'icon'   => 'fa fa-folder-open-o',

        ),
        '4' => array(
            'pid'   => '2',
            'title' => '菜品菜单',
            'icon'  => 'fa fa-folder-open-o',
            'status'=> 'hide'
        ),
        '6' => array(
            'pid'   => '1',
            'title' => '优惠活动',
            'icon'  => 'fa fa-folder-open-o',
        ),

        '7' => array(
            'pid'   => '4',
            'title' => '删除分类(p)',
            'url'   => 'Business/CanPin/del',
            'status'=> 'hide'
        ),
        '8' => array(
            'pid'   => '4',
            'title' => '编辑分类(p)',
            'url'   => 'Business/CanPin/edit',
            'status'=> 'hide'
        ),
        '9' => array(
            'pid'   => '4',
            'title' => '添加分类(p)',
            'url'   => 'Business/CanPin/add',
            'status'=> 'hide'
        ),
        '10' => array(
            'pid'   => '4',
            'title' => '管理菜品A(p)',
            'url'   => 'Business/canming/index',
            'status'=> 'hide'
        ),
        '11' => array(
            'pid'   => '4',
            'title' => '编辑菜品A(p)',
            'url'   => 'Business/canming/edit',
            'status'=> 'hide'
        ),
        '12' => array(
            'pid'   => '4',
            'title' => '删除菜品A(p)',
            'url'   => 'Business/canming/del',
            'status'=> 'hide'
        ),
        '13' => array(
            'pid'   => '4',
            'title' => '添加菜品A(p)',
            'url'   => 'Business/canming/add',
            'status'=> 'hide'
        ),
        '15' => array(
            'pid'   => '6',
            'title' => '促销活动',
            'url'   => 'Business/Promotion/index',
        ),
        '16' => array(
            'pid'   => '6',
            'title' => '新增促销',
            'url'   => 'Business/Promotion/add',
        ),

        '20' => array(
            'pid'   => '4',
            'title' => '自动生成拼音(p)',
            'url'   => 'Business/Store/cu2py',
            'status' => 'hide'
        ),


        '36' => array(
            'pid'   => '15',
            'title' => '编辑促销(p)',
            'url'   => 'Business/Promotion/edit',
        ),
        '37' => array(
            'pid'   => '15',
            'title' => '删除促销(p)',
            'url'   => 'Business/Promotion/del',
        ),
        '38' => array(
            'pid'   => '15',
            'title' => '启用禁用(p)',
            'url'   => 'Business/Promotion/enable',
        ),
        '39' => array(
            'pid'   => '15',
            'title' => '复制商家活动(p)',
            'url'   => 'Business/Promotion/copy_store',
        ),

        '42' => array(
            'pid'   => '4',
            'title' => '管理菜单',
            'url'   => 'Business/CpStore/index',
            'status' => 'hide'
        ),
        '43' => array(
            'pid'   => '4',
            'title' => '获取菜品分类(p)',
            'url'   => 'Business/CpStore/categoryList',
            'status' => 'hide'
        ),
        '44' => array(
            'pid'   => '4',
            'title' => '删除菜品B(p)',
            'url'   => 'Business/CpStore/delCaipin',
            'status' => 'hide'
        ),
        '45' => array(
            'pid'   => '4',
            'title' => '设置状态(p)',
            'url'   => 'Business/CpStore/setStatus',
            'status' => 'hide'
        ),
        '46' => array(
            'pid'   => '4',
            'title' => '更改状态(p)',
            'url'   => 'Business/CpStore/changeCaipin',
            'status' => 'hide'
        ),
        '48' => array(
            'pid'   => '2',
            'title' => '商户列表',
            'url'   => 'Business/Shanghu/index',
        ),
        '49' => array(
            'pid'   => '48',
            'title' => '添加商户(p)',
            'url'   => 'Business/Shanghu/add',
            'status'=>'hide'
        ),
        '50' => array(
            'pid'   => '48',
            'title' => '编辑商户(p)',
            'url'   => 'Business/Shanghu/edit',
            'status'=>'hide'
        ),
        '51' => array(
            'pid'   => '48',
            'title' => '编辑商户省(p)',
            'url'   => 'Business/MenuPct/getProvince',
            'status'=>'hide'
        ),
        '52' => array(
            'pid'   => '48',
            'title' => '编辑商户市(p)',
            'url'   => 'Business/MenuPct/getCity',
            'status'=>'hide'
        ),
        '53' => array(
            'pid'   => '48',
            'title' => '编辑商户镇(p)',
            'url'   => 'Business/MenuPct/getTown',
            'status'=>'hide'
        ),
        '54' => array(
            'pid'   => '1',
            'title' => '商圈管理',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '55' => array(
            'pid'   => '54',
            'title' => '商圈管理',
            'url'   => 'Business/Shangquan/index',
        ),
        '56' => array(
            'pid'   => '54',
            'title' => '新增商圈',
            'url'   => 'Business/Shangquan/add',
        ),
        '57' => array(
            'pid'   => '1',
            'title' => '餐厅分类',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '58' => array(
            'pid'   => '57',
            'title' => '餐厅分类管理',
            'url'   => 'Business/StoreCategory/index',
        ),
        '59' => array(
            'pid'   => '57',
            'title' => '新增餐厅分类',
            'url'   => 'Business/StoreCategory/add',
        ),
        '60' => array(
            'pid'   => '1',
            'title' => '菜品库',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '61' => array(
            'pid'   => '60',
            'title' => '菜品管理',
            'url'   => 'Business/Caipin/index',
        ),
        '62' => array(
            'pid'   => '61',
            'title' => '新增菜品',
            'url'   => 'Business/Caipin/add',
        ),
        '63' => array(
            'pid'   => '61',
            'title' => '删除菜品',
            'url'   => 'Business/Caipin/del',
            'status'=>'hide',
        ),
        '64' => array(
            'pid'   => '48',
            'title' => '创建用户数量限制(p)',
            'url'   => 'Business/Shanghu/num',
            'status'=>'hide',
        ),
        '65' => array(
            'pid'   => '61',
            'title' => '菜品汉字转拼音',
            'url'   => 'Business/Caipin/cu2py',
            'status'=>'hide',
        ),
        '66' => array(
            'pid'   => '61',
            'title' => '菜品获得大图',
            'url'   => 'Business/Caipin/getPicture',
            'status'=>'hide',
        ),
        '67' => array(
            'pid'   => '2',
            'title' => '公司信息',
            'url'   => 'Business/Company/addedit',
            'status'=>'hide',

        ),
    )
);
