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
class MenuController extends AdminController {

    protected $menu_model;
    protected $auth_rule_model;

    function _initialize() {
        parent::_initialize();
        $this->menu_model = D("Admin/Menu");
        $this->auth_rule_model = D("Admin/AuthRule");
    }


    /**
     *  显示菜单
     */
    public function index() {
        $_SESSION['admin_menu_index']="Menus/index";
        $result = $this->menu_model
            ->order(array("listorder" => "ASC"))
            ->select();
        $tree = new Tree1();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';

        $newmenus=array();
        foreach ($result as $m){
            $newmenus[$m['id']]=$m;

        }
        foreach ($result as $n=> $r) {

            $result[$n]['level'] = $this->_get_level($r['id'], $newmenus);
            $result[$n]['parentid_node'] = ($r['parentid']) ? ' class="child-of-node-' . $r['parentid'] . '"' : '';

            $result[$n]['str_manage'] = '<a href="' . U("Menu/add", array("parentid" => $r['id'], "menuid" => I("get.menuid"))) . '">添加子菜单</a> | <a href="' . U("Menu/edit", array("id" => $r['id'], "menuid" => I("get.menuid"))) . '">编辑</a> | <a class="ajax-get confirm" href="' . U("Menu/delete", array("id" => $r['id'], "menuid" => I("get.menuid")) ). '">删除</a> ';
            $result[$n]['status'] = $r['status'] ? "显示" : "隐藏";
            if(APP_DEBUG){
                $result[$n]['app']=$r['app']."/".$r['model']."/".$r['action'];
            }
        }

        $tree->init($result);
        $str = "<tr id='node-\$id' \$parentid_node>
					<td style='padding-left:20px;'><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input input-order'></td>
					<td align='center'>\$id</td>
        			<td align='center'>\$app</td>
					<td align='center'>\$spacer\$name</td>
				    <td align='center'>\$status</td>
					<td align='center'>\$str_manage</td>
				</tr>";
        $categorys = $tree->get_tree(0, $str);
        $this->assign("categorys", $categorys);
        $this->display();
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



    /**
     *  添加
     */
    public function add() {

        $tree = new Tree1();
        $parentid = intval(I("get.parentid"));
        $result = $this->menu_model->order(array("listorder" => "ASC"))->select();
        foreach ($result as $r) {
            $r['selected'] = $r['id'] == $parentid ? 'selected' : '';
            $array[] = $r;
        }
        $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
        $tree->init($array);
        $select_categorys = $tree->get_tree(0, $str);
        $this->assign("select_categorys", $select_categorys);
        $this->display();
    }

    /**
     *  添加
     */
    public function add_post() {
        if (IS_POST) {
            if ($this->menu_model->create()) {
                $app=I("post.app");
                $model=I("post.model");
                $action=I("post.action");
                $name=strtolower("$app/$model/$action");
                $this->menu_model->url = $name;
                if ($this->menu_model->add()!==false) {
                    $menu_name=I("post.name");
                    $mwhere=array("name"=>$name);

                    $find_rule=$this->auth_rule_model->where($mwhere)->find();
                    if(!$find_rule){
                        $this->auth_rule_model->add(array("name"=>$name,"module"=>$app,"type"=>strtolower($app)."_url","title"=>$menu_name));//type 1-admin rule;2-user rule
                    }
                    $this->success("添加成功！", U("index"));
                } else {
                    $this->error("添加失败！");
                }
            } else {
                $this->error($this->menu_model->getError());
            }
        }
    }


    /**
     *  删除
     */
    public function delete() {
        $id = intval(I("get.id"));
        $count = $this->menu_model->where(array("parentid" => $id))->count();
        if ($count > 0) {
            $this->error("该菜单下还有子菜单，无法删除！");
        }
        $m = $this->menu_model->find($id);
        $a = strtolower($m['app']);
        $mm = strtolower($m['model']);
        $cc = strtolower($m['action']);
        $map = "'$a/$mm/$cc'";
        if ($this->menu_model->delete($id)!==false) {
            $this->auth_rule_model->where('name = '.$map)->delete();
            $this->success("删除菜单成功！");
        } else {
            $this->error("删除失败！");
        }
    }

    /**
     *  编辑
     */
    public function edit() {

        $tree = new Tree1();
        $id = intval(I("get.id"));
        $rs = $this->menu_model->where(array("id" => $id))->find();
        $result = $this->menu_model->order(array("listorder" => "ASC"))->select();
        foreach ($result as $r) {
            $r['selected'] = $r['id'] == $rs['parentid'] ? 'selected' : '';
            $array[] = $r;
        }
        $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
        $tree->init($array);
        $select_categorys = $tree->get_tree(0, $str);
        $this->assign("data", $rs);
        $this->assign("select_categorys", $select_categorys);
        $this->display();
    }

    /**
     *  编辑
     */
    public function edit_post() {
        if (IS_POST) {
            if ($this->menu_model->create()) {
                $app=I("post.app");
                $model=I("post.model");
                $action=I("post.action");
                $name=strtolower("$app/$model/$action");
                $this->menu_model->url = $name;
                if ($this->menu_model->save() !== false) {
                    $menu_name=I("post.name");
                    $mwhere=array("name"=>$name);

                    $find_rule=$this->auth_rule_model->where($mwhere)->find();
                    if(!$find_rule){
                        $this->auth_rule_model->add(array("name"=>$name,"module"=>$app,"type"=>"admin_url","title"=>$menu_name));//type 1-admin rule;2-user rule
                    }else{
                        $this->auth_rule_model->where($mwhere)->save(array("name"=>$name,"module"=>$app,"type"=>"admin_url","title"=>$menu_name));//type 1-admin rule;2-user rule
                    }

                    $this->success("更新成功！",U('index'));
                } else {
                    $this->error("更新失败！");
                }
            } else {
                $this->error($this->menu_model->getError());
            }
        }
    }

    //排序
    public function listorders() {
        $status = $this->_listorders($this->menu_model);
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }

    /**
     *  排序 排序字段为listorders数组 POST 排序字段为：listorder
     */
    protected function _listorders($model) {
        if (!is_object($model)) {
            return false;
        }
        $pk = $model->getPk(); //获取主键名称
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['listorder'] = $r;
            $model->where(array($pk => $key))->save($data);
        }
        return true;
    }

}

