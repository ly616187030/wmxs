<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/5
 * Time: 9:17
 */
namespace Mobile\Controller;
use Think\Controller;
use Think\Template\Driver\Mobile;

class MapController extends Controller {
    public function index(){
        if($_POST){
            $address = I('post.address');
            $jingdu = I('post.jingdu');
            $weidu = I('post.weidu');
            session('jingdu',$jingdu);
            session('weidu',$weidu);
            $jing = session('jingdu');
            $wei = session('weidu');

            session('address',$address);
            $addr = session('address');

            if($addr && $jingdu && $weidu){
                echo true;
            }
        }else{
            $this->display();
        }
    }
}