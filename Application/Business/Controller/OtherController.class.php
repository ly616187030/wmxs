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


class OtherController extends AdminController{

    //初始化

    public function index(){

        $weixin = M('other_weixin'); // 实例化User对象

        $where['founder_id']=FID;
        $count      = $weixin->where($where)->count();// 查询满足要求的总记录数
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
        $list = $weixin->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->meta_title = '特价专区管理';
        $this->display(); // 输出模板

//		$list = $jokes->alias('s')->join('LEFT JOIN wm_city AS c ON s.city_id=c.city_id')->field('s.*,c.city_name')->order('sort_order')->select();
    }



    //标记
    public function toogleHide($id,$value = 1){
        $this->editRow('other_weixin', array('flag'=>$value), array('qt_id'=>$id));
    }



    //增加店铺分类
    public function add(){

        $this->meta_title = '增加特价专区';
        if(IS_POST){
            $rules = array(
                array('qt_lunbo_pic','require','轮播图片不能为空！'),
                array('qt_login_pic','require','登陆页面logo不能为空！'),
                array('qt_kefu_tel','require','客服电话不能为空！')
            );
            $weixin = M("other_weixin");//动态验证
            if ($weixin->validate($rules)->create()){
                $weixin->founder_id=FID;
                if($weixin->add()){
                    $this->success('新增成功', U('index'));
                }
                else{
                    $this->error('新增失败');
                }
            }else{
                $this->error($weixin->getError());
            }
        }else{
            $this->display();
        }
    }



    //删除店铺分类

    public function del(){

        $id = array_unique((array)I('qt_id',0));
        $map = array('qt_id' => array('in', $id) );
        if(M('other_weixin')->where($map)->delete()){
            $this->success('删除成功');
        }else{
            $this->error('删除失败！');
        }
    }



    //编辑店铺分类

    public function edit(){
        $weixin = M("other_weixin");
        if(IS_POST){
            $rules1 = array(
                array('qt_lunbo_pic','require','轮播图片不能为空！'),
                array('qt_login_pic','require','登陆页面logo不能为空！'),
                array('qt_kefu_tel','require','客服电话不能为空！')
            );
            //动态验证
            if ($weixin->validate($rules1)->create()){
                $weixin->founder_id=FID;
                if($weixin->save()!== false){
                    $this->success('更新成功',U('index'));
                }else{
                    $this->error('修改失败');
                }
            }else{
                $this->error($weixin->getError());
            }
        }else{
            $id = I('get.qt_id');
            $data = $weixin->where('qt_id='.$id)->find();/* 获取数据 */
            $this->assign('_city', $data);
            $this->meta_title = '编辑特价专区';
            $this->display();
        }
    }
}