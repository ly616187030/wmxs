<?php
namespace Business\Controller;
use Admin\Controller\AdminController;
class PromotionController extends AdminController {


    public function index(){
        $storeid = I('storeid',0,'intval');
        $sname = I('storename');
        if(empty($storeid) && is_company()){
            redirect(U('shanghu/index'));
        }else{
            $uid = session('user_auth.uid');
            if(empty($storeid)){
                $res = M('Store')->where('uid = '.$uid)->field('store_id,store_name')->find();
                $storeid = $res['store_id'];
            }
        }
        $data['store_id'] = $storeid;
        $data['founder_id'] = FID;
        $data['type'] = 1;
        $cuxiao = M('cuxiao')->where($data)->order('promotion_id desc')->select();
        foreach($cuxiao as $v => $n){
             $map['promotion_id'] = $n['first'];
             $aaa = M('cuxiao')->where($map)->field('promotion_name')->select();
             $cuxiao[$v]['huo'] = $aaa;
        }



        $this->assign('list',$cuxiao);
        $this->assign('_storeid',$storeid);
        $this->assign('storename',$sname);
        $this->display();
    }
    public function add(){
        if(IS_POST) {
                    $array = array(
                        array('promotion_name', 'require', '促销标题不能为空！'),
                        //array('promotion_name', '', '促销标题已存在！', 0, 'unique', 1),
                        array('promotion_money', 'require', '订单总金额限制不能为空！'),
                    );
                    $cuxiao = M('cuxiao');
                if ($cuxiao->validate($array)->create()) {
                     if(I('time_quantum') == ""){
                         $cuxiao->time_quantum = 0;
                     }else{
                         $cuxiao->time_quantum = implode(',',I('time_quantum'));
                     }
                    $cuxiao->effective_date = strtotime(I('effective_date'));
                    $cuxiao->termination_date = strtotime(I('termination_date'));
                    $cuxiao->founder_id = FID;
                      if($cuxiao->add()){
                          $this->success('添加成功',U('index',array('storeid'=>$_POST['store_id'])));
                      }else{
                          $this->error('添加失败');
                      }
                }else{
                    $this->error($cuxiao->getError());
                }
        }

        $storeid = I('storeid',0,'intval');
        $sname = I('storename');
        if(empty($storeid) && is_company()){
            redirect(U('shanghu/index'));
        }else{
            $uid = session('user_auth.uid');
            if(empty($storeid)){
                $res = M('Store')->where('uid = '.$uid)->field('store_id,store_name')->find();
                $storeid = $res['store_id'];
            }
        }

        $this->assign('_storeid',$storeid);
        $this->assign('storename',$sname);
        $this->display();
    }
    public function edit(){
        $where['promotion_id'] = I('promotion_id');
        if(IS_POST){
            $array = array(
                array('promotion_name', 'require', '促销标题不能为空！'),
                array('promotion_money', 'require', '订单总金额限制不能为空！'),
            );
            $time_quantum = implode(',',I('post.time_quantum'));
            $cuxiao = M('cuxiao');
            if ($cuxiao->validate($array)->create()) {
                if(I('time_quantum') == ""){
                    $cuxiao->time_quantum = 0;
                }else{
                    $cuxiao->time_quantum = implode(',',I('time_quantum'));
                }
                $cuxiao->effective_date = strtotime(I('effective_date'));
                $cuxiao->termination_date = strtotime(I('termination_date'));
                if($cuxiao->where($where)->save()){
                    $this->success('编辑成功',U('index',array('storeid'=>$_POST['store_id'])));
                }else{
                    $this->error('编辑失败');
                }
            }else{
                $this->error($cuxiao->getError());
            }
        }else{
            $storeid = I('storeid',0,'intval');
            $sname = I('storename');
            if(empty($storeid) && is_company()){
                redirect(U('shanghu/index'));
            }else{
                $uid = session('user_auth.uid');
                if(empty($storeid)){
                    $res = M('Store')->where('uid = '.$uid)->field('store_id,store_name')->find();
                    $storeid = $res['store_id'];
                }
            }

            $cuxiao = M('cuxiao')->where($where)->find();
            if($cuxiao['promotion_time'] == 1){
                $cuxiao['aaa'] = explode(',',$cuxiao['time_quantum']);
            }else{
                $a = explode(',',$cuxiao['time_quantum']);
                array_push($cuxiao,$a);
            }
            $map['store_id'] = $storeid;
            $map['type'] = 1;
            $map['promotion_id'] = array(array('neq',I('promotion_id')),array('neq',I('first')),'and');
            $huo = M('cuxiao')->where($map)->field('promotion_name,promotion_id')->select();
            $first['store_id'] = $storeid;
            $first['promotion_id'] = I('first');
            $lian =  M('cuxiao')->where($first)->field('promotion_name,promotion_id')->find();

            $this->assign('huo',$huo);
            $this->assign('lian',$lian);
            $this->assign('id',$storeid);

            $this->assign('_storeid',$storeid);
            $this->assign('storename',$sname);
            $this->assign('list',$cuxiao);
            $this->assign('aaa',json_encode($cuxiao['aaa']));
            $this->display();
        }

    }
   public function del(){
       $where['promotion_id'] = I('promotion_id');
       if(M('cuxiao')->where($where)->delete()){
           $this->success('删除成功');
       }else{
           $this->error('删除失败');
       }
   }

