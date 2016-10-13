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

class StorecategoryController extends AdminController{

	//初始化

    public function index(){

		$User = M('store_category'); // 实例化User对象
		if(is_administrator()){
			$where['founder_id'] = 0;
		}else{
			$where['founder_id'] = FID;
		}

		$count      = $User->where($where)->count();// 查询满足要求的总记录数
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
		$list = $User->where($where)->order('sort_order')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->meta_title = '店铺分类管理';
		$this->display(); // 输出模板

//		$list = $jokes->alias('s')->join('LEFT JOIN wm_city AS c ON s.city_id=c.city_id')->field('s.*,c.city_name')->order('sort_order')->select();

	}

	

	//标记

	public function toogleHide($id,$value = 1){
		$this->editRow('store_category', array('flag'=>$value), array('store_c_id'=>$id));
    }

	

	//增加店铺分类

	public function add(){

		$this->meta_title = '增加店铺分类';
		if(IS_POST){
			$rules = array(
			array('store_c_name','require','店铺分类名称不能为空！'),
			//array('store_c_name','','店铺分类名称已经存在！',0,'unique',1),
			);
			$User = M("store_category");  //动态验证
			if ($User->validate($rules)->create()){
				$User->founder_id=FID;
                $add_id = $User->add();
                    if($add_id > 0){
                        action_log('add_action','store_category',$add_id,is_login());
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

	

	//删除店铺分类

	public function del(){

		$id = array_unique((array)I('store_c_id',0));
        $map = array('store_c_id' => array('in', $id) );
        if(M('store_category')->where($map)->delete()){
            action_log('add_action','store_category',is_array($id)?implode(',', $id):$id,is_login());
            $this->success('删除成功');
        }else{
            $this->error('删除失败！');
        }
	}

	

	//编辑店铺分类

	public function edit(){



		if(IS_POST){
			$rules1 = array(
			array('store_c_name','require','店铺分类名称不能为空！'),
			//array('store_c_name','','店铺分类名称已经存在！',0,'unique',2),
			);
			$verify = M("store_category");  //动态验证
			if ($verify->validate($rules1)->create()){
				$verify->founder_id=FID;
               		if($verify->save()!== false){
                    	$this->success('更新成功',U('index'));
                	}else{
						$this->error('修改失败');
            	}
        	}else{
            	$this->error($verify->getError());
			}
		}else{
			$id = I('get.store_c_id');
			$city = M('store_category')->field(true)->find($id);/* 获取数据 */
			if(false === $city){
				$this->error('获取后台城市信息错误');
			}
			$this->meta_title = '编辑店铺分类';
			$this->assign('_city', $city);
			$this->display();
		}
	}
}