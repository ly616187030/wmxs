<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Util\Think\Page;
use Think\Model;
use Think\Auth;
/**
 * 用户控制器
 * @author jry <598821125@qq.com>
 */
class UserController extends AdminController {
    /**
     * 用户列表
     * @author jry <598821125@qq.com>
     */
    public function index() {
        // 搜索
        $keyword   = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['u.id|u.username'] = array($condition, $condition, '_multi'=>true);

        // 获取所有用户
        /*if(!is_administrator() && !is_platform_manage()){
            $map['u.founder_id'] = FID;
        }*/
        if(is_agent()){
            $map['u.founder_id'] = 'agent';
            $map['u.from_agent_id'] = UID;
        }elseif(is_company()){
            $map['u.founder_id'] = FID;
        }elseif(is_company_member()){
            exit('<div style="padding:10px 20px;color:red;"><strong style="border:1px solid #ddd;padding:5px;">无权使用此功能!</strong></div>');
        }
        $map['u.status'] = array('egt', '0'); // 禁用和正常状态
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $user_object = D('User');
        $data_list = $user_object
                    ->alias('u')
                    ->join('__USER_TYPE__ as t ON u.user_type=t.name')
                   ->page($p , C('ADMIN_PAGE_ROWS'))
                   ->where($map)
                    ->field('u.*,t.title as type_name')
                   ->order('u.id desc')
                   ->select();
        $page = new Page(
            $user_object->alias('u')->where($map)->count(),
            C('ADMIN_PAGE_ROWS')
        );

        foreach($data_list as &$val){
            if(is_administrator()){
                $val['right_button'] = '<a class="label label-primary" href="'.U('edit', array('id' => $val['id'])).'">查看</a>';
            }else{
                $val['right_button'] = '<a class="label label-primary" href="'.U('edit', array('id' => $val['id'])).'">编辑</a>';
            }
            if(is_agent() && Auth::isDispatcher($val['id'])){
                $val['right_button'] .= '<a style="margin-left:5px;margin-right:5px;" class="label label-info" href="'.U('distShop', array('id' => $val['id'])).'">分配商家</a>';
            }
            if($val['id']!=is_login()){
                switch($val['status']){
                    case '0':
                        $val['right_button'] .= '<a class="label label-success ajax-get" href="'.U('setStatus', array('status' => 'resume', 'ids' => $val['id'])).'">启用</a> ';
                        $val['right_button'] .= '<a class="label label-danger ajax-get" href="'.U('setStatus', array('status' => 'recycle', 'ids' => $val['id'])).'">回收</a>';
                        break;
                    case '1':
                        $val['right_button'] .= '<a class="label label-warning ajax-get" href="'.U('setStatus', array('status' => 'forbid', 'ids' => $val['id'])).'">禁用</a> ';
                        //$val['right_button'] .= '<a class="label label-danger ajax-get" href="'.U('setStatus', array('status' => 'recycle', 'ids' => $val['id'])).'">删除</a>';
                        break;
                }

            }
        }
        // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('用户列表') // 设置页面标题
                ->addTopButton('addnew')  // 添加新增按钮
                ->addTopButton('resume')  // 添加启用按钮
                ->addTopButton('forbid')  // 添加禁用按钮
                //->addTopButton('recycle')  // 添加删除按钮
                ->setSearch('请输入ID/用户名', U('index'))
                ->addTableColumn('id', 'UID')
                ->addTableColumn('type_name', '用户类型')
                ->addTableColumn('nickname', '姓名')
                ->addTableColumn('username', '登录账号')
                ->addTableColumn('email', '邮箱')
                ->addTableColumn('mobile', '手机号')
                ->addTableColumn('create_time', '注册时间', 'time')
                ->addTableColumn('status', '状态', 'status')
                ->addTableColumn('right_button', '操作', 'btn')
                //->addRightButton('edit')          // 添加编辑按钮
                ->setTableDataList($data_list)    // 数据列表
                ->setTableDataPage($page->show()) // 数据列表分页
                ->display();
    }

