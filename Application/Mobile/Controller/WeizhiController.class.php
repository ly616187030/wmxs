<?php
namespace Mobile\Controller;
use Think\Controller;
class WeizhiController extends Controller{
    function index(){
        //查询并显示城市
        if ($_POST) {
            $val = I('post.val');
            if($val!=''){ 
                $where['name']  = array('like', '%'.$val.'%');
                $where['first_name']  = array('like','%'.strtoupper($val).'%');
                $where['_logic'] = 'or';
                $data = M('address_city')->where($where)->field('name')->select();
                if($data){$this->ajaxReturn($data);}
                else{$this->ajaxReturn('false');}
            }
        } else {
            $this->display();
        }    
    }
    function shou(){
        //获取城市并打印
        $city = $_GET['city_name'];
        if($_GET){
            session('city',$city);
        }
        $add = M('address_city');
        $msg = $add->join('LEFT JOIN wm_address_town ON wm_address_town.cityCode = wm_address_city.code')
            ->where("wm_address_city.name = '$city'")
            ->select();
        $this->assign('msg',$msg);

        $citys = session('city');
        $this->assign('city',$citys);
        $this->display();
    }
    public function saveaddr(){
        //把获取的地址，经纬度存到session
        $city = I('post.city');
        $title = I('post.address');
        $jingdu = I('post.jingdu');
        $weidu = I('post.weidu');
        if($_POST){
            session('city',$city);
            session('addrtitle',$title);
            session('addrjingdu',$jingdu);
            session('addrweidu',$weidu);

            $this->ajaxReturn(true);
        }
    }
}