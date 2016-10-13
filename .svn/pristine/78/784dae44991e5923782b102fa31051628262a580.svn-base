<?php
/**
 * Created by PhpStorm.
 * User: WYQ
 * Date: 2016/2/1
 * Time: 16:19
 */

namespace Receiveorder\Controller;
use Common\Controller\CommonController;
class GetPushMessageController extends CommonController{
    public function index(){
        $data = I("post.");
        $orderTab = M("order");
        $save = array("ord_peisong_status" => 4,"ps_time" => time());
        $re = $orderTab->where(array("order_id" => $data["order_id"]))->save($save);
        if($re > 0){
            echo "true";
        }else{
            echo "false";
        }
    }
}