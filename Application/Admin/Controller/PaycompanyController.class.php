<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-05-31
 * Time: 18:13
 */
namespace Admin\Controller;
class PaycompanyController extends AdminController{

    public function index(){
        $m = M('Company_contract');

        $count = $m->count();
        $Page = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
        $Page->setConfig('first','首页');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('last','末页');
        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $show = $Page->show();// 分页显示输出
        $list = $m->alias('a')
            ->join('LEFT JOIN __COMPANY__ AS c ON a.company_id = c.id')
            ->join('LEFT JOIN __SOFTWARE_VIP__ AS s ON a.software_id = s.id')
            ->field('a.*,c.c_name,s.vip_name')
            ->order('a.id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->display();
    }

    public function checkCompany(){
        $m = M('Company_contract');

        $status = I('status');
        $id = I('id');

        $data = $m->where('id ='.$id)->setField('status',$status);

        if($data > 0){
            action_log('edit_action','Company_contract',$id,is_login());
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }

    }
}