<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------

//开发者二次开发公共函数统一写入此文件，不要修改function.php以便于系统升级。


function distanceBetween( $fP1Lon,$fP1Lat,$fP2Lon,$fP2Lat){
    $fEARTH_RADIUS = 6378137;
    //角度换算成弧度
    $fRadLon1 = deg2rad($fP1Lon);
    $fRadLon2 = deg2rad($fP2Lon);
    $fRadLat1 = deg2rad($fP1Lat);
    $fRadLat2 = deg2rad($fP2Lat);
    //计算经纬度的差值
    $fD1 = abs($fRadLat1 - $fRadLat2);
    $fD2 = abs($fRadLon1 - $fRadLon2);
    //距离计算
    $fP = pow(sin($fD1/2), 2) + cos($fRadLat1) * cos($fRadLat2) * pow(sin($fD2/2), 2);
    return intval($fEARTH_RADIUS * 2 * asin(sqrt($fP)) + 0.5);
}

/**

 * 判断一个坐标在不在指定坐标集范围

 * @param  array $pt 当前坐标数组 pt.x,pt.y

 * @param  array $poly 范围坐标集 array.x,array.y

 * @return boolean     true-表示$pt坐标在$poly坐标范围，false-表示不在范围

 */

function PointInPoly($pt, $poly){
    for ($c = false, $i = -1, $l = count($poly), $j = $l - 1; ++$i < $l; $j = $i){
        if((($poly[$i]['y'] <= $pt['y'] && $pt['y'] < $poly[$j]['y']) || ($poly[$j]['y'] <= $pt['y'] && $pt['y'] < $poly[$i]['y']))&& ($pt['x'] < ($poly[$j]['x'] - $poly[$i]['x']) * ($pt['y'] - $poly[$i]['y']) / ($poly[$j]['y'] - $poly[$i]['y']) + $poly[$i]['x'])){
            $c = !$c;
        }
    }
    return $c;
}

/**
 * 判断餐厅是否在营业中
 * @param $start string 营业开始时间
 * @param $end string 营业结束时间
 * @return boolean true=营业 false=暂停
 */
function is_open($start,$end){
    $curstamp=strtotime(date("G:i",time()));
    if(!empty($start) && !empty($end)){
        $s=explode(',',$start);
        $szao="$s[0]";
        $swan="$s[1]";

        $e=explode(',',$end);
        $ezao="$e[0]";
        $ewan="$e[1]";
    }else if(!empty($start) && empty($end)){
        $s=explode(',',$start);
        $szao="$s[0]:$s[1]";
        $swan="$s[2]:$s[3]";
        $ewan=$ezao=0;
    }
    if(($curstamp>strtotime($szao) && $curstamp<strtotime($swan)) || ($curstamp>strtotime($ezao) && $curstamp<strtotime($ewan))){
        return 1;
    }else{
        return 0;
    }

}
/**

 * 检测当前用户是否为管理员

 * @return boolean true-管理员，false-非管理员

 */

function is_administrator($uid = null){

    $uid = is_null($uid) ? is_login() : $uid;
    $res = D('Admin/Roleuser')->toRoleIds($uid);
    return in_array(1,$res);

}
function is_agent($uid = null){

    $uid = is_null($uid) ? is_login() : $uid;
    $res = D('Admin/User')->getFieldById($uid,'user_type');
    return $res == 'agent';

}

function is_admin($uid = null){
    $uid = is_null($uid) ? is_login() : $uid;
    $res = D('Admin/Roleuser')->toRoleIds($uid);
    return in_array(1,$res);

}

function is_founder($uid =null){
    $uid = is_null($uid) ? is_login() : $uid;
    $info = M('store')->where('founder_id = '.$uid)->find();
    return $info == null?null:true;
}

function is_platform_manage($uid =null){
    $uid = is_null($uid) ? is_login() : $uid;
    $res = D('Admin/User')->getFieldById($uid,'user_type');
    return $res == 'manage';
}
function is_company($uid=null){
    $uid = is_null($uid) ? is_login() : $uid;
    $res = D('Admin/User')->getFieldById($uid,'user_type');
    return $res == 'company';
}

function is_company_member($uid=null){
    $uid = is_null($uid) ? is_login() : $uid;
    $res = D('Admin/User')->getFieldById($uid,'user_type');
    return $res == 'company_member';
}
function push_msg_client($uid){
    if(is_array($uid)){
        foreach($uid as $v){
            //$url='http://'.$_SERVER['HTTP_HOST'].":2121?type=publish&to=".$v."&content=success";
            $url="http://www.zhaohuoguo.cn:2121?type=publish&to=".$v."&content=success";
            http_get($url);
        }
    }else{
        //$url='http://'.$_SERVER['HTTP_HOST'].":2121?type=publish&to=".$uid."&content=success";
        $url="http://www.zhaohuoguo.cn:2121?type=publish&to=".$uid."&content=success";
        return http_get($url);
    }
}

