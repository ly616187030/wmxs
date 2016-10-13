<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------

require_once(APP_PATH . '/Common/Common/addon.php'); //加载插件相关公共函数库
require_once(APP_PATH . '/Common/Common/developer.php'); //加载开发者二次开发公共函数库

/**
 * 根据配置类型解析配置
 * @param  string $type  配置类型
 * @param  string  $value 配置值
 */
function parse_attr($value, $type = null) {
    switch ($type) {
        default: //解析"1:1\r\n2:3"格式字符串为数组
            $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
            if (strpos($value,':')) {
                $value  = array();
                foreach ($array as $val) {
                    list($k, $v) = explode(':', $val);
                    $value[$k]   = $v;
                }
            } else {
                $value = $array;
            }
            break;
    }
    return $value;
}

/**
 * POST数据提前处理
 * @return array
 * @author jry <598821125@qq.com>
 */
function format_data($data = null) {
    //解析数据类似复选框类型的数组型值
    if (!$data) {
        $data = $_POST;
    }
    foreach($data as $key => $val){
        if (is_array($val)) {
            $data[$key] = implode(',', $val);
        } else if (check_date_time($val)) {
            $data[$key] = strtotime($val);
        } else if (check_date_time($val, 'Y-m-d H:i')) {
            $data[$key] = strtotime($val);
        } else if (check_date_time($val, 'Y-m-d')) {
            $data[$key] = strtotime($val);
        }
    }
    return $data;
}

/**
 * 获取所有数据并转换成一维数组
 * @author jry <598821125@qq.com>
 */
function select_list_as_tree($model, $map = null, $extra = null, $key = 'id') {
    //获取列表
    $con['status'] = array('eq', 1);
    if ($map) {
        $con = array_merge($con, $map);
    }
    $model_object = D($model);
    if (in_array('sort', $model_object->getDbFields())) {
        $list = $model_object->where($con)->order('sort asc, id asc')->select();
    } else {
        $list = $model_object->where($con)->order('id asc')->select();
    }
    //转换成树状列表(非严格模式)
    $tree = new \Common\Util\Tree();
    $list = $tree->toFormatTree($list, 'title', 'id', 'pid', 0, false);

    if ($extra) {
        $result[0] = $extra;
    }

    //转换成一维数组
    foreach ($list as $val) {
        $result[$val[$key]] = $val['title_show'];
    }
    return $result;
}

/**
 * 解析文档内容
 * @param string $str 待解析内容
 * @return string
 * @author jry <598821125@qq.com>
 */
function parse_content($str) {
    // 将img标签的src改为lazy-src用户前台图片lazyload加载
    if (C('STATIC_DOMAIN')) {
        return preg_replace('/(<img.*?)src="/i', '$1 class="lazy img-responsive" style="display:inline-block;" data-lazy="'.C('STATIC_DOMAIN'), $str);
    } else {
        return preg_replace('/(<img.*?)src=/i', "$1 class='lazy img-responsive' style='display:inline-block;' data-lazy=", $str);
    }
}

/**
 * 字符串截取(中文按2个字符数计算)，支持中文和其他编码
 * @static
 * @access public
 * @param str $str 需要转换的字符串
 * @param str $start 开始位置
 * @param str $length 截取长度
 * @param str $charset 编码格式
 * @param str $suffix 截断显示字符
 * @return str
 */
function cut_str($str, $start, $length, $charset='utf-8', $suffix = true) {
    return \Common\Util\Think\String::cutStr(
        $str, $start, $length, $charset, $suffix
    );
}

/**
 * 过滤标签，输出纯文本
 * @param string $str 文本内容
 * @return string 处理后内容
 * @author jry <598821125@qq.com>
 */
function html2text($str) {
   return \Common\Util\Think\String::html2text($str);
}

/**
 * 友好的时间显示
 * @param int    $sTime 待显示的时间
 * @param string $type  类型. normal | mohu | full | ymd | other
 * @param string $alt   已失效
 * @return string
 * @author jry <598821125@qq.com>
 */