    /**
     * 新增用户
     * @author jry <598821125@qq.com>
     */
    public function add() {
        C('TOKEN_ON',false);
        if (IS_POST) {
            $user_object = D('User');
            if(!empty($_POST['role_id']) && is_array($_POST['role_id'])){
                $role_ids=$_POST['role_id'];
                unset($_POST['role_id']);
                $storeid = I('storeid');
                $data = $user_object->create();
                $model= new Model();
                $model->startTrans();
                if ($data) {
                    if(is_administrator()){
                        $data['founder_id'] = 'administrator';
                    }elseif(is_agent()){
                        $data['founder_id'] = 'agent';
                        $data['from_agent_id'] = UID;
                    }elseif(is_company()){
                        $data['founder_id'] = FID;
                    }
                    if($_POST['user_type'] == 'agent'){
                        $data['founder_id'] = 'agent';
                    }

                    $data['push_msg_id'] = time();
                    $id = $user_object->add($data);
                    if ($id) {
                        if($_POST['user_type'] == 'agent' && is_administrator()){
                            $user_object->where('id = '.$id)->setField('from_agent_id',$id);
                        }

                        if(!empty($storeid)){
                            $temp=array();
                            foreach($storeid as $store_id){
                                $temp[]=array('user_id'=>$id,'store_id'=>$store_id);
                            }
                            $a=M('assigned_store')->addAll($temp);
                        }else{
                            $a = true;
                        }
                        $role_user_model=M("RoleUser");
                        foreach ($role_ids as $role_id){
                            $b=$role_user_model->add(array("role_id"=>$role_id,"user_id"=>$id));
                        }
                        if($id && $a && $b){
                            $model->commit();
                            action_log('add_action','admin_user,role_user,assigned_store',$id.','.$b.','.$a,UID);
                            $this->success('新增成功', U('index'));
                        }else{
                            $model->rollback();
                        }
                    } else {
                        $this->error('新增失败');
                    }
                } else {
                    $this->error($user_object->getError());
                }
            }else{
                $this->error("请为此用户指定角色！");
            }

        } else {
            //角色
            if(is_company()) {
                $map['founder_id'] = $map2['founder_id'] = FID;
            }elseif(is_agent()){
                $map['founder_id'] = $map2['founder_id'] = 'agent';
                $map['user_id'] = UID;
            }elseif(is_administrator()){
                $map['founder_id'] = $map2['founder_id'] = 'administrator';
            }
            //用户类型
            if(is_administrator()){
                $usertype = D('User/Type')->select();
            }elseif(is_agent()){
                $maps['name'] = array('in','direct_sales,software_install,other,dispatcher');
                $usertype = D('User/Type')->where($maps)->select();
            }elseif(is_company()){
                $usertype = D('User/Type')->where('name="company_member"')->select();
            }
            $map['status'] = 1;
            $storelist = M('Store')->where($map2)->field('store_id,store_name')->select();
            $dep = M('Department')->where($map2)->select();
            $glist=D('Admin/Role')->where($map)->field('id,name')->select();
            $province=A('Business/MenuPct')->getProvince(false);
            $this->assign('_province',$province);
            $this->assign('storelist',$storelist);
            $this->assign('permission',$glist);
            $this->assign('usertype',$usertype);
            $this->assign('dep',$dep);
            $this->display();
        }
    }

