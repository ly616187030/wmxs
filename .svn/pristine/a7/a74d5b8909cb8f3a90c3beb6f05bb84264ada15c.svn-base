<?php
namespace Business\Controller;
use Think\Controller;
class StoreInfoController extends Controller{
    private $store=array();
    private function getStore(){
        $s = M('store');
        $storeMsg=$s->field('store_sn,store_name,lng,lat,sh_mode,sh_time,status,phone,tel,store_name_py,address_detail')->select();
        $this->store = $storeMsg;
    }
    public function storeInfo(){
        $this->getStore();
        $info = array(
            'store'=>$this->store
        );
        exit(json_encode($info));
    }
}