function friendly_date($sTime, $type = 'normal', $alt = 'false') {
    $date = new \Common\Util\Think\Date((int)$sTime);
    return $date->friendlyDate($type, $alt);
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author jry <598821125@qq.com>
 */
function time_format($time = NULL, $format='Y-m-d H:i') {
    $time = $time === NULL ? NOW_TIME : intval($time);
    return date($format, $time);
}

/**
 * 判断是否日期时间
 * @return string
 */
function check_date_time($str_time, $format="Y-m-d H:i:s") {
    $unix_time = strtotime($str_time);
    $check_date= date($format, $unix_time);
    if ($check_date == $str_time) {
        return true;
    } else {
        return false;
    }
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 * @author jry <598821125@qq.com>
 */
function user_md5($str) {
    /*if (!$auth_key) {
        $auth_key = C('AUTH_KEY') ? : 'CoreThink';
    }*/
    $auth_key = C('AUTH_KEY') ?C('AUTH_KEY') : 'CoreThink';
    return '' === $str ? '' : md5(sha1($str) . $auth_key);
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author jry <598821125@qq.com>
 */
function is_login() {
    return D('Admin/User')->is_login();
}

function is_member_login(){
    return D('User/User')->is_member_login();
}
/**
 * 根据用户ID获取用户信息
 * @param  integer $id 用户ID
 * @param  string $field
 * @return array  用户信息
 * @author jry <598821125@qq.com>
 */
function get_user_info($id, $field) {
    $userinfo = D('Admin/User')->find($id);
    $userinfo['avatar_url'] = get_cover($userinfo['avatar'], 'avatar');
    if ($userinfo[$field]) {
        return $userinfo[$field];
    }
    return $userinfo;
}

/**
 * 获取上传文件路径
 * @param  int $id 文件ID
 * @return string
 * @author jry <598821125@qq.com>
 */


function get_cover($cover_id, $field = null){

    if(empty($cover_id)){

        return false;

    }

    $picture = M('Picture')->where(array('status'=>1))->getById($cover_id);

    if($field == 'path'){

        if(!empty($picture['url'])){

            $picture['path'] = $picture['url'];

        }else{

            $picture['path'] = __ROOT__.$picture['path'];

        }

    }

    return empty($field) ? $picture : $picture[$field];

}
/**

 * 通过id 获得代理商名称

 * @param $id

 */

function get_agent_name($id){

    return M("admin_user")->getFieldById($id,'nickname');

}
/**

 * 通过id 获得企业名称

 * @param $id

 */

function get_founder_name($id){

    return M("company")->getFieldById($id,'c_name');

}













//function get_cover($id, $type) {
//    if ((int)$id) {
//        $upload_info = D('Admin/Upload')->find($id);
//        $url = $upload_info['real_path'];
//    }
//    if (!$url) {
//        switch ($type) {
//            case 'default' : //默认图片
//                $url = C('TMPL_PARSE_STRING.__HOME_IMG__').'/logo/default.gif';
//                break;
//            case 'avatar' : //用户头像
//                $url = C('TMPL_PARSE_STRING.__HOME_IMG__').'/avatar/avatar.gif';
//                break;
//            default: //文档列表默认图片
//                break;
//        }
//    }
//    return $url;
//}

/**
 * 获取上传文件信息
 * @param  int $id 文件ID
 * @return string
 * @author jry <598821125@qq.com>
 */
function get_upload_info($id, $field) {
    $upload_info = D('Admin/Upload')->where('status = 1')->find($id);
    if ($field) {
        if (!$upload_info[$field]) {
            return $upload_info['id'];
        } else {
            return $upload_info[$field];
        }
    }
    return $upload_info;
}

/**
 * 是否微信访问
 * @return bool
 * @author jry <598821125@qq.com>
 */
function is_weixin() {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
        return true;
    } else {
        return false;
    }
}

/**
 * 是否手机访问
 * @return bool
 * @author jry <598821125@qq.com>
 */
function is_wap() {
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


/**

 * 根据用户ID获取用户昵称

 * @param  integer $uid 用户ID

 * @return string       用户昵称

 */

function get_nickname($uid = 0){

    static $list;

    if(!($uid && is_numeric($uid))){ //获取当前登录用户名

        return session('user_auth.username');

    }



    /* 获取缓存数据 */

    if(empty($list)){

        $list = S('sys_user_nickname_list');

    }



    /* 查找用户信息 */

    $key = "u{$uid}";

    if(isset($list[$key])){ //已缓存，直接使用

        $name = $list[$key];

    } else { //调用接口获取用户信息

        $info = M('admin_user')->field('username')->find($uid);

        if($info !== false && $info['username'] ){

            $nickname = $info['username'];

            $name = $list[$key] = $nickname;

            /* 缓存用户 */

            $count = count($list);

            $max   = C('USER_MAX_CACHE');

            while ($count-- > $max) {

                array_shift($list);

            }

            S('sys_user_nickname_list', $list);

        } else {

            $name = '';

        }

    }

    return $name;

}
/**

 * 根据配送员ID获取配送员昵称

 * @param  integer $uid 用户ID

 * @return string       用户昵称

 */

function get_deliveryname($uid = 0){

    static $list;

    if(!($uid && is_numeric($uid))){ //获取当前登录用户名

        return session('user_auth.person_name');

    }



    /* 获取缓存数据 */

    if(empty($list)){

        $list = S('sys_user_nickname_list');

    }



    /* 查找用户信息 */

    $key = "u{$uid}";

    if(isset($list[$key])){ //已缓存，直接使用

        $name = $list[$key];

    } else { //调用接口获取用户信息

        $info = M('delivery_person')->field('person_name')->find($uid);

        if($info !== false && $info['person_name'] ){

            $nickname = $info['person_name'];

            $name = $list[$key] = $nickname;

            /* 缓存用户 */

            $count = count($list);

            $max   = C('USER_MAX_CACHE');

            while ($count-- > $max) {

                array_shift($list);

            }

            S('sys_user_nickname_list', $list);

        } else {

            $name = '';

        }

    }

    return $name;

}


/**
 * 记录行为日志，并执行该行为的规则
 * @param string $action 行为标识
 * @param string $model 触发行为的模型名
 * @param int $record_id 触发行为的记录id
 * @param int $user_id 执行行为的用户id
 * @return boolean
 * @author huajie <banhuajie@163.com>
 */
//function action_log($action = null, $model = null, $record_id = null, $user_id = null){
//    //参数检查
//    if(empty($action) || empty($model) || empty($record_id)){
//        return '参数不能为空';
//    }
//    if(empty($user_id)){
//        $user_id = is_login();
//    }
//
//    //查询行为,判断是否执行
//    $action_info = M('action')->getByName($action);
//    if($action_info['status'] != 1){
//        return '该行为被禁用或删除';
//    }
//    //插入行为日志
//    $data['action_id'] = $action_info['id'];
//    $data['user_id'] = $user_id;
//    $data['action_ip'] = ip2long(get_client_ip());
//    $data['model'] = $model;
//    $data['record_id'] = $record_id;
//    $data['create_time'] = NOW_TIME;
//    //系统日志记录操作url参数
//    $data['remark'] = '执行路径url：'.$_SERVER['REQUEST_URI'];
//    M('action_log')->add($data);
//    if(!empty($action_info['rule'])){
//        //解析行为
//        $rules = parse_action($action, $user_id);
//        //执行行为
//        $res = execute_action($rules, $action_info['id'], $user_id);
//    }
//}
function action_log($action = null, $model = null, $record_id = null, $user_id = null){



    //参数检查

    if(empty($action) || empty($model) || empty($record_id)){

        return '参数不能为空';

    }

    if(empty($user_id)){

        $user_id = is_login();

    }



    //查询行为,判断是否执行

    $action_info = M('Action')->getByName($action);

    if($action_info['status'] != 1){

        return '该行为被禁用或删除';

    }



    //插入行为日志

    $data['action_id']      =   $action_info['id'];

    $data['user_id']        =   $user_id;

    $data['action_ip']      =   ip2long(get_client_ip());

    $data['model']          =   $model;

    $data['record_id']      =   $record_id;

    $data['create_time']    =   NOW_TIME;



    //解析日志规则,生成日志备注

    if($action_info['log'] != 1 ){

        if(preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)){

            $log['user']    =   $user_id;

            $log['record']  =   $record_id;

            $log['model']   =   $model;

            $log['time']    =   NOW_TIME;

            $log['data']    =   array('user'=>$user_id,'model'=>$model,'record'=>$record_id,'time'=>NOW_TIME);

            foreach ($match[1] as $value){//$match[0][0]:[admin_group|title];$match[1][0]:admin_group|title;$match[1][1]:update_time|time_format

                $param = explode('|', $value);

                if(isset($param[1])){//$param[1]:time_format（mysql的time_format()函数）

                    $replace[] = call_user_func($param[1],$log[$param[0]]);//$param[0]:update_time
//                    var_dump("param:".$param[1]);//param[1]title\time_format
                }else{
//                    var_dump("param:".$param[1]);
                    $replace[] = $log[$param[0]];

                }

            }
            //数组$replace：标准时间，数组$match[0]：【】的内容，str_replace把[]替换成标准时间
            $data['remark'] =   str_replace($match[0], $replace, $action_info['log']);//$replace[1]:2016-05-12 17:15

//            var_dump("11111111:".$match[0]."/".$replace."/".$action_info['log']);
//            var_dump("remark:".$data['remark']);
        }else{

            $data['remark'] =   "手机端操作";
//            var_dump("22:".$data['remark']);
        }

    }else{

        //未定义日志规则，记录操作url

        $data['remark']     =   '操作url：'.$_SERVER['REQUEST_URI'];
//        var_dump("33:".$data['remark']);

    }



    M('ActionLog')->add($data);



    if(!empty($action_info['rule'])){

        //解析行为

        $rules = parse_action($action, $user_id);



        //执行行为

        $res = execute_action($rules, $action_info['id'], $user_id);

    }

}
