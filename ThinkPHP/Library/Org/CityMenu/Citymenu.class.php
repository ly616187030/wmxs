<?php
namespace Org\CityMenu;

class Citymenu{



    /** 获取省份数据

     * @param bool $type true=ajax json返回 false=普遍返回

     * @return mixed json or array

     */

    public function getProvince($type=true){

        $data=M('province')->select();

        if($type){

           return json_encode($data);

        }else{

            return $data;

        }

    }



    /** 获取地级市

     * @return mixed json data

     */

    public function getCity($provinceCode){

        $map['provinceCode']=$provinceCode;

        $data=M('city')->where($map)->select();

        return json_encode($data);

    }



    /**

     * 获取区，县

     */

    public function getTown($towncode){

        $map['cityCode']=$towncode;

        $data=M('town')->where($map)->select();

        return json_encode($data);

    }

}