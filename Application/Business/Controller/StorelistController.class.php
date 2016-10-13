<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-11-16
 * Time: 11:59
 */
namespace Business\Controller;

use Admin\Controller\AdminController;
class StorelistController extends AdminController{

    public function listStore(){
        $keyword = I('post.value');
        $uid = session('user_auth.uid');

        $m = M('Store');

        $store_id = $m->where('uid = '.$uid)->getField('store_id',true);
        $where['store_name_py'] = array('like','%'.$keyword.'%');
        //判读登录用户是否是管理员，true 没有条件
        if(is_administrator($uid)){
            $list_store = $m->where($where)->select();
        }else{
            if(empty($store_id)){
                $this->ajaxReturn(null);
            }
            $where['store_id'] = array('in',$store_id);
            $list_store = $m->where($where)->select();
        }
        $this->ajaxReturn($list_store);
    }
}