<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/3
 * Time: 8:46
 */
namespace Mobile\Controller;
use Think\Controller;
use Think\Template\Driver\Mobile;

class CommentController extends Controller {
    public function index(){
        $model_comment = M('pingjia');
        $cx_comment = $model_comment
            ->join("JOIN wm_store ON wm_pingjia.store_id = wm_store.store_id")
            ->join("LEFT JOIN wm_order ON wm_order.order_id = wm_pingjia.order_id")
            ->join("JOIN wm_order_detail ON wm_order.order_id = wm_order_detail.order_id")
            ->join("JOIN wm_canming ON wm_order_detail.cm_id = wm_canming.cm_id")
            ->field("wm_pingjia.pj_time,wm_canming.cm_name,wm_order.send_time,wm_store.pingfen as 'qqq',wm_pingjia.pingfen,wm_pingjia.pj_songda")
            ->select();
        $this->assign('pingjia',$cx_comment);

        $this->display();
    }
}