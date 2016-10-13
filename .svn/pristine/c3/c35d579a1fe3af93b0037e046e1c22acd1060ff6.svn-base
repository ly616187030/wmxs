<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-11-05
 * Time: 8:55
 */
namespace Dispatch\Controller;
use Admin\Controller\AdminController;
class BookingorderController extends AdminController{

    public function index(){
        $User = M('order'); // 实例化User对象

        $time = strtotime(date('Y-m-d',time()));
        $time=$time+86400;
        $where['a.send_time'] = array('egt',$time);
        $where['a.status'] = array('elt',3);

        $count      = $User->alias('a')->where($where)->select();// 查询满足要求的总记录数

        $Page       = new \Think\Page(count($count),15);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');

        $Page->setConfig('first','首页');

        $Page->setConfig('prev','上一页');

        $Page->setConfig('next','下一页');

        $Page->setConfig('last','末页');

        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');

        $Page->lastSuffix = false;//分页最后的总页数不显示

        $show       = $Page->show();// 分页显示输出

        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性

        $list = $User->alias('a')
            ->join('LEFT JOIN wm_store AS b ON a.store_id=b.store_id')
            ->join('LEFT JOIN wm_shangquan AS c ON b.sq_id=c.sq_id')
            ->join('LEFT JOIN wm_user_address AS d ON a.address_id=d.address_id')
            ->join('LEFT JOIN wm_ucenter_member AS e ON a.uid=e.id')
            ->field('a.*,b.store_name,b.push_msg_id,b.lng as shop_lng,b.lat as shop_lat,b.distribution_range,c.sq_name,d.lng,d.lat,e.username')->order('a.order_id desc')->limit($Page->firstRow.','.$Page->listRows)->where($where)->select();
        $list1 = array();
        foreach($list as $s1){
            if($s1['lng'] != null){
                $s1['distance_number'] = distanceBetween($s1['shop_lng'],$s1['shop_lat'],$s1['lng'],$s1['lat']);
                //计算是否在配送范围
                $arr_user = array();
                $arr_user['x'] = $s1['lng'];
                $arr_user['y'] = $s1['lat'];
                $res= explode(",",$s1['distribution_range']);
                $arr_text1 = array();
                $arr_text = array();
                for($i=0;$i<count($res);$i+=2){
                    $arr_text1['x'] = $res[$i];
                    $arr_text1['y'] = $res[$i+1];
                    $arr_text[] = $arr_text1;
                }
                $s1['is_peisong'] = $this->PointInPoly($arr_user,$arr_text);
            }else{
                $s1['distance_number'] = '未知';
            }

            $list1[] = $s1;
        }

        $this->assign('list',$list1);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->meta_title = '预定订单';

        $this->display(); // 输出模板
    }
    public function cancelOrder(){
        $order_id = I('post.order_id');
        $content = I('post.content');
        $type = I('post.type');

        $m = M('Order');
        empty($content) && $this->error('请输入拒单原因');
        if($type == 0){
            $status = 9;
        }else if($type == 1){
            $status = 10;
        }
        $data = array('reject_time'=>time(),'reject_reason'=>$content,'status'=>$status);
        $list = $m->where('order_id = '.$order_id)->setField($data);
        $this->ajaxReturn($list);
    }
    //确认订单
    public function enterOrder(){
        $id = I('get.id');
        $m = M('Order');
        $list = $m->where('order_id = '.$id)->setField('accept_time',time());
        if($list > 0){
            $this->success('确认接单成功');
        }else{
            $this->error('确认接单失败');
        }
    }
    //订单详情
    public function orderMore(){
        $canming = I('order_id');
        if($canming){
            $order = M('order');
            $cx_order = $order
                ->join("JOIN wm_user_address ON wm_user_address.address_id = wm_order.address_id")
                ->join("JOIN wm_store ON wm_order.store_id = wm_store.store_id")
                ->where("wm_order.order_id = $canming")
                ->select();
            //查询订单详情
            $detail = M('canming');
            $cx_detail = $detail->join("JOIN wm_order_detail ON wm_order_detail.cm_id = wm_canming.cm_id") -> select();
            //重组订单与订单详情
            foreach ($cx_order as $key => $v) {
                foreach ($cx_detail as $k => $cv) {
                    if ($v['order_id'] == $cv['order_id']) {
                        $cx_order[$key]['child'][] = $cv;
                    }
                }
                $cx_order[$key]['count'] = count($cx_order[$key]['child'], 0);
            }
            $this->ajaxReturn($cx_order);

        }else{
            $this->ajaxReturn(false);
        }

    }
    //计算一个坐标是否在坐标集
    public function PointInPoly($pt, $poly){
        for ($c = false, $i = -1, $l = count($poly), $j = $l - 1; ++$i < $l; $j = $i){
            if((($poly[$i]['y'] <= $pt['y'] && $pt['y'] < $poly[$j]['y']) || ($poly[$j]['y'] <= $pt['y'] && $pt['y'] < $poly[$i]['y']))&& ($pt['x'] < ($poly[$j]['x'] - $poly[$i]['x']) * ($pt['y'] - $poly[$i]['y']) / ($poly[$j]['y'] - $poly[$i]['y']) + $poly[$i]['x'])){
                $c = !$c;
            }
        }
        return $c;
    }
}