<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/1
 * Time: 18:07
 */
namespace Mobile\Controller;
use Think\Controller;
use Think\Template\Driver\Mobile;

class FavoriteController extends Controller {
    public function index(){
        //显示用户收藏的店铺
        $uid = is_member_login();
        $model_store = M('favorites_store');
        $cx_store = $model_store
            ->join("LEFT JOIN wm_store ON wm_store.store_id = wm_favorites_store.store_id")
            ->where("wm_favorites_store.uid = $uid")
            ->select();
        $cx_yh = M('youhui')->order('yh_type desc')->select();
        foreach ($cx_store as $key => $v) {
            foreach ($cx_yh as $k => $cv) {
                if ($v['store_id'] == $cv['store_id']) {
                    $cx_store[$key]['child'][] = $cv;
                }
            }
            $cx_store[$key]['count'] = count($cx_store[$key]['child'], 0);
        }
        $this->assign('store', $cx_store);
        $this->assign('yh', $cx_yh);
        if(is_member_login()){
            $this->display();
        }else{
            $this->display('Users/login');
        }

    }
    public function del(){
        //删除收藏店铺
        $id = I('get.store_id');
        $uid = is_member_login();
        $model_favorite = M('favorites_store');
        $del_favo = $model_favorite->where("store_id = $id and uid = $uid")->delete();
        if($del_favo){
            $this->redirect("index");
        }
    }

}