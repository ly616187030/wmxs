<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------

/**
 * PHP版本检查
 */
if (version_compare(PHP_VERSION,'5.3.0','<')) {
    die('require PHP > 5.3.0 !');
}

/**
 * PHP报错设置
 */
//error_reporting(E_ALL^E_NOTICE^E_WARNING);
error_reporting(E_ALL);
/**
 * 开发模式环境变量前缀
 */
define('ENV_PRE', 'CT_');

/**
 * 应用目录设置
 * 安全期间，建议安装调试完成后移动到非WEB目录
 */
define('APP_PATH', './Application/');

/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define('RUNTIME_PATH', './Runtime/');

/**
 * 静态缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define('HTML_PATH', RUNTIME_PATH.'Html/');

/**
 * 包含开发模式数据库连接配置
 */
if (@$_SERVER[ENV_PRE.'DEV_MODE'] !== 'true') {
    @include './Data/dev.php'; 
}

/**
 * 系统安装及开发模式检测
 */
if (is_file('./Data/install.lock') === false && @$_SERVER[ENV_PRE.'DEV_MODE'] !== 'true') {
    define('BIND_MODULE','Install');
} else {
    // 系统调试设置, 项目正式部署后请设置为false
    define('APP_DEBUG', @$_SERVER[ENV_PRE.'APP_DEBUG'] ? : true);
}
if(is_waps() && !is_redirect()){
    define('BIND_MODULE','Mobile');
}
/**
 * 引入核心入口
 * ThinkPHP亦可移动到WEB以外的目录
 */
require './ThinkPHP/ThinkPHP.php';

// 判断是否是在微信浏览器里
function isWeixinBrowser($from = 0) {
    if ((! $from && defined ( 'IN_WEIXIN' ) && IN_WEIXIN) || isset ( $_GET ['is_stree'] ))
        return true;

    $agent = $_SERVER ['HTTP_USER_AGENT'];
    if (! strpos ( $agent, "icroMessenger" )) {
        return false;
    }
    return true;
}
function is_redirect(){
    $str = $_GET['s'];
    if(strpos($str,'register') || strpos($str,'login')){
        return true;
    }else{
        return false;
    }
}
/**
 * 是否手机访问
 * @return bool
 * @author jry <598821125@qq.com>
 */
function is_waps() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array ('nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}
