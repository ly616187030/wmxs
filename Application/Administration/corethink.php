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
        'name'        => 'Administration',
        'title'       => '管理',
        'icon'        => 'fa fa-sitemap',
        'icon_color'  => '#F9B440',
        'description' => '管理模块',
        'developer'   => '包头市助友科技有限公司',
        'website'     => 'http://www.corethink.cn',
        'version'     => '1.1.0',
        'beta'        => 'false',
        'dependences' => array(
            'Admin'   => '1.1.0',
        )
    ),

    'admin_menu' => array(
        '1' => array(
            'pid'   => '0',
            'title' => '管理',
            'icon'  => 'fa fa-th-list',
        ),
        '2' => array(
            'pid'   => '1',
            'title' => '商圈管理',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '3' => array(
            'pid'   => '2',
            'title' => '商圈管理',
            'url'   => 'Administration/Shangquan/index',
        ),
        '4' => array(
            'pid'   => '2',
            'title' => '新增商圈',
            'url'   => 'Administration/Shangquan/add',
        ),
       /* '5' => array(
            'pid'   => '1',
            'title' => '站点管理',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '6' => array(
            'pid'   => '5',
            'title' => '站点管理',
            'url'   => 'Administration/Range/index',
        ),
        '7' => array(
            'pid'   => '5',
            'title' => '新增站点',
            'url'   => 'Administration/Range/add',
        ),*/
        /*'8' => array(
            'pid'   => '1',
            'title' => '地址范围',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '9' => array(
            'pid'   => '8',
            'title' => '地址范围管理',
            'url'   => 'Administration/RestaurantScope/index',
        ),
        '10' => array(
            'pid'   => '8',
            'title' => '新增地址范围',
            'url'   => 'Administration/RestaurantScope/add',
        ),*/
        '11' => array(
            'pid'   => '1',
            'title' => '餐厅分类',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '12' => array(
            'pid'   => '11',
            'title' => '餐厅分类管理',
            'url'   => 'Administration/StoreCategory/index',
        ),
        '13' => array(
            'pid'   => '11',
            'title' => '新增餐厅分类',
            'url'   => 'Administration/StoreCategory/add',
        ),
        /*'14' => array(
            'pid'   => '1',
            'title' => '开通城市管理',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '15' => array(
            'pid'   => '14',
            'title' => '城市管理',
            'url'   => 'Administration/City/index',
        ),
        '16' => array(
            'pid'   => '14',
            'title' => '增加城市',
            'url'   => 'Administration/City/add',
        ),

        '17' => array(
            'pid'   => '1',
            'title' => '配送范围管理',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '18' => array(
            'pid'   => '17',
            'title' => '配送范围',
            'url'   => 'Administration/DistributionRange/index',
        ),
        '19' => array(
            'pid'   => '1',
            'title' => '红包管理',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '20' => array(
            'pid'   => '19',
            'title' => '红包管理',
            'url'   => 'Administration/Bao/index',
        ),
        '21' => array(
            'pid'   => '19',
            'title' => '用户红包',
            'url'   => 'Administration/Bao/hongbao',
        ),
        '22' => array(
            'pid'   => '1',
            'title' => '用户分享',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '23' => array(
            'pid'   => '22',
            'title' => '分享设置',
            'url'   => 'Administration/Share/index',
        ),
        '24' => array(
            'pid'   => '22',
            'title' => '邀请详情',
            'url'   => 'Administration/Share/detail',
        ),
        '25' => array(
            'pid'   => '1',
            'title' => '菜品销量',
            'icon'  => 'fa fa-folder-open-o',
        ),
        '26' => array(
            'pid'   => '25',
            'title' => '菜品销量',
            'url'   => 'Administration/SalesManagemen/index',
        ),*/

    ),
);

