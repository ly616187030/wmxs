<?php

/**

 * 店铺分类数据库

 * 数据库表名 wm_store_category

 * store_c_id      店铺分类ID

 * store_c_name    店铺分类名称

 * city_id         所属城市ID

 * sort_order      排序

 * flag            标记(1启用，0未启用)

 */
namespace Business\Controller;

use Admin\Controller\AdminController;


class SpecialareaController extends AdminController{

    //初始化

    public function index(){

        $specialarea = M('specialarea'); // 实例化User对象

        $where['founder_id']=FID;
        $count      = $specialarea->where($where)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
        $Page->setConfig('first','首页');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('last','末页');
        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $Page->lastSuffix = false;//分页最后的总页数不显示
        $show       = $Page->show();// 分页显示输出

        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $specialarea->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($list as $k=>$v){
            $store=M('store');
            $condition['store_id']=array('in',$v['store_id']);
            $shangjia=$store->where($condition)->field('store_name')->select();
            $list[$k]['store']=$shangjia;
        }
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->meta_title = '特价专区管理';
        $this->display(); // 输出模板

//		$list = $jokes->alias('s')->join('LEFT JOIN wm_city AS c ON s.city_id=c.city_id')->field('s.*,c.city_name')->order('sort_order')->select();
    }



    //标记
    public function toogleHide($id,$value = 1){
        $this->editRow('specialarea', array('flag'=>$value), array('tj_id'=>$id));
    }



    //增加店铺分类
    public function add(){

        $this->meta_title = '增加特价专区';
        if(IS_POST){
            $rules = array(
                array('tj_name','require','专区名称不能为空！'),
            );
            $specialarea = M("specialarea");//动态验证
            if ($specialarea->validate($rules)->create()){
                $specialarea->founder_id=FID;
                $specialarea->store_id=implode(',',I('store'));
                if($specialarea->add()){
                    $this->success('新增成功', U('index'));
                }
                else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($specialarea->getError());
            }
        }else{
            $store=M('store');
            $fid['founder_id'] = FID;
            $info=$store->where($fid)->select();
            $this->assign('info',$info);
            $this->display();
        }
    }



    //删除店铺分类

    public function del(){

        $id = array_unique((array)I('tj_id',0));
        $map = array('tj_id' => array('in', $id) );
        if(M('specialarea')->where($map)->delete()){
            $this->success('删除成功');
        }else{
            $this->error('删除失败！');
        }
    }



    //编辑店铺分类

    public function edit(){
        $specialarea = M("specialarea");
        if(IS_POST){
            $rules1 = array(
                array('tj_name','require','专区名称不能为空！'),
            );
              //动态验证
            if ($specialarea->validate($rules1)->create()){
                $specialarea->founder_id=FID;
                $specialarea->store_id=implode(',',I('store'));
                if($specialarea->save()!== false){
                    $this->success('更新成功',U('index'));
                }else{
                    $this->error('修改失败');
                }
            }else{
                $this->error($specialarea->getError());
            }
        }else{
            $id = I('get.tj_id');
            $data = $specialarea->where('tj_id='.$id)->find();/* 获取数据 */
            $this->assign('_city', $data);
            $store=M('store');
            $fid['founder_id'] = FID;
            $info=$store->where($fid)->select();
            foreach($info as $k=>$v){
                if(in_array($v['store_id'],explode(',',$data['store_id']))){
                    $info[$k]['checked']=1;
                }else{
                    $info[$k]['checked']=0;
                }
            }
            $this->assign('info',$info);
            $this->meta_title = '编辑特价专区';
            $this->display();
        }
    }
}