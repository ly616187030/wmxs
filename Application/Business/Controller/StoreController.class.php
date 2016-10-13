<?php

/**

 * 店铺管理数据库

 * 数据库表名 wm_store

 * store_id        店铺ID

 * store_name      店铺名称

 * store_logo_id   店铺logo

 * store_desc      店铺简介

 * s_time          营业开始时间

 * e_time          店铺结束时间

 * store_c_id      店铺分类ID

 * address         地址

 * map             地图坐标

 * lng             经度

 * lat             纬度

 * phone           手机

 * sq_id           商圈ID

 * sh_mode         送货方(1自送，2平台送)

 * sh_time         送货时限（分钟）

 * qisong_price    起送价(元)

 * peisong_price   配送费(元)

 * is_online_pay   是否可在线支付

 * is_daohuo_pay   是否可货到付款

 * is_piao         是否有发票

 * is_tui          是否可无理由退货

 * is_dijia        是否支持低价卷

 * dijia_desc      低价卷说明

 * is_timeout_pay  是否超时赔付

 * cs_pay_money    超时赔付金额

 * yingye_flag     营业状态(1营业中，2休息中)

 * pinfen          评分

 */
namespace Business\Controller;
use Admin\Controller\AdminController;
use User\Controller\Admin;
class StoreController extends AdminController{
	public function index(){
		$sids=M("Store")->getFieldByUid(UID,'store_id');

		if(!empty($sids)){

			$map['store_id']=array("in",$sids);

			$data1=M("Store")->alias("s")->join('LEFT JOIN __SHANGQUAN__ AS q ON s.sq_id=q.sq_id')->where($map)->field("s.*,q.sq_name")->select();

		}

		if(IS_ROOT){

			$data1=M("Store")->alias("s")->join('LEFT JOIN __SHANGQUAN__ AS q ON s.sq_id=q.sq_id')->field("s.*,q.sq_name")->select();

		}
		$this->assign("list",$data1);


		$this->meta_title = '管理店铺';

		$this->display(); 

	}



	public function add(){


		if(IS_POST){

			$rules = array(

				array('store_name','require','店铺名称不能为空！'),

				array('store_name','','店铺名称名称已经存在！',0,'unique',1),

				array('store_desc','require','店铺简介不能为空！'),

				array('address_detail','require','店铺地址不能为空！'),
				array('push_msg_id','require','请选择商户账号！')

				);

			$stime=I('post.s_time');
			$etime=I('post.e_time');
			$stime[0]==''?$zao='':$zao=implode(',',$stime);
			$etime[0]==''?$wan='':$wan=implode(',',$etime);
			$User = M("store");  //动态验证

			if ($data1=$User->validate($rules)->create()){
				$data1['s_time']=$zao;
				$data1['e_time']=$wan;
				$data1['store_sn']=time();
                $id = $User->add($data1);

                    if($id){

                    	$this->success('新增成功', U('index'));

				   	}

				   	else{

						$this->error('新增失败');

				    }



            }else{

				$this->error($User->getError());

			}
		}else{


			$gid=D('Admin/Access')->getGroupId(UID);
			$allgid = M('AdminGroup')->select();
			$ids = listGroups($allgid,$gid);
			$ids = array_merge($ids,array($gid));
			$map1['group'] = array('in',$ids);
			$idss = D('Admin/Access')->where($map1)->getField('uid', true);
			if (!empty($idss)) {
				$map['id'] = array('in', $idss);
			} else {
				$map['id'] = null;
			}
			$map['status'] = 1;
			$users=M('AdminUser')->where($map)->field('id,nickname,push_msg_id')->select();

			$city = M('store_category');

			$arr_city = $city->select();

			$this->assign('_store_category',$arr_city);

			//城市三级联动

			$province=A('MenuPct')->getProvince(false);

			$this->assign('_province',$province);
			$this->assign('_user',$users);

			$this->meta_title = '增加店铺';

			$this->display();

			}
	}

	

	public function del(){

		$id = array_unique((array)I('store_id',0));

        $map = array('store_id' => array('in', $id) );

        if(M('store')->where($map)->delete()){

            $this->success('删除成功');

        }else{

            $this->error('删除失败！');

        }

	}

	

	public function edit(){

		if(IS_POST){

			$rules = array(

				array('store_name','require','店铺名称不能为空！'),

				array('store_name','','店铺名称名称已经存在！',0,'unique',1),

				array('store_desc','require','店铺简介不能为空！'),

				array('address_detail','require','店铺地址不能为空！'),
				array('push_msg_id','require','请选择商户账号！')

				);

			$shop = M("store");  //动态验证

			$stime=I('post.s_time');
			$etime=I('post.e_time');
			$stime[0]==''?$zao='':$zao=implode(',',$stime);
			$etime[0]==''?$wan='':$wan=implode(',',$etime);

            $uid = I('uid');


			if ($shop->validate($rules)->create()){

				$shop->s_time =$zao;
                $shop->e_time = $wan;

				if($shop->save()!==false){

					$this->success('保存成功', U('index'));

				}else{

					$this->error('保存失败');

				}

			}else{

				$this->error($shop->getError());

			}

		}else{


			$gid=D('Admin/Access')->getGroupId(UID);
			$allgid = M('AdminGroup')->select();
			$ids = listGroups($allgid,$gid);
			$ids = array_merge($ids,array($gid));
			$map1['group'] = array('in',$ids);
			$idss = D('Admin/Access')->where($map1)->getField('uid', true);
			if (!empty($idss)) {
				$map['id'] = array('in', $idss);
			} else {
				$map['id'] = null;
			}
			$map['status'] = 1;
			$users=M('AdminUser')->where($map)->field('id,nickname,push_msg_id')->select();


			$city = M('store_category'); 

			$arr_city = $city->select(); 

			$this->assign('_store_category',$arr_city);

			//城市三级联动

			$province=A('MenuPct')->getProvince(false);

			$this->assign('_province',$province);

			//读取店铺信息

			$id = I('get.store_id');

            $store = M('store')->alias('s')->join('LEFT JOIN wm_shangquan AS q ON s.sq_id=q.sq_id')->where("store_id=".$id)->field('s.*,q.sq_name')->find();/* 获取数据 */

            if(false === $store){

                $this->error('读取店铺信息失败');

            }


			$tstime=explode(',',$store['s_time']);
			$store['zao1']=$tstime[0];
			$store['zao2']=$tstime[1];
			$store['zao3']=$tstime[2];
			$store['zao4']=$tstime[3];

			$tetime=explode(',',$store['e_time']);
			$store['wan1']=$tetime[0];
			$store['wan2']=$tetime[1];
			$store['wan3']=$tetime[2];
			$store['wan4']=$tetime[3];
			$this->assign('_user',$users);
            $this->assign('_list', $store);

			$this->meta_title = '编辑店铺';

			$this->display();

		}

	}