    /**
     * 编辑用户
     * @author jry <598821125@qq.com>
     */
    public function edit($id) {
        C('TOKEN_ON',false);
        if (IS_POST) {
            // 密码为空表示不修改密码
            if ($_POST['password'] === '') {
                unset($_POST['password']);
            }

            // 提交数据
            $user_object = D('User');
            if(!empty($_POST['role_id']) && is_array($_POST['role_id'])){
                $role_ids=$_POST['role_id'];
                unset($_POST['role_id']);
                $storeid = I('storeid');

                $data = $user_object->create();
                $model= new Model();
                $model->startTrans();
                if ($data) {
                    if (isset($_POST['password'])) {
                        $data['password'] = user_md5($_POST['password']);
                    }
                    $result = $user_object
                        ->where('id = '.$_POST['id'])
                        //->field('id,nickname,password,username,email,mobile,gender,avatar,update_time,user_type,store_list')
                        ->save($data);
                    if ($result) {
                        if(!is_admin() && !is_company($id)){
                            if(!empty($storeid)){
                                $temp=array();
                                foreach($storeid as $store_id){
                                    $temp[]=array('user_id'=>$id,'store_id'=>$store_id);
                                }
                                if(M('assigned_store')->where('user_id ='.$id)->select()){
                                    M('assigned_store')->where('user_id ='.$id)->delete();
                                    $a=M('assigned_store')->addAll($temp);
                                }else{
                                    $a=M('assigned_store')->addAll($temp);
                                }
                            }else{
                                $a=true;
                            }
                        }else{
                            $a=true;
                        }

                        $role_user_model=M("RoleUser");
                        $role_user_model->where(array("user_id"=>$id))->delete();
                        foreach ($role_ids as $role_id){
                            $b=$role_user_model->add(array("role_id"=>$role_id,"user_id"=>$id));
                        }

                        if($a&&$b){

                            $model->commit();
                            action_log('edit_action','admin_user,role_user,assigned_store',$id.','.$b.','.$a,UID);
                            $this->success('更新成功', U('index'));
                        }else{
                            $model->rollback();
                        }

                    } else {
                        $this->error('更新失败');
                    }
                } else {
                    $this->error($user_object->getError());
                }

            }else{
                $this->error("请为此用户指定角色！");
            }

        } else {
            // 角色
            if(is_company()) {
                $map['founder_id'] = $map2['founder_id'] = FID;
            }elseif(is_agent()){
                $map['founder_id'] = $map2['founder_id'] = 'agent';
                $map['user_id'] = UID;
            }elseif(is_administrator()){
                $map['founder_id'] = $map2['founder_id'] = 'administrator';
            }
            //用户类型
            if(is_administrator()){
                $usertype = D('User/Type')->select();
            }elseif(is_agent()){
                $maps['name'] = array('in','direct_sales,software_install,other,dispatcher');
                $usertype = D('User/Type')->where($maps)->select();
            }elseif(is_company()){
                $usertype = D('User/Type')->where('name="company_member"')->select();
            }
            $map['status'] = 1;
            $storelist = M('Store')->where($map2)->field('store_id,store_name')->select();
            $glist=D('Admin/Role')->where($map)->field('id,name')->select();
            $info = D('User')->find($id);
            $infos=D('assigned_store')->where('user_id ='.$id)->getField('store_id',true);
            $role_user_model=M("RoleUser");
            $role_ids=$role_user_model->where(array("user_id"=>$id))->getField("role_id",true);
            $dep = M('Department')->where($map2)->select();
            $province=A('Business/MenuPct')->getProvince(false);
            $this->assign('_province',$province);
            $this->assign("role_ids",$role_ids);
            $this->assign('permission',$glist);
            $this->assign('storelist',$storelist);
            $this->assign('usertype',$usertype);
            $this->assign('isedit',true);
            $this->assign('info',$info);
            $this->assign('_info',$infos);
            $this->assign('dep',$dep);
            $this->display('add');
        }
    }

    /**
     * 设置一条或者多条数据的状态
     * @author jry <598821125@qq.com>
     */
    public function setStatus($model = CONTROLLER_NAME){
        $ids = I('request.ids');
        if (is_array($ids)) {
            if(in_array('1', $ids)||in_array(is_login(),$ids)) {
                $this->error('非法操作');
            }
        } else {
            if($ids === '1'||$ids==is_login()) {
                $this->error('非法操作');
            }
        }
        parent::setStatus($model);
    }


