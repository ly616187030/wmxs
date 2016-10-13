<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/3
 * Time: 13:14
 */

namespace Mobile\Controller;
use Think\Controller;


class ShareController extends Controller {
	private $oauth;
	private $appId;
	private $appSecret;
	private $wx;
	public function _initialize() {
		$this->oauth = new \Mobile\Controller\OAuth;
		$this->appId ='wx8692c5e7bb713d60';
		$this->appSecret='16c88e7edf75a13163a0c12a56c5b117';
		import('Vendor.WechatPhpSdk.Wx');
		$options = array(
 			'token'=>'zhaohuoguo', //填写你设定的key
 			//'encodingaeskey'=>'encodingaeskey', //填写加密用的EncodingAESKey
 			'appid'=>'wx8692c5e7bb713d60', //填写高级调用功能的app id
 			'appsecret'=>'16c88e7edf75a13163a0c12a56c5b117' //填写高级调用功能的密钥
				);
		$this->wx=new \TPWechat($options);
	}
	public function getopenid(){
		$uid=Mis_login();
		$re = $this->wx->getOauthAccessToken();
		if(!$uid){
				redirect(U("Usercenter/index"));
		}else{
				if($re){
					$um = M('ucenter_member');
					$open = $um->where('id='.$uid)->setField('openid',$re['openid']);
						redirect(U("Index/index"));
				}
		}
	}
	public function getFO(){
		$urls = 'http://'.$_SERVER['HTTP_HOST'].'/index.php?s=/Share/getopenid';
		$oauth = $this->wx->getOauthRedirect($urls);
		Header("Location:$oauth");
	}
	public function index(){
		if(Mis_login()){
			$user = Mis_login();
			$code = $_REQUEST['code'];
			$jssdk = new \Mobile\Controller\JSSDK($this->appId, $this->appSecret);
			$signPackage = $jssdk->GetSignPackage();
			$this->assign('signPackage',$signPackage);

			$output=$this->oauth->getUserInfo($this->appId,$this->appSecret,$code);
			$openid = $output['openid'];
			$re = M('ucenter_member')->where('id = '.$user)->setField('openid',$openid);

			$this->assign(user,$user);
			$this->assign(page_title,'找火锅-邀请好友');
			$this->display();
		}else{
			$this->display('User/login_content');
		}

	}

	public function share(){
		$id= $_GET['userid'];
		cookie('user_tuijian', $id,86400000);
		$this->assign(page_title,'找火锅-领红包啦！');
		$this->display();
	}
	public function getFollow()
	{
		$urls = 'http://'.$_SERVER['HTTP_HOST'].'/index.php?s=/Share/index';
		$oauth = $this->oauth->getCode($this->appId,$urls , "snsapi_userinfo");
		Header("Location:$oauth");
	}
	public function getF()
	{
		$urls = 'http://'.$_SERVER['HTTP_HOST'].'/index.php?s=/Share/shares';
		$oauth = $this->oauth->getCode($this->appId,$urls , "snsapi_userinfo");
		Header("Location:$oauth");

	}
	public function songhongbao($data){
		$um = M('ucenter_member');
		$aw = $um->where('id=1')->find();
		$uid = cookie('user_tuijian');
		$tj = $um->where('id='.$uid)->find();
		$bao = M('user_bao');
		$baoid = M('bao')->max('bao_id');
		$map['bao_id']=$baoid;
		$hb = M('bao')->where($map)->find();
		$where3['award_id']=$aw['award_id'];
		$ui = M('award')->where($where3)->find();
		$data1['bao_id']=$baoid;
		$data1['user_bao_price']=$ui['award_price'];
		$data1['user_condition']=$hb['bao_condition'];
		$data1['condition_desc']=$hb['bao_condition_desc'];
		$data1['expire_time']=$ui['award_time'];
		$data1['user_phone']=$tj['mobile'];
		$data1['user_openid']=$tj['openid'];
		$data1['status']=0;
		$hongbao = $bao->add($data1);
		if($hongbao){
			$where2['user_openid']=$data;
			$userbao = $bao->where($where2)->order('user_bao_id desc')->find();
			$where4['award_id']=$aw['award_id'];
			$uif = M('award')->where($where4)->find();
			$data2['openid']=$data;
			$data2['tj_id']=cookie('user_tuijian');
			$data2['tj_openid']=$tj['openid'];
			$data2['award']=1;
			$data2['bao_id']=$userbao['user_bao_id'];
			$data2['award_price']=$uif['award_price'];
			$data2['award_status']=$uif['award_status'];
			$data2['award_time']=time();
			$data2['grant_status']=1;
			M("share_detail")->add($data2);
		}
	}

	public function xianjinhongbao($data){
		$um = M('ucenter_member');
		$aw = $um->where('id=1')->find();
		$where1['id']=cookie('user_tuijian');
		$openid=$um->where($where1)->getField('openid');
		if($openid){
			$where4['award_id']=$aw['award_id'];
			$uif = M('award')->where($where4)->find();
			$data2['openid']=$data;
			$data2['tj_id']=cookie('user_tuijian');
			$data2['award']=2;
			$data2['tj_openid']=$openid;
			$data2['award_price']=$uif['award_price'];
			$data2['award_status']=$uif['award_status'];
			$data2['award_time']=time();
			M("share_detail")->add($data2);
		}
	}
	public function shares(){
		$code = $_REQUEST['code'];
		$output=$this->oauth->getUserInfo($this->appId,$this->appSecret,$code);
		$openid = $output['openid'];
		cookie('user_openid', $openid,86400000);
		$uid = cookie('user_tuijian');//获取推荐人的uid
		$tj_openid = M('ucenter_member')->where('id='.$uid)->getField('openid');
		if($openid==$tj_openid){
			$this->assign('tishi','邀请自己不能获得奖励哦！');
		}else{
			$where['openid']=$openid;
			$paichong = M('share_detail')->where($where)->find();
			if($paichong){
				$this->assign('tishi','您已经参与过该活动！');
			}else{
				$aw = M('ucenter_member')->where('id=1')->find();
				$wher['award_id']=$aw['award_id'];
				$res = M('award')->where($wher)->find();
				if($res['award_status']==0){
					if($res['award_name']=="获得红包"){
						$this->songhongbao($openid);
						$this->assign('tishi','恭喜您成功帮好友领取红包！');
					}else if($res['award_name']=="获得现金红包"){
						$this->xianjinhongbao($openid);
						$this->assign('tishi','恭喜您成功帮好友领取现金红包！');
					}
				}
			}
		}
		$this->display();
	}

}

?>

