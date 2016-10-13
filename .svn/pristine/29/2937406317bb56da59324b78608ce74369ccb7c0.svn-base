<?php
/**
 * Created by PhpStorm.
 * User: WXM
 * Date: 2015/11/13
 * Time: 9:32
 */
namespace Mobile\Controller;
use Think\Controller;
use Think\Model;

class KanjiaController extends MobileController {
    private $oauth;
    private $appId;
    private $appSecret;
    public function _initialize() {
       $this->oauth = new \Mobile\Controller\OAuth;
        $this->appId ='wx8692c5e7bb713d60';
        $this->appSecret='16c88e7edf75a13163a0c12a56c5b117';
    }
    public function index(){
        $kanjia = explode(',',$_GET['kanjia']);
        $storeid = $kanjia[0];
        $kanjiaid=$kanjia[1];
        $code = $_REQUEST['code'];
        if(!$code){
            return;
        }
        $kanjiaren=$this->oauth->getUserInfo($this->appId,$this->appSecret,$code);
        $data['kanjia_id']=$kanjiaid;
        $data['openid']=$kanjiaren['openid'];
        $data['nickname']=$kanjiaren['nickname'];
        $data['headimgurl']=$kanjiaren['headimgurl'];
        $ren = M('kanjiaren');
        $re = $ren->where("openid='$data[openid]' AND kanjia_id=$kanjiaid")->field('kanjia_id')->select();
        if($re==null&&$data['openid']!='a'){
            $ren->add($data);
        }else{
            foreach($re as $k=>$v){
                if($v['kanjia_id']!=$kanjiaid){
                    $ren->add($data);
                }
            }
        }

        $price_qu = explode(',',C('KANJIA_PRICE'));
        $price =mt_rand(1,10);
        $store_name=M('store')->where('store_id='.$storeid)->field('store_name')->find();
        $this->assign('kanjiaid',$kanjiaid);
        $this->assign('openid',$data['openid']);
        $this->assign('price',$price);
        $this->assign('store_name',$store_name['store_name']);
        $this->display();
    }
    public function indexx(){
        if(!empty($_GET)){
            $price = $_GET['price'];
            $openid = $_GET['openid'];
            $kanjiaid=$_GET['kanjiaid'];
            $kanjiaren = M('kanjiaren');
            $aa = $kanjiaren->where("openid='$openid' AND kanjia_id=$kanjiaid")->field('kanjia_price')->find();
            if(intval($aa['kanjia_price'])==null){
                $re =$kanjiaren->where("openid='$openid' AND kanjia_id=$kanjiaid")->setField('kanjia_price',$price);
            }else{
                $this->ajaxReturn(array('status'=>true));
            }
            $kanjia_total=0;
            foreach($kanjiaren->where("kanjia_id=$kanjiaid")->field('kanjia_price')->select() as $k=>$v){
                $kanjia_total+=$v['kanjia_price'];
            }
            $res = M('kanjia')->where('kanjia_id='.$kanjiaid)->setField('kanjia_total',$kanjia_total);
            if($re&&$res){
                $this->ajaxReturn(array('status'=>true));
            }
        }
    }
    public function getFollow()
    {
        $kanjia = $_GET['kanjia'];
        $urls = 'http://'.$_SERVER['HTTP_HOST'].'/index.php?s=/Kanjia/index&kanjia='.$kanjia;
        $oauth = $this->oauth->getCode($this->appId,$urls , "snsapi_userinfo");
        Header("Location:$oauth");
    }
    //砍价
    public function kan(){
        if(!Mis_login()){
            redirect(U('User/login'));
        }
        $uid = Mis_login();
        $list = M('kanjia')->join("JOIN wm_store ON wm_store.store_id = wm_kanjia.store_id")
            ->where('wm_kanjia.uid='.$uid)
            ->field('wm_store.store_name,wm_store.store_logo_id,wm_kanjia.*')
            ->select();
        $this->assign('list', $list);

        $jssdk = new \Mobile\Controller\JSSDK($this->appId, $this->appSecret);
        $signPackage = $jssdk->GetSignPackage();
        $this->assign('signPackage',$signPackage);
        $this->display();
    }
    public function detail(){
        $kanjiaid = $_GET['kanjia_id'];
        $list = M('kanjiaren')->where('kanjia_id='.$kanjiaid)->select();
        $this->assign('list',$list);
        $this->display();
    }
    //砍价写入
    public function kanjiaOrder(){
        $uid=Mis_login();
        $posts=I("post.");
        $data['uid']=$uid;
        $data['cai_total']=$posts['cai_total'];
        //$data['total']=$posts['total'];
        $data['store_id']=$posts['storeid'];
        $data['kanjia_time']=time();
        $model= new Model();
        $model->startTrans();
        $flag=false;
        $kanjia_id=$model->table(C('DB_PREFIX').'kanjia')->add($data);
        if(!empty($kanjia_id)){
            //$this->ajaxReturn(array('info'=>$orderid,'status'=>1));
            $data1=A('Cart')->cartView();
            $temp=array();
            foreach($data1[0] as $v){
                $temp[]=array("kanjia_id"=>$kanjia_id,"cm_id"=>$data1[0][$v],"cm_name"=>$data1[1][$v],"price"=>$data1[2][$v],"quantity"=>$data1[3][$v],"total_money"=>$data1[2][$v]*$data1[3][$v]);
            }
            $kanjialist=$model->table(C('DB_PREFIX').'kanjia_detail')->addAll($temp);
            if(!empty($kanjialist)){
                $model->commit();
                $flag=true;
            }else{
                $this->ajaxReturn(array("status"=>0,"info"=>$model->getError()));
            }
            if(!$flag){
                $model->rollback();
            }
            $this->ajaxReturn(array("status"=>$flag,"kanjia_id"=>$kanjia_id));
        }else{
            $this->error($model->getError());
        }
    }
    public function del(){
        $kanjia = explode(',',$_GET['kanjiaid']);
        if($kanjia){
            $kanjiaid=$kanjia[1];
            M('kanjia')->where('kanjia_id='.$kanjiaid)->delete();
            M('kanjia_detail')->where('kanjia_id='.$kanjiaid)->delete();
            M('kanjiaren')->where('kanjia_id='.$kanjiaid)->delete();
        }
    }
}