    public function getUsers(){
        exit('android');
    }
    public function password(){
        $uid = session('user_auth.uid');
        $admin_user = M('admin_user');
        $map['id'] = $uid;
        $user = $admin_user->where($map)->find();
        if(IS_POST){
            $password = I('password');
            $paw = I('paw');
            if($user['password'] == user_md5(I('username'))) {
                if ($password) {
                    if ($paw) {
                        if ($password == $paw) {
                            $password = user_md5(I('password'));
                            if ($user['password'] != $password) {
                                if ($admin_user->where($map)->setField('password', $password)) {
                                    $this->success('修改成功！');
                                } else {
                                    $this->error("修改失败");
                                }
                            } else {
                                $this->error("新密码不能和原密码相同");
                            }
                        } else {
                            $this->error("新密码和确认密码不一致，请从新输入");
                        }
                    } else {
                        $this->error("请输入确认密码");
                    }
                } else {
                    $this->error("请输入新密码");
                }
            }else{
                $this->error("密码输入不正确");
            }
        }else{
            $this->assign('users',$user);
            $this->display();
        }


    }
    /**
     * 回收站
     * @author jry <598821125@qq.com>
     */
    public function recycle() {
        $map['status'] = array('eq', '-1');
        $user_list = D('User')->page(!empty($_GET["p"])?$_GET["p"]:1, C('ADMIN_PAGE_ROWS'))->where($map)->select();
        $page = new Page(D('User')->where($map)->count(), C('ADMIN_PAGE_ROWS'));

        //使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('回收站') //设置页面标题
        ->addTopButton('delete') //添加删除按钮
        ->addTopButton('restore') //添加还原按钮
        ->setSearch('请输入ID/文档名称', U('recycle'))
            ->addTableColumn('id', 'UID')
            ->addTableColumn('user_type', '用户类型')
            ->addTableColumn('nickname', '姓名')
            ->addTableColumn('username', '登录账号')
            ->addTableColumn('email','邮箱')
            ->addTableColumn('mobile','手机')
            ->addTableColumn('create_time','注册时间','time')
            ->addTableColumn('status', '状态', 'status')
            ->addTableColumn('right_button', '操作', 'btn')
            ->setTableDataList($user_list) //数据列表
            ->setTableDataPage($page->show()) //数据列表分页
            ->addRightButton('restore') //添加还原按钮
            ->addRightButton('delete') //添加删除按钮
            ->display();
    }
    public function channels(){
        $channels = D('Channels')->page(1, C('ADMIN_PAGE_ROWS'))->select();
        $page = new Page(D('Channels')->count(), C('ADMIN_PAGE_ROWS'));

        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('渠道申请') //设置页面标题
        ->addTopButton('delete',array('model'=>'Channels')) //添加删除按钮
        ->setSearch('请输入ID/姓名', U('recycle'))
            ->addTableColumn('id', 'ID')
            ->addTableColumn('name', '姓名')
            ->addTableColumn('phone', '电话')
            ->addTableColumn('address', '地址')
            ->addTableColumn('email','邮箱')
            ->addTableColumn('content','留言')
            ->addTableColumn('create_time','申请时间','time')
            ->addTableColumn('right_button', '操作', 'btn')
            ->setTableDataList($channels) //数据列表
            ->setTableDataPage($page->show()) //数据列表分页
            ->addRightButton('delete',array('model'=>'Channels')) //添加删除按钮
            ->display();
    }

