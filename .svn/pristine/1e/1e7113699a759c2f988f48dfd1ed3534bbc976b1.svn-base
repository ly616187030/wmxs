<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use Think\Controller;
use Common\Util\Think\Page;
/**
 * 行为控制器
 * @author huajie <banhuajie@163.com>
 */

class ActionController extends AdminController {

    static protected $allow = array();
    /**
     * 行为日志列表
     * @author huajie <banhuajie@163.com>
     */
    public function actionLog(){
        if(IS_POST){
            session('action_id',I('action')?:null);
        }
        if(session('action_id')){
            $map = array('status'=>array('gt', -1),'action_id'=>array('eq', session('action_id')));
            $this->assign('selected',session('action_id'));
        }else {
            $map = array('status' => array('gt', -1));
            $this->assign('selected',0);
        }
        $filter['status'] = 1;
        $list   = $this->lists('ActionLog', $map);
        foreach($list as $v => $n){
            $map['id'] = $n['action_id'];
            $find = M('action')->where($map)->find();
            $list[$v]['action_id'] = $find['title'];
            $list[$v]['type'] = $find['type'];
            $list[$v]['action_ip'] = long2ip($n['action_ip']);
        }
        foreach($list as $n => $v){       
            if($v['type'] == "1"){
                $data['id'] = $v['user_id'];
                $list[$n]['user_id'] = M('admin_user')->where($data)->getField('username');
            }else{
                $data['person_id'] = $v['user_id'];
                $list[$n]['user_id'] = M('delivery_person')->where($data)->getField('person_name'); 
            }           
        }
        $action = M("action")->where($filter)->select();
        $this->assign('action', $action);
        $this->assign('list', $list);
        $this->display();
    }
    public function action(){
        $m =M('action')->select();
        $this->assign('list', $m );
        $this->display();
    }
   //新增行为
    public function add(){
        $action = M('action');
        if(IS_POST){
             $data['name'] = I('name');
             $data['title'] = I('title');
             $data['remark'] = I('remark');
             $data['type'] = I('type');
             $data['log'] = I('log');
             $data['status'] = 1;
             $data['update_time'] = time();
             if($action->add($data)){
                 $this->success('新增成功', U('action'));
             }else{
                 $this->error('新增失败');
             }
        }
        $this->display();
    }

    public function del($id){
        $m = M('action_log');
        if (is_array($id)) {
            //执行批量删除操作
            $where = 'id in (' . implode(',', $id) . ')';
        } else {
            //执行单删除操作
            $where = 'id =' . $id;
        }
        if($m->where($where)->delete() != false){
            $this->success('删除成功', U('action'));
        }else{
            $this->error('删除失败');
        }
    }
    public function del1($id){
        $m = M('action');
        if($m->where('id ='.$id)->delete() != false){
            $this->success('删除成功', U('actionlog'));
        }else{
            $this->error('删除失败');
        }
    }

    public function dels(){
        $action = M('action_log');
        if($action->where('1')->delete()){
            $this->success('删除成功', U('actionlog'));
        }else{
            $this->error('删除失败');
        }
    }
//    public function filter($action){
//        $map['status'] = 1;
//        $map['action_id'] = $action;
//        $list   = $this->lists('ActionLog', $map);
//        $this->assign('list', $list);
//        $this->display(":actionlog");
//    }

}