function http_get($url) {
    $oCurl = curl_init ();
    if (stripos ( $url, "https://" ) !== FALSE) {
        curl_setopt ( $oCurl, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $oCurl, CURLOPT_SSL_VERIFYHOST, FALSE );
    }
    curl_setopt ( $oCurl, CURLOPT_URL, $url );
    curl_setopt ( $oCurl, CURLOPT_RETURNTRANSFER, 1 );
    $sContent = curl_exec ( $oCurl );
    $aStatus = curl_getinfo ( $oCurl );
    curl_close ( $oCurl );
    if (intval ( $aStatus ["http_code"] ) == 200) {
        return $sContent;
    } else {
        return false;
    }

}

function generate_username($number=8){
    $ticket = M('TicketsU64');
    $map['stub'] = 'a';
    $ticket->add($map,'',true);
    $lastid = $ticket->getLastInsID();
    $str = pow(10,$number-1);
    return (int)$str+$lastid;
}

/**获取用户分组
 * @param $list
 * @param $id
 * @return mixed
 */
function listGroups($list,$id){
    static $result = array();
    foreach($list as $key=>$val){
        if($id == $val['pid']){
            array_push($result,$val['id']);
            listGroups($list,$val['id']);
        }
    }
    return $result;
}

//通过配送员ID获取配送员姓名

function get_person_name($id){

    return M("delivery_person")->getFieldByperson_id($id,'person_name');

}

//通过配送员ID获取配送员电话

function get_person_phone($id){

    return M("delivery_person")->getFieldByperson_id($id,'phone');

}

function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0,$strict = true) {
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parent_id = $data[$pid];
            if ($parent_id === null || (String)$root === $parent_id) {
                $tree[] =& $list[$key];
            } else {
                if(isset($refer[$parent_id])){
                    $parent =& $refer[$parent_id];
                    $parent[$child][] =& $list[$key];
                    $tree[$parent_id] = $list[$key];
                    $tree[$parent_id]['count'] = count($tree[$parent_id][$child]);
                } else {
                    if($strict === false){
                        $tree[] =& $list[$key];
                    }
                }
            }
        }
    }
    return $tree;
}


/**

 * 通过餐名id 获得餐名

 * @param $id

 */

function get_cm($id){

    return M("Canming")->getFieldByCm_id($id,'cm_name');

}

/**

 * 通过地址id 获得详细地址

 * @param $id

 */

function get_add($id){

    return M("User_address")->getFieldByAddress_id($id,'detail_address');

}

/**

 * 通过地址id 获得姓名

 * @param $id

 */

function get_name($id){

    return M("User_address")->getFieldByAddress_id($id,'username');

}

/**

 * 通过地址id 获得地址经纬度

 * @param $id

 */

function get_lng($id){

    return M("User_address")->getFieldByAddress_id($id,'lng');

}

function get_lat($id){

    return M("User_address")->getFieldByAddress_id($id,'lat');

}

/**

 * 通过店铺id 获得店铺经,纬度

 * @param $id

 */

function get_shoplng($id){

    return M("Store")->getFieldByStore_id($id,'lng');

}

function get_shoplat($id){

    return M("Store")->getFieldByStore_id($id,'lat');

}

/**

 * 通过商品id 获得商品信息

 * @param $id

 */

function get_sp_name($id){

    return M("shangpin")->getFieldBysp_id($id,'sp_name');

}
function get_sp_danwei($id){

    return M("shangpin")->getFieldBysp_id($id,'sp_danwei');

}
/**

 * 通过商家id 获得商家信息

 * @param $id

 */
function get_store_name($id){

    return M("store")->getFieldBystore_id($id,'store_name');

}

/**

 * 通过仓库id 获得仓库信息

 * @param $id

 */
function get_storage_name($id){

    return M("storage")->getFieldBystorage_id($id,'storage_name');

}
/**

 * 通过商店id 获得商店经度

 * @param $id

 */
function get_store_lng($id){

    return M("store")->getFieldBystore_id($id,'lng');

}
/**

 * 通过商店id 获得商店纬度

 * @param $id

 */
function get_store_lat($id){

    return M("store")->getFieldBystore_id($id,'lat');

}
/**

 * 通过送餐地址id 获得送餐经度

 * @param $id

 */
