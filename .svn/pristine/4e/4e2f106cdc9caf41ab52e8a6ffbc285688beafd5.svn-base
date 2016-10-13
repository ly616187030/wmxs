<?php
namespace Admin\Controller;
class SoftwareController extends AdminController
{
    public function funcindex(){
        $soft = M('SoftwareFunction');
        if(IS_POST){
            $name = $data['name'] = I('sname');
            empty($name) && $this->error('功能名称不能为空');
            if($soft->add($data)){
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }
        }else{
            $list = $soft->order('listorder ASC')->select();
            $this->assign('list',$list);
            $this->display();
        }
    }

    public function funcdel($id){
        $res = M('SoftwareFunction')->delete($id);
        if($res){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function vipindex(){
        $list = D('Software')->select();
        $this->assign('list',$list);
        $this->display();
    }

    public function vipadd(){
        if(IS_POST){
            $dbs = D('Software');
            if($dbs->create()){
                $list = I('function_list');
                if(!empty($list)) $dbs->function_list = implode(',',$list);
                if($dbs->add()){
                    $this->success('添加成功',U('vipindex'));
                }else{
                    $this->error('添加失败');
                }
            }else{
                $this->error($dbs->getError());
            }
        }else{
            $func =  M('SoftwareFunction')->order('listorder ASC')->select();
            $map['founder_id'] = 'administrator';
            $map['all'] = 3;
            $list =  M('Role')->where($map)->select();
            $this->assign('list',$list);
            $this->assign('func',$func);
            $this->display();
        }
    }

    public function vipedit($id){
        $dbs = D('Software');
        if(IS_POST){
            if($iid = $dbs->create()){
                $list = I('function_list');
                if(!empty($list)) $dbs->function_list = implode(',',$list);
                if($dbs->save()!==false){
                    $this->success('修改成功',U('vipindex'));
                }else{
                    $this->error('修改失败');
                }
            }else{
                $this->error($dbs->getError());
            }
        }else{
            $res = $dbs->find($id);
            $map['founder_id'] = 'administrator';
            $map['all'] = 3;
            $list =  M('Role')->where($map)->select();
            $func =  M('SoftwareFunction')->order('listorder ASC')->select();
            $this->assign('list',$list);
            $this->assign('soft_list',$res);
            $this->assign('func',$func);
            $this->display('vipadd');
        }
    }

    public function vipdel($id){
        $res = M('SoftwareVip')->delete($id);
        if($res){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function listorder($id,$num){
        if(!empty($id)){
            $n = intval($num);
            $res = M('SoftwareFunction')->where('id = '.$id)->setField('listorder',$n);
            if($res) $this->ajaxReturn('success');
        }
    }

    public function upgrade(){
        $func =  M('SoftwareFunction')->order('listorder ASC')->select();
        $vip = M('SoftwareVip')->where('auth_type=1')->select();
        $vid = M('Company')->getFieldById(FID,'vip_id');
        $map['expire_time'] = array('gt',time());
        $contract = M('CompanyContract')->where($map)->find();
        $this->assign('func',$func);
        $this->assign('vip',$vip);
        $this->assign('vid',$vid);
        $this->assign('fid',FID);
        $this->assign('_uid',UID);
        $this->assign('_contract',$contract);
        $this->display();
    }
}