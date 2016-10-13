<?php
/**
 * Created by PhpStorm.
 * Auther: <zhangpf41@163.com>
 * Date: 2015/6/17
 * Time: 16:28
 */

namespace Dispatch\Controller;

use Admin\Controller\AdminController;

class MenuPct1Controller extends AdminController{

    /** 获取省份数据
     * @param bool $type true=ajax json返回 false=普遍返回
     * @return mixed json or array
     */
    public function getProvince($type=true){
        $data=M('address_province')->select();
        if($type){
            $this->ajaxReturn($data);
        }else{
            return $data;
        }
    }

    /** 获取地级市
     * @return mixed json data
     */
    public function getCity(){

        $provinceCode=I('get.provinceCode');
        $map['provinceCode']=$provinceCode;
        $data=M('address_city')->where($map)->select();
        $this->ajaxReturn($data);
    }

    /**
     * 获取区，县
     */
    public function getTown(){

        $towncode=I('get.cityCode');
        $map['cityCode']=$towncode;
        $data=M('address_town')->where($map)->select();
        $this->ajaxReturn($data);
    }
//
//     /**
//     * 获取商圈
//     */
//    public function getSq(){
//
//        $sqcode=I('get.townCode');
//        $map['town']=$sqcode;
//        $data=M('stations')->where($map)->select();
//        $this->ajaxReturn($data);
//    }
}