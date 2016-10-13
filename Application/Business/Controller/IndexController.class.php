<?php
namespace Business\Controller;

use Admin\Controller\AdminController;

class IndexController extends AdminController
{
    public function setSession(){



        $dp_id = I('store_id');

        $dp_mc = I('store_name');

        session('store_id',$dp_id);

        session('store_name',$dp_mc);

        $this->ajaxReturn("1");

    }



    public function getSession(){

        $store_id = session('store_id');

        $store_name = session('store_name');

        $array_data = array();

        $array_data["store_id"] = $store_id;

        $array_data["store_name"] = $store_name;

        $this->ajaxReturn($array_data);

    }
}