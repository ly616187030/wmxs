<?php
namespace Admin\Controller;
class WeixinController extends AdminController
{
    public function index(){
        $wei = D('Weixin');
        if(IS_POST){
            $id=I('id',0,'intval');
            if(!empty($id)){
                if($wei->create()){
                    if($wei->save()!==false){
                        $this->success('修改成功');
                    }else{
                        $this->error('修改失败');
                    }
                }else{
                    $this->error($wei->getError());
                }
            }else{
                if($wei->create()){
                    if($wei->add()){
                        $this->success('添加成功');
                    }else{
                        $this->error('添加失败');
                    }
                }else{
                    $this->error($wei->getError());
                }
            }

        }else{
            $fids['founder_id'] = FID;
            $ids['id'] = FID;
            $cname = M('Company')->where($ids)->getField('c_name');
            $list = $wei->where($fids)->find();
            $this->assign('cname',$cname);
            $this->assign('list',$list);

        }
        $this->display();
    }

    public function link(){
        $this->assign('fid',FID);
        $this->display();
    }
}