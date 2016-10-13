<?php
namespace Business\Controller;

use Admin\Controller\AdminController;

class ShanghuController extends AdminController{

    public function index(){

        //接收页面传来的值
        $store_id=I('store_id');
        $store_name=I('store_name');
        $status=I('status');
        $yingye_flag=I('yingye_flag');
        $city=I('city');
        $store_c_id=I('store_c_id');

        //判断值是不是为空，不为空加入条件数组
        if(!empty($store_name)){
            $condition['s.store_name']=$store_name;
        }
        if($status!= null){
            $condition['s.status']=$status;
        }
        if(!empty($yingye_flag)){
            $condition['s.yingye_flag']=$yingye_flag;
        }
        if(!empty($city)){
            $condition['c.name']=array('like',"$city%");
        }
        if(!empty($store_c_id)){
            $condition['s.store_c_id']=$store_c_id;
        }

        $uid=is_login();
        $fids['founder_id'] =FID;
        $adminUser=M('admin_user');
        $data=$adminUser->where('id='.$uid)->find();

        //管理员显示全部商家
        if(is_admin($uid)){}

        //判断商家founder_id等于注册公司的ID
        if(is_company($uid)){
            $condition['s.founder_id']=$data['founder_id'];
            $n = M('Store')->where($fids)->count();
            //$canb = M('Company')->where('id = '.FID)->getField('can_create_num');
            $can = intval($this->canBuildnum(FID));
            if(!empty($n)){
                intval($n) >= $can? $canadd = array('status'=>null,'num'=>$can):$canadd = array('status'=>true,'num'=>$can);
            }else{
                $canadd = array('status'=>true,'num'=>$can);
            }
        }

        //单独商家登陆
        if($data['user_type']=='company_member'){
            $condition['s.uid']=$uid;
        }

        //查询商户类型到前台下拉菜单选项
        $storeCategory=M('store_category');
        $dataCategory=$storeCategory->where($fids)->select();

        //分页所需数据条数
        $Store=M('store');
        $count=$Store->alias('s')
            ->join('left join __ADDRESS_CITY__ as c ON c.code = s.city_code')
            ->join('left join __STORE_CATEGORY__ as sc ON sc.store_c_id = s.store_c_id')
            ->where($condition)
            ->count();

        //分页数据设置
        $Page       = new \Think\Page($count,15);
        $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条</span>');
        $Page->setConfig('prev','<');
        $Page->setConfig('next','>');
        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $Page->lastSuffix = false;
        $show       = $Page->show();

        //根据条件查询前台所需商家信息
        $info=$Store->alias('s')
            ->join('left join __ADDRESS_CITY__ as c ON c.code = s.city_code')
            ->join('left join __STORE_CATEGORY__ as sc ON sc.store_c_id = s.store_c_id')
            ->field('s.*,c.name,sc.store_c_name')
            ->where($condition)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

        $this->assign('dataCategory',$dataCategory);
        $this->assign('store_id',$store_id);
        $this->assign('store_name',$store_name);
        $this->assign('status',$status);
        $this->assign('yingye_flag',$yingye_flag);
        $this->assign('city',$city);
        $this->assign('store_c_id',$store_c_id);
        $this->assign('info',$info);
        $this->assign('page',$show);
        $this->assign('canadd',$canadd);
        $this->display();
    }


    public function add(){

        if(IS_POST){
            $rules = array(
                array('store_name','require','店铺名称不能为空！'),
                //array('store_name','','店铺名称名称已经存在！',0,'unique',1),
                array('store_desc','require','店铺简介不能为空！'),
                array('address_detail','require','店铺地址不能为空！'),
            );

            $stime=I('post.s_time');
            $etime=I('post.e_time');
            $stime[0]==''?$zao='':$zao=implode(',',$stime);
            $etime[0]==''?$wan='':$wan=implode(',',$etime);
            $User = M("store");  //动态验证
            if ($data1=$User->validate($rules)->create()){

                    $adminUser=M('admin_user');
                    $data=$adminUser->where('id='.UID)->field('founder_id')->find();

                    $data1['founder_id']=$data['founder_id'];
                    $data1['s_time']=$zao;
                    $data1['e_time']=$wan;
                    $data1['store_sn']=time();
                    $id = $User->add($data1);

                    if($id){
                        $this->success('新增成功', U('index'));
                    } else{
                        $this->error('新增失败');
                    }
                }else{
                    $this->error($User->getError());
                }

        }else{

            $adminUser=M('admin_user');
            $data=$adminUser->where('id='.UID)->field('founder_id')->find();
            $map['founder_id'] = $data['founder_id'];
            $map['status'] = 1;
            $users=$adminUser->where($map)->field('id,nickname,founder_id')->select();

            $city = M('store_category');
            $fids['founder_id'] =FID;
            $arr_city = $city->where($fids)->select();
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
                //array('store_name','','店铺名称名称已经存在！',0,'unique',1),
                array('store_desc','require','店铺简介不能为空！'),
                array('address_detail','require','店铺地址不能为空！'),
//                array('push_msg_id','require','请选择商户账号！')
            );

            $shop = M("store");  //动态验证
            $service_call1 = I('post.service_call1');
            $service_call2 = I('post.service_call2');
            $order_dct = I('order_dct');
            $stime=I('post.s_time');
            $etime=I('post.e_time');
            $stime[0]==''?$zao='':$zao=implode(',',$stime);
            $etime[0]==''?$wan='':$wan=implode(',',$etime);

            if ($shop->validate($rules)->create()){
                if($order_dct == 0 && $service_call2 == ''){
                    $shop->service_call = $service_call1;
                }else{
                    $shop->service_call = $service_call2;
                    $shop->order_dct = $order_dct;
                }

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

            $adminUser=M('admin_user');
            $data=$adminUser->where('id='.UID)->field('founder_id')->find();
            $map['founder_id'] = $data['founder_id'];
            $map['status'] = 1;
            $users=$adminUser->where($map)->field('id,nickname,push_msg_id')->select();

            $city = M('store_category');
            $fids['founder_id'] =FID;
            $arr_city = $city->where($fids)->select();
            $this->assign('_store_category',$arr_city);

            //城市三级联动
            $province=A('MenuPct')->getProvince(false);
            $this->assign('_province',$province);

            //读取店铺信息
            $id = I('get.store_id');
            $store = M('store')->alias('s')->where("store_id=".$id)->field('s.*')->find();/* 获取数据 */
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

            $data = array();
            $str = $store['lng_lat'];
            $lines = explode(",", $str);
            $data['distribution_range'] = $store['distribution_range'];
            $data['lng'] = $lines[0];
            $data['lat'] = $lines[1];
            $this->assign('data',$data);
            $this->assign('_user',$users);
            $this->assign('_list', $store);
            $this->display();
        }
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



    public function canBuildnum($uid)
    {

        $ID = M('company')->getFieldById($uid,'vip_id');

        $admin_group = M('software_vip');
        $founder_num = $admin_group->where('id=' . $ID)->getField('store_number');
        return $founder_num;
    }

}