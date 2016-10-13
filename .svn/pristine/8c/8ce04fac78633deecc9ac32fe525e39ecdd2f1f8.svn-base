<?php
namespace Storemanagement\Controller;
use Admin\Controller\AdminController;
class CanmingController extends AdminController{


    public function index(){

		$storeid = I('storeid',0,'intval');
		$sname = I('storename');
		if(empty($storeid) && is_company()){
			redirect(U('Business/shanghu/index'));
		}else{
			$uid = session('user_auth.uid');
			if(empty($storeid)){
				$res = M('Store')->where('uid = '.$uid)->field('store_id,store_name')->find();
				$storeid = $res['store_id'];
				//$sname = $res['store_name'];
			}
		}
			$User = M('canming'); // 实例化User对象
		if($storeid){
			$count = $User->where("store_id=".$storeid)->count();// 查询满足要求的总记录数
		}
			$Page = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
			$Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
	        $Page->setConfig('first','首页');
	        $Page->setConfig('prev','上一页');
	        $Page->setConfig('next','下一页');
	        $Page->setConfig('last','末页');
	        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
	        $Page->lastSuffix = false;//分页最后的总页数不显示
			$show = $Page->show();// 分页显示输出
		if($storeid){

			$list1 = $User
				->alias('s')
				->join('LEFT JOIN __CANPIN__ AS c ON s.canping_id=c.canpin_id')
				->join('LEFT JOIN __DANGKOU__ AS d ON s.dangkou_id=d.id')
				->field('s.*,c.canpin_name,d.*')
				->where("s.store_id=".$storeid)

				->order('cm_id')
				->select();
		}
			//dump($list1);
			$id = 1;
			$list = array();
			foreach($list1 as $k=>$val){
				$val["id"]=$id;
				$val["store_name"]=$sname;
				array_push($list,$val);
				$id++;
		}
        $dangkou = M('dangkou')->where('uid = '.$uid)->select();
		$this->assign('data', $list);
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('store_id',$storeid);
		$this->assign('storename',$sname);
        $this->assign('dangkou',$dangkou);
			$this->meta_title = '菜品管理';
		$this->display(); // 输出模板
    }

    public function add(){
		C('TOKEN_ON',false);
	        if(IS_POST){
				$timetype = I('saletime_type');
				if($timetype == 1) {
					unset($_POST['sale_time']);
					unset($_POST['week']);
				}
				if($timetype == 2){
					$salet = I('sale_time');
					$ts = '';
					foreach($salet as $val){
						$s = '';
						foreach($val as $v){
							$s.= $v==''?'00':(strlen($v)==1?'0'.$v:$v);
						}
						$ts.=$s.'|';
					}
				}
				$week = I('week');
				if(!empty($week)){
					$w = implode(',',$week);
				}
	            $m = D('Canming');
	            if($m->create()){
					$timetype == 2 && $m->sale_time = rtrim($ts,'|');
					!empty($week) && $m->week = $w;
	                $m->ctime = time();
	                $m->flag = 1;
                    $m_add = $m->add();
	                if($m_add > 0){
                        action_log('add_action','Canming',$m_add,is_login());
	                    $this->success('新增成功',U('Canming/index'));
	                }else{
	                    $this->error('新增失败');
	                }
	            }else{
	                $this->error($m->getError());
	            }
	        }else{
				$storeid = I('storeid',0,'intval');
				$sname = I('storename');
				if(empty($storeid) && is_company()){
					redirect(U('Business/shanghu/index'));
				}else{
					$uid = session('user_auth.uid');
					if(empty($storeid)){
						$res = M('Store')->where('uid = '.$uid)->field('store_id,store_name')->find();
						$storeid = $res['store_id'];
						//$sname = $res['store_name'];
					}
				}
	        	$user = M('Canpin');
				$array_data = array();
				$array_data["store_id"] = $storeid;
				$array_data["store_name"] = $sname;
				$this->assign('list',$array_data);
				if($storeid){
					$clist = $user->where('store_id ='.$storeid)->select();
				}
	            $this->assign('clist',$clist);
				$Dangkou = M('Dangkou');
				$dangkou=$Dangkou->where('uid='.is_login())->select();
				$this->assign('dangkou',$dangkou);
				$this->meta_title = '新增餐品名称';
	        }
		$this->display();
    }

    public function edit(){

		C('TOKEN_ON',false);
			$m = D('Canming');
			$sname= I('storename');
	        $id = I('cm_id');
	        if(IS_POST){
				$timetype = I('saletime_type');
				if($timetype == 1) {
					unset($_POST['sale_time']);
					unset($_POST['week']);
				}
				if($timetype == 2){
					$salet = I('sale_time');
					$ts = '';
					foreach($salet as $val){
						$s = '';
						foreach($val as $v){
							$s.= $v==''?'00':(strlen($v)==1?'0'.$v:$v);
						}
						$ts.=$s.'|';
					}
				}
				$week = I('week');
				if(!empty($week)){
					$w = implode(',',$week);
				}
				if($m->create()){
					$timetype == 2 && $m->sale_time = rtrim($ts,'|');
					!empty($week) && $m->week = $w;
					$m->ctime = time();
					$m->flag = 1;
	                $arr = $m->where('cm_id = '.$id)->save();

	                if($arr !== false){
                        action_log('edit_action','Canming',$id,is_login());
	                    $this->success('更新数据成功',U('Canming/index'));
	                }else{
	                    $this->error('数据没有更新=>更新失败');
	                }
	            }else{
	                $this->error($m->getError());
	            }
	        }else{
	            $list = $m->find($id);
				$array_data = array();
				$array_data["store_id"] = $list['store_id'];
				$array_data["store_name"] = '';
				$this->assign('list',$array_data);
	           	$cp = M('Canpin');
	            $clist = $cp->where('store_id = '.$list['store_id'])->select();
	            $this->assign('clist',$clist);
				$Dangkou = M('Dangkou');
				$dangkou=$Dangkou->where('uid='.is_login())->select();
				$this->assign('dangkou',$dangkou);
	            $this->assign('data',$list);
				$this->assign('storename',$sname);
				//dump($clist);
				$this->meta_title = "编辑餐品名称";
	        }
		$this->display();
    }