    //餐厅页面添加用户
	public function addUser(){
        $m = M('Admin_user');
        $n = M('Admin_access');
        $username = I('post.username');
        $password = I('post.password');
        $group = I('post.group');

        if(IS_POST){
            $rules = array(
                //用户名验证
                array('username','require','请填写用户名'),
                array('username', '3,32', '用户名长度为3-32个字符',1, 'length', 1),
                array('username', '', '用户名被占用', 1, 'unique', 1),
                array('username', '/^(?!_)(?!\d)(?!.*?_$)[\w]+$/', '用户名只可含有数字、字母、下划线且不以下划线开头结尾，不以数字开头！', 1, 'regex', 1),

                //密码验证
                array('password', 'require', '请填写密码', 1, 'regex', 1),
                array('password', '6,30', '密码长度为6-30位', 1, 'length',1),
                array('password', '/(?!^(\d+|[a-zA-Z]+|[~!@#$%^&*()_+{}:"<>?\-=[\];\',.\/]+)$)^[\w~!@#$%^&*()_+{}:"<>?\-=[\];\',.\/]+$/', '密码至少由数字、字符、特殊字符三种中的两种组成', 1, 'regex', 1),

                //用户分组验证
                array('group','require','请选择用户分组'),

                //用户分类验证
                array('user_type','require','请选择用户分类')
            );

            if($m->validate($rules)->create()){
                $m->username = $username;
                $m->nickname = $username;
                $m->password = user_md5($password);
                $m->create_time = time();
                $m->update_time = time();
                $m->push_msg_id = date('YmdHis',time());
                $m->status = 1;

                $list = $m->add();
                if($list > 0){
                    $n->create();
                    $n->uid = $list;
                    $n->group = $group;
                    $n->create_time = time();
                    $n->update_time = time();
                    $n->status = 1;
                    if($n->add()){
                        $data['info'] = "添加成功";
                        $data['value'] = 1;
                        $this->ajaxReturn($data);
                    }else{
                        $data['info'] = "添加失败";
                        $data['value'] = 0;
                        $this->ajaxReturn($data);
                    }

                }else{
                    $data['info'] = "添加失败";
                    $data['value'] = 0;
                    $this->ajaxReturn($data);
                }
            }else{
                $this->error($m->getError());
            }
        }
    }

    //查询用户
    public function selectUser(){
        $m = M('Admin_user');
        $n = M('User_type');

        $type_id = $n->where('status = 1')->getField('id',true);

        $where['user_type'] = array('in',$type_id);
        $where['status'] = 1;

        $list = $m->where($where)->select();

        $this->ajaxReturn($list);
    }

	//标记

	public function toogleHide($id,$value = 1){

        $this->editRow('store', array('status'=>$value), array('store_id'=>$id));

    }



    /**

     * 汉字转拼音

     */

    public function cu2py()

    {

        $str = I('get.keys');

        if (!empty($str)) {

            $str = \Vendor\CUtf8_PY::encode($str);

            $this->ajaxReturn(strtoupper($str));

        }

    }

	public function getShangquan(){

		$code=I('code',0);

		if(!empty($code)){

			$res=M("Shangquan")->where("city_code=".$code)->field("sq_id,sq_name")->select();

			$this->ajaxReturn($res);

		}

	}
	//推荐置顶
    public function top(){
	   $where = 'store_id ='.I('store_id');
	    if(I('istop') != 0){
                $select = M('store')->where($where)->setField('istop',0);
		    if($select > 0){
                    $this->success('不推荐成功');
		    }
	    }else{
		    $select = M('store')->where($where)->setField('istop',1);
		    if($select > 0){
			    $this->success('推荐成功');
		    }
	    }

    }
	public  function tai(){

		$id = I('id');

		$restaurant_allot = M('restaurant_allot')->join("LEFT JOIN wm_ucenter_member ON wm_restaurant_allot.uid = wm_ucenter_member.id ")
			->where('store_id ='.$id)->field('wm_restaurant_allot.uid,wm_ucenter_member.username name')->find();

		$User = new UserApi;

		$uid = $User->login_1($restaurant_allot['name']);

		if(0 < $uid){  //UC登录成功
			/* 登录用户 */

			$Member = D('Member');

			if($Member->login($uid)){ //登录用户

				$this->ajaxReturn(1);

			} else {

				$this->ajaxReturn(0);

			}
		}


	}


}