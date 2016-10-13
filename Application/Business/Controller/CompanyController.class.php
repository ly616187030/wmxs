<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-04-13
 * Time: 09:53
 */
namespace Business\Controller;
use Admin\Controller\AdminController;
use Common\Util\Tree;
class CompanyController extends AdminController{

    public function addedit(){
        $m = D('Company');
        $cc = M('Company_category');

        $id = I('id');

        if(IS_POST){
            if($m->create()){
                if($m->save() !== false){
                    action_log('edit_action','Company',$id,is_login());
                    $this->success('更新成功');
                }else{
                    $this->error('更新失败');
                }
            }else{
                $this->error($m->getError());
            }
        }
        $wherefid['id']=FID;
        $list = $m->where($wherefid)->find();
        $list_cc = $cc->select();
        $tree = new Tree();
        $list_cc = $tree->toFormatTree($list_cc);

        $province=A('MenuPct')->getProvince(false);
        if(!empty($list)){
            if(!empty($list['province'])){
                $city = M('Address_city')->where('provinceCode = '.$list['province'])->select();
                $this->assign('_city',$city);
            }
            if(!empty($list['city'])){
                $county = M('Address_town')->where('cityCode = '.$list['city'])->select();
                $this->assign('_county',$county);
            }

            $this->assign('_province',$province);

        }
        $this->assign('list',$list);
        $this->assign('list_cc',$list_cc);
        $this->display();
    }
}