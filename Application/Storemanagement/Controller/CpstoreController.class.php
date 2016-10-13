<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/10
 * Time: 11:42
 */

namespace Storemanagement\Controller;
use Admin\Controller\AdminController;
use Common\Util\Tree;
class CpstoreController extends AdminController
{
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
            }
        }

        $map['c.store_id'] = $storeid;
        $map['c.flag'] =1;
        $result = M('Canpin')
            ->alias('c')
            ->join('LEFT JOIN __CANMING__ AS m ON c.canpin_id = m.canping_id')
            ->join('LEFT JOIN __PICTURE__ AS p ON m.big_img = p.id')
            ->where($map)
            ->field('c.canpin_id,c.canpin_name,m.cm_id,m.cm_name,m.now_price,m.canping_id,p.path,m.flag')
            ->select();
        $res = list_to_tree($result,'canpin_id','canping_id');

        $founderid['founder_id'] = FID;
        $allcp = M('Caipin')->where($founderid)->order('sort_order ASC')->field('cp_id,cp_name')->select();

//        dump($res);
        $this->assign('_all_cp',$allcp);
        $this->assign('canpin',$res);
        $this->assign('_storeid',$storeid);
        $this->assign('storename',$sname);
        $this->display();
    }

    public function categoryList($allcp,$cid,$storeid){
        $map['cp_id'] = array('in',$allcp);
        $res = M('caipin')->where($map)->select();
        foreach($res as $val){
            $list[] = array('cm_name'=>$val['cp_name'],
                'cm_py_name' => $val['cp_name_pyjx'],
                'cm_desc' => $val['cp_desc'],
                'big_img' => $val['cp_big_img'],
                'now_price' => $val['now_price'],
                'danwei' => $val['danwei'],
                'c_time' => time(),
                's_time' => $val['s_time'],
                'e_time' => $val['e_time'],
                'store_id' => $storeid,
                'canping_id' => $cid,
                'discount' =>$val['discount'],
                'min_number' => $val['min_number'],
                'storage_number' => $val['storage_number'],
                'remain_storage_number' => $val['remain_storage_number'],
                'food_box_price' => $val['food_box_price'],
                'food_box_number' => $val['food_box_number'],
                'saletime_type' => $val['saletime_type'],
                'sale_time' => $val['sale_time'],
                'sort_order' => $val['sort_order'],
                'week' => $val['week'],
                'flag' => 1
            );
        }
        $save = M('canming')->addAll($list);

        if($save != false){
            $this->ajaxReturn(array('info'=>'添加成功','status'=>1));
        }else{
            $this->ajaxReturn(array('info'=>'创建失败','status'=>0));
        }
       /* $map['c.store_id'] = $storeid;
        $result = M('Canpin')
            ->alias('c')
            ->join('INNER JOIN __CANMING__ AS m ON c.canpin_id = m.canping_id')
            ->join('LEFT JOIN __PICTURE__ AS p ON m.big_img = p.id')
            ->where($map)
            ->field('c.canpin_id,c.canpin_name,m.cm_id,m.cm_name,m.now_price,m.canping_id,p.path')
            ->select();

        $res = list_to_tree($result,'canpin_id','canping_id');
        //dump(json_encode($res));
        $this->ajaxReturn($res);*/
    }

    public function delCaipin($id){
        $res  = M('canming')->delete($id);
        if($res){
            action_log('delete_action','canming',$id,is_login());
            $this->ajaxReturn(array('status' => 1));
        }else{
            $this->ajaxReturn(array('status' => 0));
        }
    }

    public function changeCaipin($id,$status){
        $map['cm_id'] =$id;
        if($status == 'forbid') {
            $data['flag'] =0;
        }elseif('resume' == $status){
            $data['flag'] =1;
        }
        $result = M('canming')->where($map)->save($data);
        action_log('edit_action','canming',$id,is_login());
        if($result !== false){
            $this->ajaxReturn(array('status' => 1));
        }else{
            $this->ajaxReturn(array('status' => 0));
        }
    }
}