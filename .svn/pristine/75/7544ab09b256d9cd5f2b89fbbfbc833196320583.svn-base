<?php
/**
 * Created by PhpStorm.
 * User: WXM
 * Date: 2015/11/13
 * Time: 9:32
 */
namespace Mobile\Controller;
use Think\Controller;
use Think\Template\Driver\Mobile;


class KanjiasController extends Controller {
    public function index(){
        if(is_login()) {
            $shop = M('kanjia_admin');
            $cx_shop = $shop->select();
            $jssdk = new \Mobile\Controller\JSSDK("wx8692c5e7bb713d60", "16c88e7edf75a13163a0c12a56c5b117");
            $signPackage = $jssdk->GetSignPackage();
            $this->assign('signPackage', $signPackage);
            $this->assign('shop', $cx_shop);
            $user_id = is_login();
            $this->assign('user_id', $user_id);
            $this->display();
        }else{
            $this->display('Users/login');
        }
    }
    public function kanjia(){
        $id = I('id');
        $user_id = I('user_id');
        $this->assign("user_id",$user_id);
        $shop = M('kanjia_admin');
        $cx_shop = $shop->where("kj_id = $id")->find();
        $cx_kanjia = M('kanjia')->where("kj_id = $id")->find();
        $this->assign('name',$cx_shop);
        $kj = mt_rand($cx_shop['kj_down'],$cx_shop['kj_up']);
        $cha = $cx_shop['total'] - $cx_kanjia['kanjia_total'];
        if($cha<=$cx_shop['kj_price_min']){
            $price = $cx_kanjia['kanjia_total'] - $cx_shop['kj_price_min'];
            $this->assign('price',$price);
        }else{
            $this->assign('price',$kj);
        }
        $aa = $_REQUEST['code'];
        $oauth = new \Mobile\Controller\OAuth;
        $bb=$oauth->getUserInfo('wx8692c5e7bb713d60','16c88e7edf75a13163a0c12a56c5b117',$aa);
        $this->assign('openid',$bb);
        $this->display();
    }
    public function add(){
        $id = I('id');
        $user_id = I('user_id');
        $store_id = I('store_id');
        $kj_price = I('kj_price');
        $total = I('total');
        $openid = I('openid');
        $uid = is_login();
        $touxiang = I('touxiang');
        $wei_name = I('wei_name');
        if($openid == 'a' || $touxiang == 'a' || $wei_name == 'a'){
            $this->ajaxReturn(3);
        }
        $kanjia = M('kanjiaren');
        $tj['wm_kanjiaren.openid'] = $openid;
        $tj['wm_kanjia.kj_id'] = $id;
        $cx_id = $kanjia->join("JOIN wm_kanjia ON wm_kanjia.kanjia_id = wm_kanjiaren.kanjia_id")->where($tj)->select();
        $cx_zuidi = M('kanjia_admin')->where("kj_id = $id")->find();
        $cx_this = M('kanjia')->where("user_id = $user_id")->find();

        if($cx_id){
            $this->ajaxReturn('您已经砍过价了，请勿重复砍价');
        }else if($cx_zuidi['kj_price_min'] == $cx_this['kanjia_total']){
            $this->ajaxReturn(4);
        }else{
            $kanjiabiao = M('kanjia');
            $cx_kanjiabiao = $kanjiabiao->where("user_id = $user_id")->find();
            if($cx_kanjiabiao == ''){
                $data['store_id'] = $store_id;
                $data['cai_total'] = $total;
                $data['user_id'] = $user_id;
                $data['kj_id'] = $id;
                $data['kanjia_total'] = $total - $kj_price;
                $add_kanjia = $kanjiabiao->add($data);
                $cx_kanjiaid = $kanjiabiao->where("kj_id = $id")->find();
                if($add_kanjia){
                    $info['uid'] = $uid;
                    $info['openid'] = $openid;
                    $info['kanjia_id'] = $cx_kanjiaid['kanjia_id'];
                    $info['kanjia_price'] = $kj_price;
                    $info['kj_time'] = time();
                    $info['nickname'] = $wei_name;
                    $info['headimgurl'] = $touxiang;
                    $kanjia_user = M('kanjiaren')->add($info);
                    if($kanjia_user){
                        $this->ajaxReturn(1);
                    }else{
                        $this->ajaxReturn(2);
                    }
                }else{
                    $this->ajaxReturn(2);
                }
            }else{
                $data['kanjia_total'] = $cx_kanjiabiao['kanjia_total'] - $kj_price;
                $add_kanjia = $kanjiabiao->where("kj_id = $id")->save($data);
                $cx_kanjiaid = $kanjiabiao->where("kj_id = $id")->find();
                if($add_kanjia){
                    $info['uid'] = $uid;
                    $info['kanjia_id'] = $cx_kanjiaid['kanjia_id'];
                    $info['openid'] = $openid;
                    $info['kanjia_price'] = $kj_price;
                    $info['kj_time'] = time();
                    $info['nickname'] = $wei_name;
                    $info['headimgurl'] = $touxiang;
                    $kanjia_user = M('kanjiaren')->add($info);
                    if($kanjia_user){
                        $this->ajaxReturn(1);
                    }else{
                        $this->ajaxReturn(2);
                    }
                }else{
                    $this->ajaxReturn(2);
                }
            }

        }
    }
    public function canming(){
        $id = I('id');
        $canming = M('kanjia_detail');
        $cx_canming = $canming->join("JOIN wm_canming ON wm_canming.cm_id = wm_kanjia_detail.cm_id")
            ->where("kanjia_id = $id")
            ->select();
        $this->ajaxReturn($cx_canming);
    }
    public function detail(){
        $kanjiaid = $_GET['kanjia_id'];
        $user_id = $_GET['user_id'];
        $this->assign('user_id',$user_id);
        $us_id = is_login();
        $this->assign('us_id',$us_id);
        $list = M('kanjia')->where('user_id='.$us_id)->select();
        $cx_user = M('kanjiaren')->select();
        foreach($list as $k=>$v){
            foreach($cx_user as $key=>$val){
                if($v['kanjia_id'] == $val['kanjia_id']){
                    $list[$k]['child'][] = $val;
                }
            }
        }
        $this->assign('list',$list);
        $cx_taocan = M('kanjia_admin')->join("JOIN wm_kanjia ON wm_kanjia.kj_id = wm_kanjia_admin.kj_id")->where("wm_kanjia_admin.kj_id = $kanjiaid")->select();
        $cx_canming = M('kanjia_detail')->join("JOIN wm_canming ON wm_canming.cm_id = wm_kanjia_detail.cm_id")
            ->where("kanjia_id = $kanjiaid")
            ->select();
        foreach($cx_taocan as $k=>$v){
            foreach($cx_canming as $key=>$val){
                if($v['kj_id'] == $val['kanjia_id']){
                    $cx_taocan[$k]['child'][] = $val;
                }
            }
        }
        $this->assign('tc',$cx_taocan);
        $lists = M('kanjia')->where('user_id='.$us_id)->find();
        $cx_taocans = M('kanjia_admin')->where("kj_id = $kanjiaid")->find();
        $shengyu = $lists['kanjia_total'] - $cx_taocans['kj_price_min'];
        $this->assign('shengyu',$shengyu);
        $this->assign("kanjia_total",$lists['kanjia_total']);
        $this->assign("zuidi",$cx_taocans['kj_price_min']);
        $this->display();
    }
    public function getFollow()
    {
        $id = I('id');
        $user_id = I('user_id');
        $urls = 'http://zhg.zhuyousoft.com/index.php?s=/Kanjias/kanjia/&id='.$id.'&user_id='.$user_id;
        $oauth = new \Mobile\Controller\OAuth;
            $oauth =$oauth->getCode("wx8692c5e7bb713d60",$urls , "snsapi_userinfo");
            Header("Location:$oauth");
    }
}