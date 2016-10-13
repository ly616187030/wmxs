<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-01-20
 * Time: 15:21
 */


namespace Printer\Controller;

use Admin\Controller\AdminController;

    class DangkouController extends AdminController
    {
        public function index(){

            //从dangkou表查询数据记录
            $Dangkou = M('dangkou');
            $dangkou = $Dangkou
                ->alias('a')
                ->join('LEFT JOIN __PRINTER__ AS c ON c.id = a.dayinji_id')
                ->field('a.*,c.printer_name')
                ->where('uid='.is_login())
                ->select();

            //分页数据设置
            $count = count($dangkou);
            $Page       = new \Think\Page($count,15);
            $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
            $Page->setConfig('first','首页');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('last','末页');
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
            $Page->lastSuffix = false;
            $show       = $Page->show();

            $this->assign('dangkou',$dangkou);
            $this->assign('page',$show);
            $this->display();
        }

        public function add(){

            $id=I('id');
            $Dangkou = M('dangkou');

            if(IS_POST) {
                $rules = array(
                    array('name', 'require', '档口名称不能为空！'),
                    array('dayinji_id','require','打印机名称不能为空！'),
                );
                if ($Dangkou->validate($rules)->create()) {
                    $Dangkou->uid=is_login();
                    if ($id==0) {
                        if ($Dangkou->add()) {
                            $this->success('添加成功', U('index'));
                        } else {
                            $this->error('添加失败');
                        }
                    } else {
                        if ($Dangkou->save()!==false) {
                            $this->success('更新成功', U('index'));
                        } else {
                            $this->error('更新失败');
                        }
                    }
                } else {
                    $this->error($Dangkou->getError());
                }
            }

            if($id!==0){
                $info = $Dangkou->where('id = '.$id)->find();
                $this->assign('info',$info);
                $this->assign('id',$id);
                $daYinJi=M('printer');
                $dayinji=$daYinJi->where('user_id='.is_login())->select();
                $this->assign('dayinji',$dayinji);
            }

            if($id==0){
                $daYinJi=M('printer');
                $dayinji=$daYinJi->where('user_id='.is_login())->select();
                $this->assign('dayinji',$dayinji);
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