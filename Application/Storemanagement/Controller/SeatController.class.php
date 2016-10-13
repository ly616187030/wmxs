<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/10/22
 * Time: 20:40
 */
namespace Storemanagement\Controller;
use Admin\Controller\AdminController;

class SeatController extends AdminController{

    public function index(){

        //从Caipin表查询数据记录
        $Seatnum = M('seat_num');

        $uid=is_login();
        $admin_info=M('admin_user')->where('id='.$uid)->field('founder_id,user_type')->find();

        //如果是注册公司
        $map['a.founder_id'] = $admin_info['founder_id'];
        if($admin_info['user_type']=='company'){
            $count = $Seatnum->alias('a')
                ->join(' LEFT JOIN __SEAT_LOCATION__ AS b ON b.id = a.f_id')
                ->where($map)
                ->count();
        }
        //如果是注册公司下创建的商家
        if($admin_info['user_type']=='company_member'){
            $count = $Seatnum->alias('a')
                ->join(' LEFT JOIN __SEAT_LOCATION__ AS b ON b.id = a.f_id')
                ->where('a.uid='.$uid)
                ->count();
        }

        //分页数据设置
        $Page       = new \Think\Page($count,15);
        $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
        $Page->setConfig('first','首页');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('last','末页');
        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $Page->lastSuffix = false;
        $show       = $Page->show();

        //如果是注册公司
        if($admin_info['user_type']=='company'){

            $zhuotai = $Seatnum->alias('a')
                ->join(' LEFT JOIN __SEAT_LOCATION__ AS b ON b.id = a.f_id')
                ->where($map)
                ->field('a.*,b.name as leiname')
                ->select();
        }
        //如果是注册公司下创建的商家
        if($admin_info['user_type']=='company_member'){
            $zhuotai = $Seatnum->alias('a')
                ->join(' LEFT JOIN __SEAT_LOCATION__ AS b ON b.id = a.f_id')
                ->where('a.uid='.$uid)
                ->field('a.*,b.name as leiname')
                ->select();
        }

        $this->assign('zhuotai',$zhuotai);
        $this->assign('page',$show);
        $this->display();
    }


    public function add(){

        $id=I('id');
        $Seatnum = M('seat_num');
        $Seatlocation = M('seat_location');
        if(IS_POST) {
            $rules = array(
                array('name','require','桌台名称不能为空！'),
                array('seat_sn','require','桌台编号不能为空！'),
                array('f_id','require','桌台类别不能为空！'),
            );
            if ($Seatnum->validate($rules)->create()) {

                $uid=is_login();
                $admin_info=M('admin_user')->where('id='.$uid)->field('founder_id,user_type')->find();
                $Seatnum->uid = $uid;
                $Seatnum->founder_id = $admin_info['founder_id'];

                $fid = I('f_id');
                $data['f_id'] = $fid;
                $data['seat_sn'] = I('seat_sn');
                $data['name'] = I('name');

                $zuowei=$Seatnum->where('f_id='.$fid)->getField('seat_sn',true);
                if(in_array($data['seat_sn'],$zuowei)){
                    $this->error('此分类的桌台编号已存在！');
                }
                if ($id) {
                    if ($Seatnum->save()!==false) {
                        action_log('edit_action', 'seat_num', "$id", is_login());
                        $this->success('更新成功', U('index'));
                    } else {
                        $this->error('更新失败');
                    }

                } else {
                    $a=$Seatnum->add();
                    if ($a) {
                        action_log('add_action', 'seat_location', "$a", is_login());
                        $this->success('添加成功', U('index'));
                    } else {
                        $this->error('添加失败');
                    }
                }
            } else {
                $this->error($Seatnum->getError());
            }
        }
        if($id){
            $info = $Seatnum->where('id = '.$id)->find();

            $this->assign('info',$info);
            $this->assign('id',$id);
        }
        $feilei=$Seatlocation->where('uid='.is_login())->select();
        $this->assign('feilei',$feilei);
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
            action_log('delete_action', "$m", "$id", is_login());
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }


}

?>