function get_user_lng($id){

    return M("user_address")->getFieldByaddress_id($id,'lng');

}
/**

 * 通过送餐地址id 获得送餐纬度

 * @param $id

 */
function get_user_lat($id){

    return M("user_address")->getFieldByaddress_id($id,'lat');

}
/**

 * 通过配送员id 获得配送员经度

 * @param $id

 */
function get_person_lng($id){

    return M("delivery_person")->getFieldByperson_id($id,'lng');

}
/**

 * 通过配送员id 获得配送员纬度

 * @param $id

 */
function get_person_lat($id){

    return M("delivery_person")->getFieldByperson_id($id,'lat');

}
/**

 * 通过配送员id 获得配送员纬度

 * @param $id

 */
function get_store_phone($id){

    return M("store")->getFieldBystore_id($id,'phone');

}
/**

 * 通过配送员id 获得配送员纬度

 * @param $id

 */
function get_store_tel($id){

    return M("store")->getFieldBystore_id($id,'tel');

}

/**

 * 通过商家id 获得点餐人信息

 * @param $id

 */
function get_user_address($id){

    return M("user_address")->getFieldByaddress_id($id,'username');

}
function get_address($id){

    return M("user_address")->getFieldByaddress_id($id,'detail_address');

}
function get_user_phone($id){

    return M("user_address")->getFieldByaddress_id($id,'phone');

}
/**
 * 通过商家id 获得点餐人信息
 * @param $id
 */
function get_canpin_name($id){

    return M("canpin")->getFieldBycanpin_id($id,'canpin_name');

}

/**
 * 通过用户电话获得用户名
 */
function get_user_name($id){

    return M("ucenter_member")->getFieldBymobile($id,'username');

}


/**

 *

 * 获取城市名称

 * @param string $type 省，市,区

 * @param string $code 编码

 * @param string 市，区名称

 */

function get_city_name($type,$code){

    $map['code']=$code;

    if($type=='province'){

        $data=M('AddressProvince')->where($map)->find();

        return $data['name'];

    }elseif($type=='city'){

        $data=M('AddressCity')->where($map)->find();

        return $data['name'];

    }elseif($type=='town'){

        $data=M('AddressTown')->where($map)->find();

        return $data['name'];

    }

}

/**
 * 通过用户id获取电话
 */
function user_phone($id){

    return M("ucenter_member")->getFieldByid($id,'mobile');

}
/**

 * @param $id int 商圈id

 */

function get_shangquan_name($id){

    $res=M("Shangquan")->getFieldBySqId($id,'sq_name');

    return $res;

}
/**
 * 获取上传文件路径
 * @param  int $id 文件ID
 * @return string
 */
function get_new_cover($cover_id, $field = null){

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

 * 字符串截取，支持中文和其他编码

 * @static

 * @access public

 * @param string $str 需要转换的字符串

 * @param string $start 开始位置

 * @param string $length 截取长度

 * @param string $charset 编码格式

 * @param string $suffix 截断显示字符

 * @return string

 */

function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {

    if(function_exists("mb_substr"))

        $slice = mb_substr($str, $start, $length, $charset);

    elseif(function_exists('iconv_substr')) {

        $slice = iconv_substr($str,$start,$length,$charset);

        if(false === $slice) {

            $slice = '';

        }

    }else{

        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";

        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";

        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";

        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";

        preg_match_all($re[$charset], $str, $match);

        $slice = join("",array_slice($match[0], $start, $length));

    }

    return $suffix ? $slice.'...' : $slice;

}
/*
 * 生成订单唯一编号
 */
function createOrdersn(){
    //$store_sn = M('Store')->where('uid = '.is_login())->getField('store_sn');

    $res = time().substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 4);
    return $res;

    /*$map['order_sn'] = $res;

    $data_sn=M("Order")->where($map)->count();

    if($data_sn == 0){
        return $res;
    }else if($data_sn > 0){
        $this->createOrdersn();
    }*/


}
/*
 * 根据来源id查询来源
 */
function getSource($id){
    $source = C('ORDER_SOURCE');
    foreach($source as $k=>$v){
        if($id == $k){
            return $v;
        }
    }
}


/*
 * 判断是否开启打印功能
 */
function is_print(){
    $m = M('Admin_user');
    $n = M('Store');

    $uid = is_login();

    $user_type = $m->where('id = '.$uid)->getField('user_type');

    if($user_type == 'company_member'){
        $data = $n->where('uid = '.$uid)->find();
        if(!empty($data)){

            if($data['is_kitchen_print'] == 0 && $data['is_qiantai_print'] == 0){
                return 0;
            }else{
                return 1;
            }
        }else{
            return -1;
        }
    }else{
        return -2;
    }


}