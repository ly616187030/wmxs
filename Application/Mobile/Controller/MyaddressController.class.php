<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/3
 * Time: 18:50
 */
namespace Mobile\Controller;
use Think\Controller;
use Think\Template\Driver\Mobile;

class MyaddressController extends MobileController {
    public function index(){
        session('lianxiren',null);
        session('sex',null);
        session('address',null);
        session('menpai',null);
        session('phone',null);
        session('jingdu',null);
        session('weidu',null);
        $id = is_member_login();
        $model_address = M('user_address');
        $cx_address = $model_address->where("uid = $id")->select();
        $cx_addresss = $model_address->where("uid = $id")->field("detail_address")->select();
        foreach($cx_addresss as $k=>$v) {
            $addr = explode("&",$v['detail_address']);
            $cx_address[$k]['detail_address']=$addr;
            $cx_address[$k]['src']=$_GET['src'];
        }
        $this->assign('address',$cx_address);
        if(is_member_login()){
            $this->display();
        }else{
            $this->redirect('Index/getFollow');
        }

    }
    public function newaddress(){
        $info = array(
            array('lng','require','请定位地址',1),
            array('lat','require','请定位地址',1),
            array('username','require','请填写您的姓名',1),
            array('username','/^[\x{4e00}-\x{9fa5}]+$/u','收货人姓名格式错误',1),
            array('gender','require','您还没有选择性别',1),
            array('detail_address','require','详细地址不能为空',1),
            array('phone','require','联系方式不能为空',1),
        );

        //从session取出地址信息，并打印到前台
        $addr = session('address');
        $jingdu = session('jingdu');
        $weidu = session('weidu');
        $name = session('username');
        $pai = session('menpai');
        $phones = session('phone');
        $sexs = session('sex');
        $this->assign('username',$name);
        $this->assign('pai',$pai);
        $this->assign('phones',$phones);
        $this->assign('sexs',$sexs);
        $this->assign('jd',$jingdu);
        $this->assign('wd',$weidu);
        //添加信息到数据库
        $data['detail_address'] =$_POST['detail_address'];
        $data['lng'] = $jingdu;
        $data['lat'] = $weidu;
        $id = is_member_login();
        $data['uid'] = $id;
        $data['username'] = $_POST['username'];
        $data['gender'] = $_POST['gender'];
        $data['phone'] = $_POST['phone'];
        $model_address = M('user_address');
        if($_POST){
            if(!$model_address->validate($info)->create($data)){
                $this->ajaxReturn($model_address->getError());
            }else{
                $cx_flag = $model_address->where("uid = $id and flag = 1")->select();
                if($cx_flag){
                    $data['flag'] = 0;
                    $add_address = $model_address->where("uid = $id")->add($data);
                    if($add_address){
                        echo true;
                        session('lianxiren',null);
                        session('sex',null);
                        session('address',null);
                        session('menpai',null);
                        session('phone',null);
                        session('jingdu',null);
                        session('weidu',null);
                    }else{
                        $this->ajaxReturn('添加失败');
                    }
                }else{
                    $data['flag'] = 1;
                    $add_address1 = $model_address->where("uid = $id")->add($data);
                    if($add_address1){
                        echo true;
                        session('lianxiren',null);
                        session('sex',null);
                        session('address',null);
                        session('menpai',null);
                        session('phone',null);
                        session('jingdu',null);
                        session('weidu',null);
                    }else{
                        $this->ajaxReturn('添加失败');
                    }
                }
            }
        }else{
            $this->display();
        }
    }
    public function saveaddress(){
        //获取地址id，并把相关信息打印到前台
        $uid = is_member_login();
        $id = $_GET['address_id'];
        $model_address = M('user_address');
        $cx_address = $model_address->where("address_id = $id and uid = $uid")->select();
        $this->assign('address',$cx_address);

        $addr = session('address');
        $name = session('lianxiren');
        $pai = session('menpai');
        $phones = session('phone');
        $sexs = session('sex');
        $jingdu = session('jingdu');
        $weidu = session('weidu');
        $this->assign('jd',$jingdu);
        $this->assign('wd',$weidu);
        $this->assign('name',$name);
        $this->assign('pai',$pai);
        $this->assign('phones',$phones);
        $this->assign('sexs',$sexs);
        $this->display();
    }
    public function save(){
        $info = array(
            array('lng','require','请定位地址',1),
            array('lat','require','请定位地址',1),
            array('username','require','请填写您的姓名',1),
            array('username','/^[\x{4e00}-\x{9fa5}]+$/u','收货人姓名格式错误',1),
            array('gender','require','您还没有选择性别',1),
            array('detail_address','require','详细地址不能为空',1),
            array('phone','require','联系方式不能为空',1),
        );

        //修改地址信息，保存到数据库
        $uid = is_member_login();
        $jingdu = session('jingdu');
        $weidu = session('weidu');
        $id = $_POST['address_id'];
        $data['lng'] = $jingdu;
        $data['lat'] = $weidu;
        $data['username'] = $_POST['username'];
        $data['gender'] = $_POST['gender'];
        $data['detail_address'] = $_POST['detail_address'];
        $data['phone'] = $_POST['phone'];
        $model_address = M('user_address');
        if($_POST){
            if(!$model_address->validate($info)->create($data)){
                $this->ajaxReturn($model_address->getError());
            }else{
                $add_address = $model_address->where("address_id = $id and uid = $uid")->save($data);
                if($add_address){
                    session('lianxiren',null);
                    session('sex',null);
                    session('address',null);
                    session('menpai',null);
                    session('phone',null);
                    session('jingdu',null);
                    session('weidu',null);
                    echo true;
                }else{
                    $this->ajaxReturn('修改失败');
                }
            }
        }
    }

    public function del(){
        //获取地址id,并删除该地址
        $uid = is_member_login();
        $id = $_GET['address_id'];
        $model_address = M('user_address');
        $del_address = $model_address->where("address_id = $id and uid = $uid")->delete();
        if($del_address){
            echo true;
        }
        else{
            $this->ajaxReturn('删除失败');
        }
    }
    public function se(){
        //接收地址信息，并保存到session
        $sex = I('get.sex');
        $menpai = I('get.menpai');
        $phone = I('get.phone');
        $lianxiren = I('get.user_name');
        session('sex',$sex);
        session('menpai',$menpai);
        session('phone',$phone);
        session('username',$lianxiren);
    }

}