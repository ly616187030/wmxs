<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Think\Controller;
use Common\Util\Tree1;
class RbacController extends AdminController {

    protected $role_model, $auth_access_model;

    function _initialize() {
        parent::_initialize();
        $this->role_model = D("Admin/Role");
    }

    /**
     * 角色管理，有add添加，edit编辑，delete删除
     */
    public function index() {

        if(is_administrator()){
            $map['founder_id'] = 'administrator';
        }elseif(is_agent()){
            $map['founder_id'] = 'agent';
            $map['user_id'] = UID;
        }elseif(is_company_member()){
            exit('<div style="padding:10px 20px;color:red;"><strong style="border:1px solid #ddd;padding:5px;">无权使用此功能!</strong></div>');
        }else{
            $map['founder_id'] = FID;
        }
        $data = $this->role_model->where($map)->order(array("listorder" => "asc", "id" => "desc"))->select();
        $this->assign("roles", $data);
        $this->display();
    }

    /**
     * 添加角色
     */
    public function add() {
        $this->display();
    }

    /**
     * 添加角色
     */
    public function add_post() {
        if (IS_POST) {
            if ($this->role_model->create()) {
                if(is_administrator()){
                    $fid = 'administrator';
                }elseif(is_agent()){
                    $fid = 'agent';
                }elseif(is_company()){
                    $fid = FID;
                }
                $this->role_model->founder_id = $fid;
                $this->role_model->user_id = UID;
                if ($this->role_model->add()!==false) {
                    $this->success("添加角色成功",U("index"));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($this->role_model->getError());
            }
        }
    }

    /**
     * 删除角色
     */
    public function delete() {
        $id = intval(I("get.id"));
        if ($id == 1) {
            $this->error("超级管理员角色不能被删除！");
        }
        $role_user_model=M("RoleUser");
        $count=$role_user_model->where("role_id=$id")->count();
        if($count){
            $this->error("该角色已经有用户！");
        }else{
            $status = $this->role_model->delete($id);
            if ($status!==false) {
                $this->success("删除成功！", U('index'));
            } else {
                $this->error("删除失败！");
            }
        }

    }

    /**
     * 编辑角色
     */
    public function edit() {
        $id = intval(I("get.id"));
        if ($id == 0) {
            $id = intval(I("post.id"));
        }
        if ($id == 1) {
            $this->error("超级管理员角色不能被修改！");
        }
        $data = $this->role_model->where(array("id" => $id))->find();
        if (!$data) {
            $this->error("该角色不存在！");
        }
        $this->assign("data", $data);
        $this->display();
    }

    /**
     * 编辑角色
     */
    public function edit_post() {
        $id = intval(I("get.id"));
        if ($id == 0) {
            $id = intval(I("post.id"));
        }
        if ($id == 1) {
            $this->error("超级管理员角色不能被修改！");
        }
        if (IS_POST) {
            $data = $this->role_model->create();
            if ($data) {
                if ($this->role_model->save($data)!==false) {
                    $this->success("修改成功！", U('Rbac/index'));
                } else {
                    $this->error("修改失败！");
                }
            } else {
                $this->error($this->role_model->getError());
            }
        }
    }

    /**
     * 角色授权
     */
    public function authorize() {
        $this->auth_access_model = M("AuthAccess");
        //角色ID
        $roleid = intval(I("get.id"));
        if (!$roleid) {
            $this->error("参数错误！");
        }
        $menu = new Tree1();
        $menu->icon = array('│ ', '├─ ', '└─ ');
        $menu->nbsp = '&nbsp;&nbsp;&nbsp;';
        $result = $this->initMenu();
        $newmenus=array();
        $priv_data=$this->auth_access_model->where(array("role_id"=>$roleid))->getField("rule_name",true);//获取权限表数据
        foreach ($result as $m){
            $newmenus[$m['id']]=$m;
        }

        foreach ($result as $n => $t) {
            $result[$n]['checked'] = ($this->_is_checked($t, $roleid, $priv_data)) ? ' checked' : '';
            $result[$n]['level'] = $this->_get_level($t['id'], $newmenus);
            $result[$n]['parentid_node'] = ($t['parentid']) ? ' class="child-of-node-' . $t['parentid'] . '"' : '';
        }
        $str = "<tr id='node-\$id' \$parentid_node>
                       <td style='padding-left:30px;'>\$spacer<input id='permission_\$id' type='checkbox' name='menuid[]' value='\$id' level='\$level' \$checked onclick='javascript:checknode(this);'><label for='permission_\$id'> \$name</label></td>
	    			</tr>";
        $menu->init($result);
        $categorys = $menu->get_tree(0, $str);

        $this->assign("categorys", $categorys);
        $this->assign("roleid", $roleid);
        $this->display();
    }

    /**
     * 角色授权
     */
    public function authorize_post() {
        $this->auth_access_model = D("AuthAccess");
        if (IS_POST) {
            $roleid = intval(I("post.roleid"));
            if(!$roleid){
                $this->error("需要授权的角色不存在！");
            }
            if (is_array($_POST['menuid']) && count($_POST['menuid'])>0) {

                $menu_model=D("Admin/Menu");
                $auth_rule_model=D("Admin/AuthRule");
                $this->auth_access_model->where(array("role_id"=>$roleid))->delete();
                foreach ($_POST['menuid'] as $menuid) {
                    $menu=$menu_model->where(array("id"=>$menuid))->field("app,model,action")->find();
                    if($menu){
                        $app=$menu['app'];
                        $model=$menu['model'];
                        $action=$menu['action'];
                        $name=strtolower("$app/$model/$action");
                        $this->auth_access_model->add(array("role_id"=>$roleid,"rule_name"=>$name,'type'=>$app.'_url'));
                    }
                }

                $this->success("授权成功！", U("index"));
            }else{
                //当没有数据时，清除当前角色授权
                $this->auth_access_model->where(array("role_id" => $roleid))->delete();
                $this->error("没有接收到数据，执行清除授权成功！");
            }
        }
    }
    /**
     *  检查指定菜单是否有权限
     * @param array $menu menu表中数组
     * @param int $roleid 需要检查的角色ID
     */
    private function _is_checked($menu, $roleid, $priv_data) {

        $app=$menu['app'];
        $model=$menu['model'];
        $action=$menu['action'];
        $name=strtolower("$app/$model/$action");
        if($priv_data){
            if (in_array($name, $priv_data)) {
                return true;
            } else {
                return false;
            }
        }else{
            return false;
        }

    }

    /**
     * 获取菜单深度
     * @param $id
     * @param $array
     * @param $i
     */
    protected function _get_level($id, $array = array(), $i = 0) {

        if ($array[$id]['parentid']==0 || empty($array[$array[$id]['parentid']]) || $array[$id]['parentid']==$id){
            return  $i;
        }else{
            $i++;
            return $this->_get_level($array[$id]['parentid'],$array,$i);
        }

    }


    public function member(){
        //TODO 添加角色成员管理

    }


}