    //启用禁用
    public function enable($id,$state = 1){
        $this->editRow('cuxiao', array('state'=>$state), array('promotion_id'=>$id));
    }

    //复制商家优惠
    public function copy_store(){
        $list = I('post.list_post');
        $yh_id = I('post.yh_id');
        $yh_lx = I('post.yh_lx');
        if($yh_id >0){
            $d1['promotion_id'] = $yh_id;
            $ret_copy = $User = M('cuxiao')->where($d1)->find();
            $j = 0;
            for ($i = 0; $i <count($list); $i++){
                $d2['store_id'] = $list[$i];
                $d2['first_order'] = $ret_copy['first_order'];
                $ret_data = M('cuxiao')->where($d2)->find();
                if($ret_data == null){
                    $data2[$j]['store_id'] = $list[$i];
                    $data2[$j]['promotion_name'] = $ret_copy['promotion_name'];
                    $data2[$j]['promotion_money'] = $ret_copy['promotion_money'];
                    $data2[$j]['promotion_time'] = $ret_copy['promotion_time'];
                    $data2[$j]['time_quantum'] = $ret_copy['time_quantum'];
                    $data2[$j]['promotion_type'] = $ret_copy['promotion_type'];
                    $data2[$j]['present_name'] = $ret_copy['present_name'];
                    $data2[$j]['present_numbe'] = $ret_copy['present_numbe'];
                    $data2[$j]['kucun_nuber'] = $ret_copy['kucun_nuber'];
                    $data2[$j]['discount'] = $ret_copy['discount'];
                    $data2[$j]['reduction'] = $ret_copy['reduction'];
                    $data2[$j]['vertical'] = $ret_copy['vertical'];
                    $data2[$j]['shou_jian'] = $ret_copy['shou_jian'];
                    $data2[$j]['jian'] = $ret_copy['jian'];
                    $data2[$j]['effective_date'] = $ret_copy['effective_date'];
                    $data2[$j]['termination_date'] = $ret_copy['termination_date'];
                    $data2[$j]['promotional_labels'] = $ret_copy['promotional_labels'];
                    $data2[$j]['payment'] = $ret_copy['payment'];
                    $data2[$j]['first_order'] = $ret_copy['first_order'];
                    $data2[$j]['state'] = $ret_copy['state'];
                    $data2[$j]['type'] = $ret_copy['type'];
                    $j++;
                }else{
                    $data1 = array();
                    $data1['store_id'] = $list[$i];
                    $data1['promotion_name'] = $ret_copy['promotion_name'];
                    $data1['promotion_money'] = $ret_copy['promotion_money'];
                    $data1['promotion_time'] = $ret_copy['promotion_time'];
                    $data1['time_quantum'] = $ret_copy['time_quantum'];
                    $data1['promotion_type'] = $ret_copy['promotion_type'];
                    $data1['present_name'] = $ret_copy['present_name'];
                    $data1['present_numbe'] = $ret_copy['present_numbe'];
                    $data1['kucun_nuber'] = $ret_copy['kucun_nuber'];
                    $data1['discount'] = $ret_copy['discount'];
                    $data1['reduction'] = $ret_copy['reduction'];
                    $data1['vertical'] = $ret_copy['vertical'];
                    $data1['shou_jian'] = $ret_copy['shou_jian'];
                    $data1['jian'] = $ret_copy['jian'];
                    $data1['effective_date'] = $ret_copy['effective_date'];
                    $data1['termination_date'] = $ret_copy['termination_date'];
                    $data1['promotional_labels'] = $ret_copy['promotional_labels'];
                    $data1['payment'] = $ret_copy['payment'];
                    $data1['first_order'] = $ret_copy['first_order'];
                    $data1['state'] = $ret_copy['state'];
                    $data1['type'] = $ret_copy['type'];

                    $se_user = M("cuxiao");
                    $d_yhid['promotion_id'] = $ret_data['promotion_id'];
                    $se_user-> where($d_yhid)->save($data1);
                }

            }
            if(count($list)>0){
                if(count($data2)>0){
                    $add_ret = D('cuxiao')->addAll($data2);
                    if($add_ret >0){
                        $this->ajaxReturn("1");
                    }else{
                        $this->ajaxReturn("0");
                    }
                }else{
                    $this->ajaxReturn("1");
                }

            }else{
                $this->ajaxReturn("0");
            }

        }
        $this->ajaxReturn("0");
    }


}