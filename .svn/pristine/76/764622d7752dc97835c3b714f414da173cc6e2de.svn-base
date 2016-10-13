<?php
/**
 * Created by PhpStorm.
 * User: WXM
 * Date: 2015/9/22
 * Time: 10:43
 */
namespace Platform\Controller;
use Admin\Controller\AdminController;
class ShangpinController extends AdminController{
    public function index(){
        $sp = M('shangpin');
        $cx_sp = $sp->select();
        $this->assign('sp',$cx_sp);
        $this->display();
    }
    public function add(){
        $this->meta_title = '增加商品';

        if(IS_POST){

            $rules = array(

                array('sp_name','require','商品名称不能为空！'),
                array('sp_danwei','require','商品单位不能为空'),
                array('sp_danjia','require','商品单价不能为空'),
                array('guanggao_name','','商品名称已经存在！',0,'unique',1),

            );

            $User = M("shangpin");  //动态验证

            if ($User->validate($rules)->create()){

                if($User->add()){

                    $this->success('新增成功', U('index'));

                }

                else{

                    $this->error('新增失败');

                }

            }else{

                $this->error($User->getError());

            }

        }else{

            $this->display();

        }
    }
    public function setFlag(){



        $id = I('sp_id');

        $flag = I('get.sp_status');

        $m = M('shangpin');

        if(is_array($id)){

            $where = 'sp_id in ('.implode(',',$id).')';

            if($flag == 1){

                $arr = $m->where($where)->setField('sp_status',1);

                if($arr>0){

                    $this->success("启用成功");

                }else{

                    $this->error("启用失败");

                }

            }elseif($flag == 0){

                $arr = $m->where($where)->setField('sp_status',0);

                if($arr>0){

                    $this->success("禁用成功");

                }else{

                    $this->error("禁用失败");

                }

            }

        }else{

            $where = 'sp_id ='.$id;

            if($flag != 0){

                $arr = $m->where($where)->setField('sp_status',0);

                if($arr>0){

                    $this->success("禁用成功");

                }else{

                    $this->error("禁用失败");

                }

            }else{

                $arr = $m->where($where)->setField('sp_status',1);

                if($arr>0){

                    $this->success("启用成功");

                }else{

                    $this->error("启用失败");

                }

            }
        }





    }
    //编辑商品

    public function edit(){



        if(IS_POST){

            $rules1 = array(

                array('sp_name','require','商品名称不能为空！'),

                array('sp_name','','商品名称已经存在！',0,'unique',2),

            );

            $verify = M("shangpin");  //动态验证
            $id = I('sp_id');
            $where = 'sp_id='.$id;

            if ($verify->validate($rules1)->create()){

                if($verify->where($where)->save()!== false){

                    $this->success('更新成功',U('index'));

                }else{

                    $this->error('修改失败');

                }

            }else{

                $this->error($verify->getError());



            }

        }else{

            $id = I('get.sp_id');

            $city = M('shangpin')->field(true)->find($id);/* 获取数据 */

            if(false === $city){

                $this->error('获取商品信息错误');

            }

            $this->meta_title = '编辑商品';

            $this->assign('_city', $city);

            $this->display();

        }



    }
    //删除商品

    public function del(){

        $id = array_unique((array)I('sp_id',0));

        $map = array('sp_id' => array('in', $id) );

        $shu = M('ptkucun')->where($map)->find();
        $inst = M('instorage')->where($map)->find();
        $outst = M('outstorage')->where($map)->find();
        if($shu!=0 || $inst || $outst){
            $this->error('该商品已有库存或出入库记录！删除商品失败！');
        }else{
            if(M('shangpin')->where($map)->delete()){

                $this->success('删除成功！');

            }else{

                $this->error('删除失败！');

            }
        }


    }
}