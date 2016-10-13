<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-11-24
 * Time: 13:59
 */
namespace Administration\Controller;

use Admin\Controller\AdminController;
class RangeController extends AdminController{

    public function index(){
        $range = M('range');
        $list = $range->alias('A')->join('LEFT JOIN '.C('DB_PREFIX').'address_province'.' AS B ON A.province = B.code')
                                  ->join('LEFT JOIN '.C('DB_PREFIX').'address_city'.' AS C ON A.city = C.code')
                                  ->join('LEFT JOIN '.C('DB_PREFIX').'address_town'.' AS D ON A.area = D.code')
                                  ->field('A.*,B.name namea,C.name nameb,D.name namec')->select();
        $this->assign('list',$list);
        $this->display();
    }
    public function add(){
        if(IS_POST){
            $array = array(
              array('range_name','require','站点名称不能为空！'),
              array('charge','require','负责人名称不能为空！'),
              array('phone','require','联系电话不能为空！'),
              array('phone','/^1[3|4|5|8][0-9]\d{4,8}$/','手机号码格式不正确！')
            );
            $range = M('range');
            if($range->validate($array)->create()){
                 if($range->add()){
                     $this->success('新增成功！',U('index'));
                 }else{
                     $this->error('新增失败！');
                 }
            }else{
                $this->error($range->getError());
            }
        }else{
            //城市三级联动
            $province=A('MenuPct')->getProvince(false);
            $this->assign('_province', $province);

            $this->display();
        }
    }
    public function del(){
        $map['range_id'] = I('id');
        if(M('range')->where($map)->delete()){
            $this->success('删除成功！');
        }else{
            $this->error('删除失败！');
        }
    }

    public function edit(){
        $id = I('id');
        if(IS_POST){
            $array = array(
                array('range_name','require','站点名称不能为空！'),
                array('charge','require','负责人名称不能为空！'),
                array('phone','require','联系电话不能为空！'),
                array('phone','/^1[3|4|5|8][0-9]\d{4,8}$/','手机号码格式不正确！')
            );
            $range = M('range');
            $where['range_id'] = $id;
            if($range->validate($array)->create()){
                if($range->save()){
                    $this->success('编辑成功！',U('index'));
                }else{
                    $this->error('编辑失败！');
                }
            }else{
                $this->error($range->getError());
            }
        }else{
            $range = M('range');
            $list = $range->where('range_id ='.$id)->find();
            $this->assign('list',$list);
            //城市三级联动
            $province=A('MenuPct')->getProvince(false);
            $this->assign('_province',$province);

            $this->display();
        }

    }
}