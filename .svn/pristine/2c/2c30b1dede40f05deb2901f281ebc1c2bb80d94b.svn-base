<?php
/**
 * Created by PhpStorm.
 * User: WXM
 * Date: 2015/9/21
 * Time: 11:16
 */
namespace Platform\Controller;
use Admin\Controller\AdminController;
class ShopinController extends AdminController{
    public function index(){

        $User = M('instorage'); // 实例化User对象

        $count      = $User->count();// 查询满足要求的总记录数

        $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');

        $Page->setConfig('first','首页');

        $Page->setConfig('prev','上一页');

        $Page->setConfig('next','下一页');

        $Page->setConfig('last','末页');

        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');

        $Page->lastSuffix = false;//分页最后的总页数不显示



        $show       = $Page->show();// 分页显示输出

        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性

        $list = M('instorage')
            ->alias('a')->join('LEFT JOIN wm_storage AS b ON a.storage_id = b.storage_id')
            ->field("a.*,b.storage_name")
            ->order('a.in_id asc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

        $this->assign('list',$list);// 赋值数据集

        $this->assign('page',$show);// 赋值分页输出

        $this->meta_title = '入库管理';

        $this->display();
    }
    public function add(){
        $cangku = M('storage');
        $cx_cangku = $cangku->select();
        $this->assign('ck',$cx_cangku);
        $sp = M('shangpin');
        $shangpin['sp_status']=1;
        $cx_sp = $sp ->where($shangpin)-> select();
        $this->assign('sp',$cx_sp);



            $this->meta_title = '增加入库';

            if(IS_POST){
            	
                $rules = array(

                    array('person','require','经办人不能为空！'),
                    array('quantity','/^[1-9]\d*$/','数量须大于0!',1)

                );

                $User = M("instorage");  //动态验证
                $date = time();

                if ($User->validate($rules)->create()){
                        $User->in_time = $date;
						$add = $User->add();
				//接收入库的数据用来操作平台库存表
				$data['storage_id'] = I('storage_id');
				$data['sp_id'] = I('sp_id');
				$data['quantity'] = I('quantity');
				
				$where['storage_id'] = $data['storage_id'];
				$where['sp_id'] = $data['sp_id'];
				$kucun = M('ptkucun')->where($where)->find();
				if($kucun){
					$data['quantity'] = $kucun['quantity']+$data['quantity'];
					$res = M('ptkucun')->where($where)->save($data);
				}else{
					$addkucun = M('ptkucun')->add($data);
				}
				
                    if($add){
						if($addkucun||$res){
							$this->success('新增成功', U('index'));
						}
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

    public function del(){
        $id = array_unique((array)I('in_id',0));
        $in = array('in_id'=> array('in',$id));
		$data['storage_id']=I('get.st_id');
		$data['sp_id']=I('get.sp_id');
		$data['quantity']=I('get.quantity');
		$where['storage_id'] = $data['storage_id'];
		$where['sp_id'] = $data['sp_id'];
		$kucun = M('ptkucun')->where($where)->find();
		$data['quantity'] = $kucun['quantity']-$data['quantity'];
		if(M('ptkucun')->where($where)->save($data)){
			if(M('instorage')->where($in)->delete()){
            $this->success('删除成功',U('index'));
        }
		}
        else{
            $this->error('删除失败');
        }
    }
    public function setFlag(){



        $id = I('in_id');

        $flag = I('get.status');

        $m = M('instorage');

        if(is_array($id)){

            $where = 'in_id in ('.implode(',',$id).')';

            if($flag == 1){

                $arr = $m->where($where)->setField('status',1);

                if($arr>0){

                    $this->success("启用成功");

                }else{

                    $this->error("启用失败");

                }

            }elseif($flag == 0){

                $arr = $m->where($where)->setField('status',0);

                if($arr>0){

                    $this->success("禁用成功");

                }else{

                    $this->error("禁用失败");

                }

            }

        }else{

            $where = 'in_id ='.$id;

            if($flag != 0){

                $arr = $m->where($where)->setField('status',0);

                if($arr>0){

                    $this->success("禁用成功");

                }else{

                    $this->error("禁用失败");

                }

            }else{

                $arr = $m->where($where)->setField('status',1);

                if($arr>0){

                    $this->success("启用成功");

                }else{

                    $this->error("启用失败");

                }

            }
        }
    }
    //编辑店铺分类

    public function edit(){



        if(IS_POST){

            $rules1 = array(

                array('person','require','经办人名称不能为空！'),
                array('quantity','require','数量不能为空！'),
                array('quantity','/^[1-9]\d*$/','数量须大于0!',1),
                array('guanggao_name','require','经办人名称不能为空！')

            );

            $verify = M("instorage");  //动态验证
            $id = I('in_id');
			$jian['quantity']=I('post.quantityjian');
			$jia['quantity'] = I('post.quantity');
            $where = 'in_id='.$id;
			$map['storage_id'] = I('post.storage_id');
			$map['sp_id'] = I('post.sp_id');
            $date = time();
            if ($verify->validate($rules1)->create()){
            	$verify->where($where)->in_time = $date;
            	$kucunjian = M('ptkucun')->where($map)->find();
				$jian['quantity'] = $kucunjian['quantity']-$jian['quantity'];
				$jia['quantity'] = $jian['quantity']+$jia['quantity'];
				$re = M('ptkucun')->where($map)->save($jia);
					if($verify->where($where)->save()!== false){
						if($re){
						$this->success('更新成功',U('index'));
						}
                }else{

                    $this->error('修改失败');

				}
                

            }else{

                $this->error($verify->getError());



            }

        }else{
            $id = I('get.in_id');
            $cangku = M('storage');
            $cx_cangku = $cangku->select();
            $this->assign('ck',$cx_cangku);
            $sp = M('shangpin');
            $shangpin['sp_status']=1;
            $cx_sp = $sp ->where($shangpin)-> select();
            $this->assign('sp',$cx_sp);
            $city = M('instorage')
                ->alias('a')->join('LEFT JOIN wm_storage AS b ON a.storage_id = b.storage_id')
                ->where("in_id = $id")
                ->field("a.*,b.storage_name")
                ->select(); /* 获取数据 */

            if(false === $city){

                $this->error('获取入库信息错误');

            }

            $this->meta_title = '编辑入库';
			$data['quantity'] = I('get.quantity');
			$this->assign('quantity',$data['quantity']);
            $this->assign('_city', $city);

            $this->display();

        }



    }
}