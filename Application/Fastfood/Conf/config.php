<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
return array(

    // 路由配置
    'URL_ROUTER_ON'     => true,
    'URL_MAP_RULES'     => array(
        'index'         =>    'Fastfood/index', // 每个模块必须配置此项
    ),
    'URL_ROUTE_RULES'   => array(
        'post/:id'    =>  'Home/article/detail',
        'notice/:id'  =>  'Home/notice/detail',
        'list/:cid'   =>  'Home/article/index',
        'cate/:id'    =>  'Home/category/detail',
    ),
    'SESSION_PREFIX'=>'CT_Admin_',
    'COOKIE_PREFIX'=>'CT_Admin_'
);
