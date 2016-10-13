<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/1
 * Time: 18:07
 */
namespace Mobile\Controller;
use Think\Controller;
use Think\Template\Driver\Mobile;

class YudingController extends Controller {
	public function index(){
		$s = M('store');
		$q = M('shangquan');
		$c = M('store_category');
		$open = C('OPENTIME');
		$open = explode('-',$open);
		if($_GET){
			$a = $_GET['numm'];
			if($_SESSION['sqid']==''&& $_SESSION['cid']==''){
				$store = $s->join('LEFT JOIN wm_picture ON wm_store.store_logo_id = wm_picture.id')->field('wm_store.*,wm_picture.path')->limit($a,5)->select();
				foreach($store as $k=>$v){
					$store[$k]['timea']=$open[0];
					$store[$k]['timeb']=$open[1];
				}
				$_SESSION['sqid']='';
				$_SESSION['cid']='';
				$this->ajaxReturn($store);
				return;
			}elseif($_SESSION['sqid']!==''&&$_SESSION['cid']==''){
				$where = "wm_store.sq_id = ".$_SESSION['sqid'];
			}elseif($_SESSION['sqid']==''&&$_SESSION['cid']!==''){
				$where = "wm_store.store_c_id = ".$_SESSION['cid'];
			}else{
				$where = "wm_store.sq_id =". $_SESSION['sqid']." AND wm_store.store_c_id =". $_SESSION['cid'];
			}
			$store = $s->join('LEFT JOIN wm_picture ON wm_store.store_logo_id = wm_picture.id')->where($where)->field('wm_store.*,wm_picture.path')->limit($a,5)->select();
			foreach ($store as $k => $v) {
				$store[$k]['timea']=$open[0];
				$store[$k]['timeb']=$open[1];
			}
			$_SESSION['sqid']='';
			$_SESSION['cid']='';
			$this->ajaxReturn($store);
		}else{
			if($_POST){
				$sqid = $_POST['sqid'];
				$cid = $_POST['cid'];
				$_SESSION['sqid']=$sqid;
				$_SESSION['cid']=$cid;
				if($sqid==''&&$cid==''){
					$store = $s->join('LEFT JOIN wm_picture ON wm_store.store_logo_id = wm_picture.id')->field('wm_store.*,wm_picture.path')->limit(7)->select();
					foreach ($store as $k => $v) {
						$store[$k]['timea']=$open[0];
						$store[$k]['timeb']=$open[1];
					}
					$this->ajaxReturn($store);
					return;
				}elseif($sqid!==''&&$cid==''){
					$where = "wm_store.sq_id = $sqid";
				}elseif($sqid==''&&$cid!==''){
					$where = "wm_store.store_c_id = $sqid";
				}else{
					$where = "wm_store.sq_id = $sqid AND wm_store.store_c_id = $cid";
				}
				$store = $s->join('LEFT JOIN wm_picture ON wm_store.store_logo_id = wm_picture.id')->where($where)->field('wm_store.*,wm_picture.path')->limit(7)->select();
				foreach ($store as $k => $v) {
					$store[$k]['timea']=$open[0];
					$store[$k]['timeb']=$open[1];
				}
				$this->ajaxReturn($store);
			}else{
				$store = $s->join('LEFT JOIN wm_picture ON wm_store.store_logo_id = wm_picture.id')->field('wm_store.*,wm_picture.path')->limit(7)->select();
				foreach ($store as $k => $v) {
					$store[$k]['timea']=$open[0];
					$store[$k]['timeb']=$open[1];
			}
				$this->assign('store',$store);
			}
		}
		$quan = $q->select();
		$category =$c->select();
		$this->assign('quan',$quan);
		$this->assign('category',$category);
		$this->display();
	}
	public function myyuding(){
		$id = I('store_id');
		$shop = M('store');
		$cx_store = $shop->where("store_id = $id")->find();
		$timea = explode(',', $cx_store['s_time']);
		$timeb = explode(',', $cx_store['e_time']);
		$cx_store['timea'] = array($timea[0].':'.$timea[1],$timeb[0].':'.$timeb[1]);
		$cx_store['timeb'] = array($timea[2].':'.$timea[3],$timeb[2].':'.$timeb[3]);
		$cai = M('canming');
		$cx_cm = $cai->where("store_id = $id")->select();
		$this->assign('cm',$cx_cm);
		$this->assign('sp',$cx_store);
		$this->display();
	}
	public function thisyuding(){
		if(!is_member_login())redirect(U('Index/getFollow'));
		if($_POST){
			$time = I('time');
			$store_id = I('store_id');
			$food = I('food');
			$bao = I('bao');
			$people = I('people');
			$tel = I('tel');
			$name = I('name');
			$sex = I('sex');
			if($time == '选择日期'){
				$this->ajaxReturn('您还没有选择预定时间');
			}elseif($time < date("Y-m-d")){
				$this->ajaxReturn('时间选择有误');
			}elseif($food == '午餐' && date("H")<21 && date("H")>15){
				$this->ajaxReturn('午餐时间已过');
			}elseif($food == '晚餐' && date("H")>21 && date("H")<15){
				$this->ajaxReturn('晚餐时间已过');
			}elseif(empty($people)){
				$this->ajaxReturn('请输入用餐人数或用餐人数不能为0');
			}elseif(preg_match('/^\d{1,2}$/',$people) == 0){
				$this->ajaxReturn('用餐人数只能填写数字且不超过两位数');
			}elseif($people>50){
				$this->ajaxReturn('已超出最大预定人数');
			}elseif(empty($tel)){
				$this->ajaxReturn('请输入预定电话');
			}elseif(preg_match('/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$/',$tel) == 0){
				$this->ajaxReturn('请正确输入手机号码');
			}elseif(empty($name)){
				$this->ajaxReturn('请输入您的姓名');
			}elseif(preg_match('/^[\x{4e00}-\x{9fa5}]+$/u',$name)==0){
				$this->ajaxReturn('姓名格式不正确');
			}
			$data['yd_name'] = $name;
			$data['yd_sex'] = $sex;
			$data['yd_store'] = $store_id;
			$data['yd_eattime'] = $food;
			$data['yd_bao'] = $bao;
			$data['yd_time'] = $time;
			$data['yd_people'] = $people;
			$data['yd_tel'] = $tel;
			$data['uid'] = is_member_login();
			$yuding = M('yuding');
			$add_yuding = $yuding->add($data);
			if($add_yuding){
				$this->ajaxReturn(true);
			}else{
				$this->ajaxReturn('预定失败');
			}
		}else{
			$id = I('store_id');
			$store = M('store');
			$cx_store = $store->where("store_id = $id")->select();
			$this->assign('store',$cx_store);
			$this->display();
		}
	}
	public function tel(){
		$this->display();
	}
	public function ydlist(){
		if(is_member_login()){
			if($_GET){
				$a = $_GET['flog'];
				$y = M('store')->alias('s')->join('LEFT JOIN wm_yuding AS y ON y.yd_store = s.store_id')->where("y.flog = $a AND y.uid =".is_member_login())->field('y.*,s.store_name,s.address_detail,s.store_logo_id')->select();
				$this->assign('info',$y);
				$this->assign('active',$a);
			}else{
				$y = M('store')->alias('s')->join('LEFT JOIN wm_yuding AS y ON y.yd_store = s.store_id')->where("y.flog = 0 AND y.uid =".is_member_login())->field('y.*,s.store_name,s.address_detail,s.store_logo_id')->select();
				$this->assign('info',$y);
			}
		}else{
			redirect(U('Users/login'));
		}

		$this->display();
	}
}
