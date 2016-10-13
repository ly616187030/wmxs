<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/10/22
 * Time: 20:40
 */
namespace Seat\Controller;
use Admin\Controller\AdminController;

class SeatcategoryController extends AdminController{

    public function index(){

        //从seat_location表查询数据记录
        $Seatlocation = M('seat_location');
        $uid=is_login();
        if($uid==1){
            $leibie = $Seatlocation->field('id,name')->select();
        }else{
            $leibie = $Seatlocation->where('uid='.$uid)->field('id,name')->select();
        }

        //分页数据设置
        $count = count($leibie);
        $Page       = new \Think\Page($count,15);
        $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
        $Page->setConfig('first','首页');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('last','末页');
        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $Page->lastSuffix = false;
        $show       = $Page->show();

        $this->assign('leibie',$leibie);
        $this->assign('page',$show);
        $this->display();
    }


    public function add(){

        $id=I('id');
        $Seatlocation = M('seat_location');

        if(IS_POST) {
            $rules = array(
                array('name','require','桌台类别名称不能为空！'),
            );
            if ($Seatlocation->validate($rules)->create()) {
                $Seatlocation->uid = is_login();
                if ($id) {
                    if ($Seatlocation->save()!==false) {
                        $this->success('更新成功', U('index'));
                    } else {
                        $this->error('更新失败');
                    }
                } else {
                    if ($Seatlocation->add()) {
                        $this->success('添加成功', U('index'));
                    } else {
                        $this->error('添加失败');
                    }
                }
            } else {
                $this->error($Seatlocation->getError());
            }
        }

        if($id){
            $info = $Seatlocation->where('id = '.$id)->find();

            $this->assign('info',$info);
            $this->assign('id',$id);
        }
        $this->display();
    }


    //删除列表中数据记录
    public function del(){
        $m=I('get.m');
        $id = I('id');
//        $this->ajaxReturn($id);
        $n=M($m);
        //判断id是否是数组类型
        if (is_array($id)) {
            //执行批量删除操作
            $where = 'id in (' . implode(',', $id) . ')';
        } else {
            //执行单挑删除操作
            $where = 'id =' . $id;
        }

        $list = $n->where($where)->delete();
        if ($list > 0) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }


}

?>