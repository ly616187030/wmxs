<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace User\Controller\Admin;
use Admin\Controller\AdminController;
use Common\Util\Tree;
/**
 * 部门控制器
 * @author jry <598821125@qq.com>
 */
class GroupController extends AdminController {
    /**
     * 部门列表
     * @author jry <598821125@qq.com>
     */
    public function index() {
        // 搜索
        $keyword = I('keyword', '', 'string');
        $condition = array('like','%'.$keyword.'%');
        $map['id|title'] = array(
            $condition,
            $condition,
            '_multi' => true
        );

        // 获取所有部门

        $map['founder_id'] = FID;

        $map['status'] = array('egt', '0'); //禁用和正常状态
        $data_list = D('Group')
                   ->where($map)
                   ->order('sort asc, id asc')
                   ->select();

        // 转换成树状列表

        //dump($data_list);
        $tree = new Tree();
        $data_list = $tree->toFormatTree($data_list);
        // 使用Builder快速建立列表页面。
        $builder = new \Common\Builder\ListBuilder();
        $builder->setMetaTitle('部门列表')  // 设置页面标题
                ->addTopButton('addnew')   // 添加新增按钮
                ->setSearch('请输入ID/部门名称', U('index'))
                ->addTableColumn('id', 'ID')
                ->addTableColumn('title', '角色')
                ->addTableColumn('status', '状态', 'status')
                ->addTableColumn('right_button', '操作', 'btn')
                ->setTableDataList($data_list)  // 数据列表
                ->addRightButton('edit')        // 添加编辑按钮
                ->addRightButton('self',array('title'=>'删除','class'=>'label label-danger ajax-get confirm','href'=>U('del',array('id' => '__data_id__'))))
                ->alterTableData(  // 修改列表数据
                    array('key' => 'id', 'value' => '1'),
                    array('right_button' => '<a class="label label-warning">超级管理员无需操作</a>')
                )
                ->display();
    }



    /**
     * 新增部门
     * @author jry <598821125@qq.com>
     */
    public function add() {
        if (IS_POST) {
            $group_object = D('Group');
            $_POST['menu_auth']= json_encode(I('post.menu_auth'));
            $_POST['founder_id']=FID;
            $data = $group_object->create();
            if ($data) {
                $id = $group_object->add($data);
                if ($id) {
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($group_object->getError());
            }
        } else {

            $map['founder_id'] = FID;

            // 获取现有部门
            $map['status'] = array('egt', 0);

            $all_group = select_list_as_tree('Group', $map,'顶级部门');

            $ginfo=D('Admin/Group')->getGroupInfo(UID);
            $menu_json=json_decode($ginfo['menu_auth'],true);
            $menu=array_keys($menu_json);
            $names=implode(',',$menu);
            // 获取功能模块的后台菜单列表
            $tree = new Tree();
            if(!is_administrator()){
                $where['name'] = array('in',$names);
            }
            $where['status'] = 1;
            $moule_list = D('Admin/Module')
                        ->where($where)
                        ->select();  // 获取所有安装并启用的功能模块
            $all_module_menu_list = array();
            foreach ($moule_list as $key => $val) {
                $temp = json_decode($val['admin_menu'], true);
                if(!is_administrator()){
                    $b1 = array_keys($temp);
                    $a1 = $menu_json[$val['name']];
                    foreach($b1 as $k=>$b1v){
                        if(!in_array($b1v,$a1)){
                            unset($temp[$b1v]);
                        }
                    }
                }
                //dump($temp);
                $menu_list_item = $tree->list_to_tree($temp);
                $all_module_menu_list[$val['name']] = $menu_list_item[0];
            }

            $this->assign('all_module_menu_list', $all_module_menu_list);
            $this->assign('all_group', $all_group);
            $this->assign('meta_title', '新增部门');
            $this->display('add_edit');
        }
    }

    /**
     * 编辑部门
     * @author jry <598821125@qq.com>
     */
    public function edit($id) {
        if (IS_POST) {
            $group_object = D('Group');
            $_POST['menu_auth']= json_encode(I('post.menu_auth'));
            $data = $group_object->create();
            if ($data) {
                if ($group_object->save($data)!== false) {
                    $this->success('更新成功', U('index'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($group_object->getError());
            }
        } else {
            // 获取部门信息
            $info = D('Group')->find($id);
            $info['menu_auth'] = json_decode($info['menu_auth'], true);

            //dump($info);
            // 获取现有部门


            $map['founder_id'] = FID;
            $map['status'] = array('egt', 0);
            $all_group = select_list_as_tree('Group', $map, '顶级部门');

            $ginfo=D('Admin/Group')->getGroupInfo(UID);
            $menu_json=json_decode($ginfo['menu_auth'],true);
            $menu=array_keys($menu_json);
            $names=implode(',',$menu);

            // 获取所有安装并启用的功能模块
            if(!is_administrator()){
                $where['name'] = array('in',$names);
            }
            $where['status'] = 1;
            $moule_list = D('Admin/Module')
                        ->where($where)
                        ->select();

            // 获取功能模块的后台菜单列表
            $tree = new Tree();
            $all_module_menu_list = array();
            foreach ($moule_list as $key => $val) {
                $temp = json_decode($val['admin_menu'], true);
                if(!is_administrator()){
                    $b1 = array_keys($temp);
                    $a1 = $menu_json[$val['name']];
                    foreach($b1 as $k=>$b1v){
                        if(!in_array($b1v,$a1)){
                            unset($temp[$b1v]);
                        }
                    }
                }
                $menu_list_item = $tree->list_to_tree($temp);
                $all_module_menu_list[$val['name']] = $menu_list_item[0];
            }

            $this->assign('info', $info);
            $this->assign('all_module_menu_list', $all_module_menu_list);
            $this->assign('all_group', $all_group);
            $this->assign('meta_title', '编辑部门');
            $this->display('add_edit');
        }
    }

    /**
     * 设置一条或者多条数据的状态
     * @author jry <598821125@qq.com>
     */
    public function setStatus($model = CONTROLLER_NAME){
        $ids = I('request.ids');
        if (is_array($ids)) {
            if(in_array('1', $ids)) {
                $this->error('超级管理员组不允许操作');
            }
        } else {
            if($ids === '1') {
                $this->error('超级管理员组不允许操作');
            }
        }
        parent::setStatus($model);
    }

    public function del(){

        $id = array_unique((array)I('id',0));

        $map = array('id' => array('in', $id) );

        if(D('Group')->where($map)->delete()){

            $this->success('删除成功');

        }else{

            $this->error('删除失败！');

        }

    }
}