    public function channelsuser(){
        // 搜索
        $keyword   = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['u.id|u.username'] = array($condition, $condition, '_multi'=>true);

        // 获取所有用户
        /*if(!is_administrator() && !is_platform_manage()){
            $map['u.founder_id'] = FID;
        }*/
        if(is_agent()){
            $map['u.founder_id'] = 'agent';
            $map['u.from_agent_id'] = UID;
        }elseif(is_company()){
            $map['u.founder_id'] = FID;
        }elseif(is_company_member()){
            exit('<div style="padding:10px 20px;color:red;"><strong style="border:1px solid #ddd;padding:5px;">无权使用此功能!</strong></div>');
        }
        $map['u.status'] = array('egt', '0'); // 禁用和正常状态
        $map['u.user_type'] = 'agent'; // 禁用和正常状态
        $p = !empty($_GET["p"]) ? $_GET['p'] : 1;
        $user_object = D('User');
        $data_list = $user_object
            ->alias('u')
            ->join('__USER_TYPE__ as t ON u.user_type=t.name')
            ->page($p , C('ADMIN_PAGE_ROWS'))
            ->where($map)
            ->field('u.*,t.title as type_name')
            ->order('u.id desc')
            ->select();
        $page = new Page(
            $user_object->alias('u')->where($map)->count(),
            C('ADMIN_PAGE_ROWS')
        );

        foreach($data_list as &$val){
            if(is_administrator()){
                $val['right_button'] = '<a class="label label-primary" href="'.U('edit', array('id' => $val['id'])).'">查看</a>';
            }else{
                $val['right_button'] = '<a class="label label-primary" href="'.U('edit', array('id' => $val['id'])).'">编辑</a>';
            }

            if($val['id']!=is_login()){
                switch($val['status']){
                    case '0':
                        $val['right_button'] .= '<a class="label label-success ajax-get" href="'.U('setStatus', array('status' => 'resume', 'ids' => $val['id'])).'">启用</a> ';
                        $val['right_button'] .= '<a class="label label-danger ajax-get" href="'.U('setStatus', array('status' => 'recycle', 'ids' => $val['id'])).'">回收</a>';
                        break;
                    case '1':
                        $val['right_button'] .= '<a class="label label-warning ajax-get" href="'.U('setStatus', array('status' => 'forbid', 'ids' => $val['id'])).'">禁用</a> ';
                        //$val['right_button'] .= '<a class="label label-danger ajax-get" href="'.U('setStatus', array('status' => 'recycle', 'ids' => $val['id'])).'">删除</a>';
                        break;
                }

            }
        }
        // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('用户列表') // 设置页面标题
        ->addTopButton('addnew')  // 添加新增按钮
        ->addTopButton('resume')  // 添加启用按钮
        ->addTopButton('forbid')  // 添加禁用按钮
        //->addTopButton('recycle')  // 添加删除按钮
        ->setSearch('请输入ID/用户名', U('index'))
            ->addTableColumn('id', 'UID')
            ->addTableColumn('type_name', '用户类型')
            ->addTableColumn('nickname', '姓名')
            ->addTableColumn('username', '登录账号')
            ->addTableColumn('email', '邮箱')
            ->addTableColumn('mobile', '手机号')
            ->addTableColumn('create_time', '注册时间', 'time')
            ->addTableColumn('status', '状态', 'status')
            ->addTableColumn('right_button', '操作', 'btn')
            //->addRightButton('edit')          // 添加编辑按钮
            ->setTableDataList($data_list)    // 数据列表
            ->setTableDataPage($page->show()) // 数据列表分页
            ->display();
    }

    public function distshop(){
        $m = M('Company');
        $n = M('Store');
        $ad = M('Admin_user');

        $did = I('id');

        $where_ad['id'] = $did;

        if(is_agent(UID)){

            $where['from_agent_id'] = UID;
            $list = $m->where($where)->select();
            $list_id = $m->where($where)->getField('id',true);

            $where_store['founder_id'] = array('in',$list_id);
            $list_store = $n->where($where_store)->select();

            $d_store_id = $ad->where($where_ad)->getField('assign_store_id');

            foreach($list as $k=>$v){
                foreach($list_store as $a=>$b){
                    if($v['id'] == $b['founder_id']){
                        $list[$k]['child'][] = $b;
                    }

                }
            }
            foreach($list as $k=>$v){
                if(empty($v['child'])){
                    unset($list[$k]);
                }
            }
            foreach($list as $k=>$v){
                foreach($v['child'] as $a=>$b){
                    if(!empty($d_store_id)){
                        $check = in_array($b['store_id'],explode(',',$d_store_id));
                        if($check == true){
                            $list[$k]['child'][$a]['check_status'] = 1;
                        }else{
                            $list[$k]['child'][$a]['check_status'] = 0;
                        }
                    }
                }
            }

            if(IS_POST){
                $m->startTrans();

                $store_id = I('menu_auth');

                $all_store_id = $n->where($where_store)->getField('store_id',true);

                $where_all_store['store_id'] = array('in',$all_store_id);

                $data_all_store = $n->where($where_all_store)->setField('dispatch_push_id',"");

                $list_ad = $ad->where($where_ad)->find();
                $where_s['store_id'] = array('in',$store_id);
                $data_s = $n->where($where_s)->setField('dispatch_push_id',$list_ad['push_msg_id']);
                $data_ad = $ad->where($where_ad)->setField('assign_store_id',implode(',',$store_id));
                if($data_s > 0 && $data_ad !==false && $data_all_store > 0){
                    $m->commit();
                    action_log('edit_action','Admin_user',$did,is_login());
                    action_log('edit_action','Store',implode(',',$store_id),is_login());
                    $this->success('更新成功',U('index'));
                }else{
                    $m->rollback();
                    $this->error('更新失败');
                }
            }else{
                $this->assign('list',$list);
                $this->assign('id',$did);
                $this->display();
            }
        }else{
            $this->error('当前帐号没有权限');
        }


    }
}
