<?php


namespace Platform\Controller;
use Admin\Controller\AdminController;

class TablewareController extends AdminController{

	//设置餐具
    public function index(){
     if(IS_POST){

         $rules = array(
             array('tab_deposit','require','餐具押金不能为空！'),
             array('tab_remarks','require','餐具备注不能为空！'),
             array('tab_details','require','餐具详情不能为空！'),
             array('tab_status','require','启用状态必须选择一个！'),
         );

         $User = M("tableware");  //动态验证
         if ($User->validate($rules)->create()){
             $i = $User->tab_id;
             if($i == ''){

                 $id = $User->add();

             }else{
                 $id = $User->save();
             }

             if($id>0){
                 $this->success('设置成功', U('index'));
             }
             else{
                 $this->error('设置失败');
             }

         }else{

             $this->error($User->getError());

         }

     }else{
         $User = M('tableware'); // 实例化User对象
         $ret_data = $User->find();
		 $this->assign('list',$ret_data);
		 $this->assign("_bootstrap",1); //引入bootstrap
         $this->meta_title = '餐具设置';
         $this->display();
     }
  }

}

