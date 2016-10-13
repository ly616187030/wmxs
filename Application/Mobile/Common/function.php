<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 手机公共文件
 * 主要定义后台公共函数库
 */


/**
 * 通过餐厅id 获得餐厅名称
 * @param $id
 */
function get_store($id){
    return M("Store")->getFieldByStoreId($id,'store_name');
}
/**
 * 通过餐厅id 获得餐厅名称
 * @param $id
 */
function get_qisong($id){
    return M("Store")->getFieldByStoreId($id,'qisong_price');
}
/**
 * 通过餐厅id 获得餐厅名称
 * @param $id
 */
function get_peisong($id){
    return M("Store")->getFieldByStoreId($id,'peisong_price');
}
/**
 * 通过餐厅id 获得餐厅名称
 * @param $id
 */
function get_songda($id){
    return M("Store")->getFieldByStoreId($id,'sh_time');
}

/**
 * 计算两个坐标之间的距离(米)
 *  @param float $fP1Lon 起点(经度)
 * @param float $fP1Lat 起点(纬度)
 * @param float $fP2Lon 终点(经度)
 * @param float $fP2Lat 终点(纬度)
 * @return int
 */
/*function distanceBetween( $fP1Lon,$fP1Lat,$fP2Lon,$fP2Lat){
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
}*/
/**
 * 判断餐厅是否在营业中
 * @param $start string 营业开始时间
 * @param $end string 营业结束时间
 * @return boolean true=营业 false=暂停
 */
/*function is_open($start,$end){
    $curstamp=strtotime(date("G:i",time()));
    if(strpos($start,",")){
        $s=explode(",",$start);
        $e=explode(",",$end);
        if(($curstamp>=strtotime($s[0]) && $curstamp<=strtotime($e[0])) || ($curstamp>=strtotime($s[1]) && $curstamp<=strtotime($e[1]))){
            return 1;
        }else{
            return 0;
        }
    }else{
        if($curstamp>=strtotime($start) && $curstamp<=strtotime($end)){
            return 1;
        }else{
            return 0;
        }
    }

}*/

/**
 * @param $times 营业时间字符串
 * @param int $num 数组索引
 * @return mixed
 */
function yingye_to_time($times,$num=0){
    if(strpos($times,",")) {
        $list = explode(",", $times);
        return $list[$num];
    }else{
        return $times;
    }
}

/**
 * 营业时间是否为2个
 * @param $times
 * @return bool
 */
function yingye_is_two($times){
    if(strpos($times,",")){
        return true;
    }else{
        return false;
    }
}
/**

 * 判断一个坐标在不在指定坐标集范围

 * @param  string $lng $lat 当前坐标 经 纬

 * @param  array $st_poly 范围坐标集

 * @return boolean     true-表示$pt坐标在$poly坐标范围，false-表示不在范围

 */

function MPointInPoly($lng,$lat,$st_poly){
    $pt['x'] = $lng;
    $pt['y'] = $lat;
    $ress= explode(",",$st_poly);
    for($i=0;$i<count($ress);$i+=2){
        $arr_text1['x'] = $ress[$i];
        $arr_text1['y'] = $ress[$i+1];
        $poly[] = $arr_text1;
    }
    for ($c = false, $i = -1, $l = count($poly), $j = $l - 1; ++$i < $l; $j = $i){
        if((($poly[$i]['y'] <= $pt['y'] && $pt['y'] < $poly[$j]['y']) || ($poly[$j]['y'] <= $pt['y'] && $pt['y'] < $poly[$i]['y']))&& ($pt['x'] < ($poly[$j]['x'] - $poly[$i]['x']) * ($pt['y'] - $poly[$i]['y']) / ($poly[$j]['y'] - $poly[$i]['y']) + $poly[$i]['x'])){
            $c = !$c;
        }
    }
    return $c;
}
function Mis_login(){

    $user = cookie('user_auth');

    if (empty($user)) {

        return 0;

    } else {

        return cookie('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;

    }
}
