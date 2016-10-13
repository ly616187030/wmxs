<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-04-14
 * Time: 14:22
 */
namespace User\Controller\Admin;
use Admin\Controller\AdminController;
use Common\Util\Tree;
class DepartmentController extends AdminController{

    public function index(){
        $m = M('Department');
        $map['founder_id'] = FID;
        $list = $m->where($map)->select();
        $tree = new Tree();
        $list = $tree->toFormatTree($list);

        foreach($list as $k=>$v){
            $list[$k]['margin_left'] = $v['level'] * 21+50;
        }
        $this->assign('list',$list);
        $this->display();
    }
    public function add(){
        $m = M('Department');

        $data['dt_name'] = I('dt_name');
        $data['listorder'] = 0;
        $data['pid'] = I('pid');
        $data['founder_id'] = FID;

        $id = $m->add($data);

        action_log('add_action','Department',$id,is_login());
        $list = $m->where('id = '.$id)->find();
        $list['level'] = I('level');

        $this->ajaxReturn($list);

    }
    public function edit(){
        $m = M('Department');

        $id = I('id');
        $dt_name = I('dt_name');

        $data['dt_name'] = $dt_name;

        $list = $m->where('id = '.$id)->setField($data);
        if($list > 0){
            action_log('edit_action','Department',$id,is_login());
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(0);
        }
    }
    public function del(){
        $m = M('Department');

        $id = I('id');
        $list = $m->where('pid = ' .$id)->count();
        if($list > 0){
            $data['status'] = -1;
            $data['text'] = '请先删除该栏目下的子栏目';
        }else{
            $list = $m->where('id = '.$id)->delete();
            if($list > 0){
                action_log('delete_action','Department',$id,is_login());
                $data['status'] = 1;
                $data['text'] = '删除成功';
            }else{
                $data['status'] = 0;
                $data['text'] = '删除失败';
            }
        }
        $this->ajaxReturn($data);
    }
}