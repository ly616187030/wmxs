<?php/** * 城市管理数据库 * 数据库表名 wm_city * city_id      城市ID * city_name    城市名称 * help_code    快捷字母 * sort_order   排序 * flag         标记(1启用，0未启用) */namespace Administration\Controller;use Admin\Controller\AdminController;use Administration\Controller\MenuPctController;class CityController extends AdminController{	//初始化    public function index(){		$User = M('city'); // 实例化User对象		$count      = $User->count();// 查询满足要求的总记录数		$Page       = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)		$Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');		$Page->setConfig('first','首页');		$Page->setConfig('prev','上一页');		$Page->setConfig('next','下一页');		$Page->setConfig('last','末页');		$Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');		$Page->lastSuffix = false;//分页最后的总页数不显示		$show       = $Page->show();// 分页显示输出		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性		$list = $User->order('sort_order')->limit($Page->firstRow.','.$Page->listRows)->select();		$this->assign('list',$list);// 赋值数据集		$this->assign('page',$show);// 赋值分页输出		$this->meta_title = '城市管理';		$this->display(); // 输出模板	}		//标记	public function toogleHide($id,$value = 1){		$this->editRow('city', array('flag'=>$value), array('city_id'=>$id));	}		//增加城市	public function add(){		$this->meta_title = '增加城市';		if(IS_POST){			$rules = array(				array('city_code','require','城市不能为空！'),			);			$User = M("city");  //动态验证			if ($User->validate($rules)->create()){				if($User->add()){					$this->success('新增成功', U('index'));				}else{					$this->error('新增失败');				}			}else{				$this->error($User->getError());			}		}else{			//实例化添加商圈模版			$province=A('MenuPct')->getProvince(false);			$this->assign('_province',$province);			$this->display();		}	}		//删除城市	public function del(){		$id = array_unique((array)I('city_id',0));		$map = array('city_id' => array('in', $id) );		if(M('city')->where($map)->delete()){			$this->success('删除成功');		}else{			$this->error('删除失败！');		}	}		//编辑城市	public function edit(){		$this->meta_title = '编辑城市';		if(IS_POST){			$rules1 = array(				array('city_code','require','城市不能为空！'),			);			$city = M("city");  //动态验证			if ($city->validate($rules1)->create()){				if($city->save()!== false){					$this->success('更新成功','admin.php?s=/City/index.html');				}else{					$this->error('更新失败');				}			}else{				$this->error($city->getError());			}		}else{			$id = I('get.city_id');			$city = M('city')->field(true)->find($id);/* 获取数据 */			if(false === $city){				$this->error('获取后台城市信息错误');			}			//实例化添加商圈模版			$province=A('MenuPct')->getProvince(false);			$this->assign('_province',$province);			$this->assign('list', $city);			$this->display();		}	}}