    public function del(){

        $id = I('cm_id');
        $m = M('Canming');
        if(is_array($id)){
            $where = 'cm_id in ('.implode(',',$id).')';
        }else{
            $where = 'cm_id ='.$id;
        }
        $list = $m->where($where)->delete();
        if($list>0){
            action_log('delete_action','Canming',is_array($cm_id)?implode(',', $cm_id):$cm_id,is_login());
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }

    }
    public function updateDangkou($cm_id,$dk){
        if(!empty($cm_id) && !empty($dk)){
            $map['cm_id'] = array('in',$cm_id);
            $data['dangkou_id'] =$dk;
            $res = M('canming')->where($map)->save($data);
            if($res !==false){
                action_log('edit_action','Canming',is_array($cm_id)?implode(',', $cm_id):$cm_id,is_login());
                $this->ajaxReturn('success');
            }
        }
        $this->ajaxReturn(null);
    }
    public function setFlag(){



        $id = I('cm_id');

        $flag = I('get.flag');

        $m = M('Canming');

        if(is_array($id)){

            $where = 'cm_id in ('.implode(',',$id).')';

            if($flag == 1){

                $arr = $m->where($where)->setField('flag',1);

                if($arr>0){
                    action_log('enable_action','Canming',is_array($id)?implode(',', $id):$id,is_login());
                    $this->success("启用成功");

                }else{

                    $this->error("启用失败");

                }

            }elseif($flag == 0){

                $arr = $m->where($where)->setField('flag',0);

                if($arr>0){
                    action_log('disable_action','Canming',is_array($id)?implode(',', $id):$id,is_login());
                    $this->success("禁用成功");

                }else{

                    $this->error("禁用失败");

                }

            }

        }else{

            $where = 'cm_id ='.$id;

            if($flag != 0){

                $arr = $m->where($where)->setField('flag',0);

                if($arr>0){
                    action_log('disable_action','Canming',$id,is_login());
                    $this->success("禁用成功");

                }else{

                    $this->error("禁用失败");

                }

            }else{

                $arr = $m->where($where)->setField('flag',1);

                if($arr>0){
                    action_log('enable_action','Canming',$id,is_login());
                    $this->success("启用成功");

                }else{

                    $this->error("启用失败");

                }
            }
        }
    }
	public function uploadImg(){
		$cm_id =I('cm_id');
		$canping_logo_id=I('canping_logo_id');
		$m=M('canming');
		$res=session('cm_id');
		if(is_array($res)){
			$where = 'cm_id in ('.implode(',',$res).')';
		}
		if(IS_POST){
			if(!empty($cm_id)){
				session('cm_id',$cm_id);
				$this->success('',U(''));
			}elseif(!empty($canping_logo_id)){
					if(is_array($canping_logo_id)){
						for($i=0;$i<count($res);$i++){
							$re=$m->where('cm_id='.$res[$i])->setField('big_img',$canping_logo_id[$i]);
						}
						if($re>0){
                            action_log('edit_action','Canming',$cm_id,is_login());
							$this->success('上传成功',U('canming/index'));
						}else{
							$this->error('上传失败');
						}
					}
			}else{
				session('cm_id',null);
				$this->error('选择至少一个餐品或图片');
			}
		}
		$canming=$m->where($where)->field('cm_name')->select();
		$this->assign('canming',$canming);
		$this->display();
	}
	function unescape($str) {

    $str = rawurldecode($str);

    preg_match_all("/(?:%u.{4})|&#x.{4};|&#\d+;|.+/U",$str,$r);

    $ar = $r[0];

    //print_r($ar);

    foreach($ar as $k=>$v) {

        if(substr($v,0,2) == "%u"){

            $ar[$k] = iconv("UCS-2BE","UTF-8",pack("H4",substr($v,-4)));

  	}

        elseif(substr($v,0,3) == "&#x"){

            $ar[$k] = iconv("UCS-2BE","UTF-8",pack("H4",substr($v,3,-1)));

  	}

        elseif(substr($v,0,2) == "&#") {

             

            $ar[$k] = iconv("UCS-2BE","UTF-8",pack("n",substr($v,2,-1)));

        }

    }

    return join("",$ar);

	}
    /**

     * 汉字转拼音

     */
    public function cu2py()

    {

        $str = I('get.keys');

        if (!empty($str)) {

            $str = \Vendor\CUtf8_PY::encode($str);

            $this->ajaxReturn(strtoupper($str));

        }

    }

}

?>

