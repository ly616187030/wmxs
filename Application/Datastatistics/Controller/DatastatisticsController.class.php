<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-10-21
 * Time: 18:53
*/
namespace Datastatistics\Controller;
use Admin\Controller\AdminController;
class DatastatisticsController extends AdminController{

    /*
     * 日常运营数据统计
     */
    public function dailyData(){
        $dp = M('Delivery_person');
        $s = M('Store');
        $order = M('Order');

        $order_sn = I('order_sn');
        $store_id = I('store_id');
        $pay_type = I('pay_type');
        $person_id = I('person_id');
        $stime = strtotime(I('stime'));
        $etime = strtotime(I('etime'));
        $ord_peisong_status = I('ord_peisong_status');

        if(!empty($order_sn)){
            $where['a.order_sn'] = array('like',"%".$order_sn."%");
        }
        if(!empty($store_id)){
            $where['a.store_id'] = $store_id;
        }
        if($pay_type != null){
            $where['a.pay_type'] = $pay_type;
        }
        if(!(empty($stime) && empty($etime))){
            $where['a.xd_time'] = array('between',array($stime,$etime+86400));
        }
        if(!(empty($person_id))){
            $where['a.person_id'] = $person_id;
        }
        if($ord_peisong_status != null){
            $where['a.ord_peisong_status'] = $ord_peisong_status;
        }

        $count = $order->alias('a')->where($where)->count();

        $Page = new \Think\Page($count,15);
        //设置分页显示方式

        $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
        $Page->setConfig('first','首页');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('last','末页');
        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $Page->lastSuffix = false;//分页最后的总页数不显示

        $show = $Page->show();

        $list_dp = $dp->select();//查询所有的配送员
        $list_s = $s->select();//查询所有的商家

        $list_order = $order->alias('a')
            ->join('LEFT JOIN wm_store AS s ON a.store_id = s.store_id')
            ->join('LEFT JOIN wm_delivery_detail AS d ON a.order_id = d.order_id')
            ->join('LEFT JOIN wm_delivery_person AS p ON d.person_id = p.person_id')
            ->join('LEFT JOIN wm_shangquan AS sq ON s.sq_id = sq.sq_id')
            ->field('a.*,s.store_name,s.sq_id,s.city_code,p.person_name,sq.sq_name')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('a.order_id desc')
            ->where($where)->select();

        session('search',null);
        session('search',$order->_sql());

        $this->assign('list_dp',$list_dp);
        $this->assign('list_s',$list_s);
        $this->assign('list_order',$list_order);
        $this->assign('order_sn',$order_sn);
        $this->assign('store_id',$store_id);
        $this->assign('pay_type',$pay_type);
        $this->assign('stime',$stime);
        $this->assign('etime',$etime);
        $this->assign('person_id',$person_id);
        $this->assign('ord_peisong_status',$ord_peisong_status);
        $this->assign('page',$show);
        $this->meta_title='日常运营数据统计';
        $this->display();
    }
    /*
     * 运营指标监测
     */
    public function monitor(){
        $m = M('Order');

        $stime = strtotime(I('post.stime'));
        $etime = strtotime(I('post.etime'));


        $res = '';
        $str = '';
        $arr = array();
        if(IS_POST){
            $sstime = $stime;
            $new_time = ($etime-$stime)/86400;
        }else{
            $sstime = strtotime(date('Y-m-d',time()))-6*86400;
            $new_time = 6;
        }


        for($i=0;$i<=$new_time;$i++){
            $res .= "'".date('Y-m-d',$sstime)."',";
            $where['xd_time'] = array('between',array($sstime,$sstime+86400));
            $data['xd_time'] = array('between',array($sstime,$sstime+86400));
            $data['ord_peisong_status'] = 4;
            $arr[$i]['stime'] = $sstime;
            $sstime = $sstime+86400;
            $count = $m->where($where)->count();
            $w_count = $m->where($data)->count();
            $arr[$i]['count'] = $count;
            $arr[$i]['w_count'] = $w_count;
            $arr[$i]['w_lv'] = round($w_count/$count,2)*100;
            $str .= $count.',';

        }

        $this->assign('stime',$stime);
        $this->assign('etime',$etime);
        $this->assign('res',rtrim($res,','));
        $this->assign('str',rtrim($str,','));
        $this->assign('arr',$arr);
        $this->assign('qs_str',"1,2,3,5,10");
        $this->meta_title='运营指标监测';

        $this->display();
    }
    /*
     * 业务情况数据
     */
    public function business(){
        $m = M('Order');

        $stime = strtotime(I('post.stime'));
        $etime = strtotime(I('post.etime'));

        $res = '';
        $str = '';
        $order_arr = array();
        $order_avg = array();
        $order = '';
        $arr = array();
        if(IS_POST){
            $sstime = $stime;
            $new_time = ($etime-$stime)/86400;
        }else{
            $sstime = strtotime(date('Y-m-d',time()))-6*86400;
            $new_time = 6;
        }


        for($i=0;$i<=$new_time;$i++){
            $res .= "'".date('Y-m-d',$sstime)."',";
            $where['xd_time'] = array('between',array($sstime,$sstime+86400));
            $where['ord_peisong_status'] = 4;
            $arr[$i]['stime'] = $sstime;
            $sstime = $sstime+86400;
            $count = $m->where($where)->count();
            $sum = $m->where($where)->sum('total');
            $shop = $m->distinct(true)->where($where)->field('store_id')->select();
            $person_id = $m->distinct(true)->where($where)->field('person_id')->select();

            $arr[$i]['count'] = $count;//订单总数
            $arr[$i]['sum'] = $sum;//订单金额总价
            $arr[$i]['avg'] = round($sum/$count,2);//订单均价
            $arr[$i]['shop'] = count($shop);//交易商家数
            $arr[$i]['person'] = round($sum/count($person_id),2);//人均交易额
            $arr[$i]['order'] = round($count/count($person_id),2);//人均订单数

            $str .= $arr[$i]['person'].',';
            $order .= $arr[$i]['order'].',';

        }

        foreach($arr as $k=>$v){
            $order_arr[0] += $v['count'];
            $order_arr[1] += $v['sum'];
            $order_arr[2] += $v['avg'];
            $order_arr[3] += $v['shop'];
            $order_arr[4] += $v['person'];
            $order_arr[5] += $v['order'];
        }
        foreach($order_arr as $k=>$v){
            $order_avg[$k] = round($order_arr[$k]/($new_time+1),2);
        }
        session('arr',null);
        session('order_arr',null);
        session('order_avg',null);
        session('arr',$arr);
        session('order_arr',$order_arr);
        session('order_avg',$order_avg);

        $this->assign('stime',$stime);
        $this->assign('etime',$etime);
        $this->assign('res',rtrim($res,','));
        $this->assign('str',rtrim($str,','));
        $this->assign('order',rtrim($order,','));
        $this->assign('arr',$arr);
        $this->assign('order_arr',$order_arr);
        $this->assign('order_avg',$order_avg);

        $this->meta_title='业务情况数据';

        $this->display();
    }
    /*
     * 骑手效率统计
     */
    public function efficiency(){
        $m = M('Order');
        $n = M('Delivery_detail');

        $stime = strtotime(I('post.stime'));
        $etime = strtotime(I('post.etime'));
        $res = '';
        $str = array();
        $order_arr = array();
        $order_avg = array();
        $order = '';
        $one = '';
        $two = '';
        $three = '';
        $four = '';
        $five = '';
        $six = '';

        $arr = array();
        if(IS_POST){
            $sstime = $stime;
            $new_time = ($etime-$stime)/86400;
        }else{
            $sstime = strtotime(date('Y-m-d',time()))-6*86400;
            $new_time = 6;
        }
        $ssstimte = $sstime;
        for($i=0;$i<=$new_time;$i++){
            $time[]=date('Ymd',$ssstimte);
            $ssstimte = $ssstimte+86400;
        }

        for($i=0;$i<=$new_time;$i++){
            $res .= "'".date('Y-m-d',$sstime)."',";
            $where['assign_time'] = array('between',array($sstime,$sstime+86400));
            $arr[$i]['stime'] = $sstime;

            $sstime = $sstime+86400;

            $list_p = $n->where($where)->select();

                foreach($list_p as $k=>$v){
                   if(empty($v['delivery_time'])){
                        //骑手效率统计0-40分钟
                        if($v['delivery_time']>$v['assign_time'] && $v['delivery_time']<($v['assign_time']+2400)){
                            $data['delivery_time'] = array('between',array($v['assign_time'],$v['assign_time']+2399));
                            $arr[$i]['one'] = $n->where($data)->count();

                            foreach($time as $b){

                                if(date('Ymd',$v['assign_time']) == $b){
                                    $one .= $arr[$i]['one'].",";
                                }else{
                                    $one .= "0,";
                                }
                            }


                        }
                        //骑手效率统计40-60分钟
                        if($v['delivery_time']>=($v['assign_time']+2400) && $v['delivery_time']<($v['assign_time']+3600)){
                            $data['delivery_time'] = array('between',array($v['assign_time']+2400,$v['assign_time']+3599));
                            $arr[$i]['two'] = $n->where($data)->count();

                            foreach($time as $b){
                                if(date('Ymd',$v['assign_time']) == $b){
                                    $two .= $arr[$i]['two'].",";
                                }else{
                                    $two .= "0,";
                                }
                            }

                        }
                       //骑手效率统计60-90分钟
                        if($v['delivery_time']>=($v['assign_time']+3600) && $v['delivery_time']<($v['assign_time']+5400)){
                            $data['delivery_time'] = array('between',array($v['assign_time']+3600,$v['assign_time']+5399));
                            $arr[$i]['three'] = $n->where($data)->count();
                            foreach($time as $b){
                                if(date('Ymd',$v['assign_time']) == $b){
                                    $three .= $arr[$i]['three'].",";
                                }else{
                                    $three .= "0,";
                                }
                            }


                        }

                       //骑手效率统计90分钟以上
                        if($v['delivery_time']>=($v['assign_time']+5400)){
                            $data['delivery_time'] = array('egt',$v['assign_time']+5400);
                            $arr[$i]['four'] = $n->where($data)->count();
                            foreach($time as $b){
                                if(date('Ymd',$v['assign_time']) == $b){
                                    $four .= $arr[$i]['four'].",";
                                }else{
                                    $four .= "0,";
                                }
                            }

                        }
                   }
                    /*if(($v['delivery_time']>=$v['send_time']-600) && ($v['delivery_time']<=$v['send_time']+600)){
                        $data['delivery_time'] = array('between',array($v['send_time']-600,$v['send_time']+600));
                        $arr[$i]['five'] = $n->where($data)->count();
                        foreach($time as $b){
                            if(date('Ymd',$v['assign_time']) == $b){
                                $five .= $arr[$i]['five'].",";
                            }else{
                                $five .= "0,";
                            }
                        }
                    }
                    if(!($v['delivery_time']>=$v['send_time']-600) && ($v['delivery_time']<=$v['send_time']+600)){
                        $data['delivery_time'] = array('not between',array($v['send_time']-600,$v['send_time']+600));
                        $arr[$i]['six'] = $n->where($data)->count();
                        var_dump($arr[$i]['six']);
                        foreach($time as $b){
                            var_dump($b);
                            if(date('Ymd',$v['assign_time']) == $b){
                                $six .= $arr[$i]['six'].",";
                            }else{
                                $six .= "0,";
                            }
                        }
                    }*/



                }



            //$data['order_id'] = array('in',array(implode(',',$ord_id)));
            /*$assign_time = $n->where($data)->getField('assign_time',true);
            $delivery_time = $n->where($data)->getField('delivery_time',true);*/

            /*$new_data['delivery_time'] = array('between',array($assign_time,$assign_time+2400));
            $list = $n->where($data)->select();*/

        }


        //计算每个时间段的总和并写入数组
        foreach($arr as $k=>$v){
            $order_arr[0] += $v['one'];
            $order_arr[1] += $v['two'];
            $order_arr[2] += $v['three'];
            $order_arr[3] += $v['four'];

        }

        //计算每个时间段的平均值
        foreach($order_arr as $k=>$v){
            $order_avg[$k] = round($order_arr[$k]/($new_time+1),2);
        }
        //当值为空时用0补齐
        if(empty($one)){
            for($i=0;$i<=$new_time;$i++){
                $one .= "0,";
            }
        }
        if(empty($two)){
            for($i=0;$i<=$new_time;$i++){
                $two .= "0,";
            }
        }
        if(empty($three)){
            for($i=0;$i<=$new_time;$i++){
                $three .= "0,";
            }
        }
        if(empty($four)){
            for($i=0;$i<=$new_time;$i++){
                $four .= "0,";
            }
        }
        if(empty($five)){
            for($i=0;$i<=$new_time;$i++){
                $five .= "0,";
            }
        }
        if(empty($six)){
            for($i=0;$i<=$new_time;$i++){
                $six .= "0,";
            }
        }

        $this->assign('stime',$stime);
        $this->assign('etime',$etime);
        $this->assign('res',rtrim($res,','));
        $this->assign('str',rtrim($str,','));
        $this->assign('order',rtrim($order,','));
        $this->assign('one',rtrim($one,','));
        $this->assign('two',rtrim($two,','));
        $this->assign('three',rtrim($three,','));
        $this->assign('four',rtrim($four,','));
        $this->assign('five',rtrim($five,','));
        $this->assign('six',rtrim($six,','));
        $this->assign('arr',$arr);
        $this->assign('order_arr',$order_arr);
        $this->assign('order_avg',$order_avg);

        $this->meta_title='骑手效率统计';

        $this->display();
    }

    /*
     * 订单详情
     */
    public function orderMore(){
        $canming = I('post.id');
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
    /*
     * 导出excel文件
     */
    public function excel(){

        $Model=new \Think\Model();
        $str=session('search');

        $str=preg_replace(array('/LIMIT 0,15 /'),'',$str);
        $index=strpos($str,'desc');
        $str=substr($str,0,($index+4));

        $data=$Model->query("$str");
        foreach($data as $k=>$v){
            if($v['pay_type'] == 0){
                $data[$k]['pay_type'] = '货到付款';
            }else if($v['pay_type'] == 1){
                $data[$k]['pay_type'] = '在线支付';
            }
            switch ($v['ord_peisong_status']){
                case 0:$data[$k]['ord_peisong_status']="未分配";break;
                case 1:$data[$k]['ord_peisong_status']="已分配";break;
                case 2:$data[$k]['ord_peisong_status']="已接单";break;
                case 3:$data[$k]['ord_peisong_status']="已送餐";break;
                case 4:$data[$k]['ord_peisong_status']="已送达";break;
                case 5:$data[$k]['ord_peisong_status']="已取消";break;
            }
        }

        header("Content-type:application/vnd.ms-excel;charset=UTF-8");
        header("Content-disposition:filename='日常运营数据统计".date('Ymd',time()).".xls'");

        echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>
             <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
             <html>
                 <head>
                    <meta http-equiv='Content-type' content='text/html;charset=UTF-8' />
                 </head>
                 <body>
                     <div align=center x:publishsource='Excel'>
                         <table x:str border=1 cellpadding=0 cellspacing=0 width=100% style='border-collapse: collapse'>
                            <tr>
                            <th>订单编号</th>
                            <th>商家名称</th>
                            <th>城市</th>
                            <th>配送员</th>
                            <th>商圈</th>
                            <th>支付方式</th>
                            <th>预订单</th>
                            <th>线下结算</th>
                            <th>配送评分</th>
                            <th>运单状态</th>
                            <th>订单金额</th>
                            <th>付商家款</th>
                            <th>收用户款</th>
                            <th>配送费</th>
                            <th>下单时间</th>
                            </tr>";
                              foreach($data as $v){
                        echo "<tr>
                            <td>".$v['order_sn']."</td>
                            <td>".$v['store_name']."</td>
                            <td>".get_city_name('city',$v['city_code'])."</td>
                            <td>".$v['person_name']."</td>
                            <td>".$v['sq_name']."</td>
                            <td>".$v['pay_type']."</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>".$v['ord_peisong_status']."</td>
                            <td>".$v['total']."</td>
                            <td></td>
                            <td></td>
                            <td>".$v['ps_cost']."</td>
                            <td>".date('Y-m-d H:i:s',$v["xd_time"])."</td></tr>";
                    }

                    echo "</table>
                     </div>
                 </body>
             </html>";
        /*echo "订单编号\t";
        echo "商家名称\t";
        echo "城市\t";
        echo "配送员\t";
        echo "商圈\t";
        echo "支付方式\t";
        echo "预订单\t";
        echo "线下结算\t";
        echo "配送评分\t";
        echo "运单状态\t";
        echo "订单金额\t";
        echo "付商家款\t";
        echo "收用户款\t";
        echo "配送费\t";
        echo "下单时间\t\n";
        foreach($data as $v){
            echo $v['order_sn']."\t";
            echo $v['store_name']."\t";
            echo get_city_name('city',$v['city_code'])."\t";
            echo $v['person_name']."\t";
            echo $v['sq_name']."\t";
            if($v["pay_type"]==0){
                echo "货到付款\t";
            }else if($v["pay_type"]==1){
                echo "在线支付\t";
            }
            echo "\t";
            if($v["pay_type"]==1){
                echo "是\t";
            }else{
                echo "否\t";
            }
            echo "\t";
            echo "\t";
            echo $v["total"]."\t";
            echo "\t";
            echo "\t";
            echo $v["ps_cost"]."\t";
            echo date('Y-m-d H:i:s',$v["xd_time"])."\t\n";
        }*/

    }
    public function businessExcel(){

        $arr = session('arr');
        $order_arr = session('order_arr');
        $order_avg = session('order_avg');
        foreach($arr as $k=>$v){
            if($v['sum'] == null){
                $arr[$k]['sum'] = 0;
            }
        }
        header("Content-type:application/vnd.ms-excel;charset=UTF-8");
        header("Content-disposition:filename='业务情况数据".date('Ymd',time()).".xls'");

        echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>
             <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
             <html>
                 <head>
                    <meta http-equiv='Content-type' content='text/html;charset=UTF-8' />
                 </head>
                 <body>
                     <div align=center x:publishsource='Excel'>
                         <table x:str border=1 cellpadding=0 cellspacing=0 width=100% style='border-collapse: collapse'>
                            <tr>
                            <th>日期</th>
                            <th>订单总数</th>
                            <th>交易额总数</th>
                            <th>订单均价</th>
                            <th>交易商家总数</th>
                            <th>活跃骑手人均交易额(元)</th>
                            <th>活跃骑手人均订单数</th>
                            </tr>";
                            foreach($arr as $v){
                                echo "<tr>
                                        <td>".date('Y-m-d',$v["stime"])."</td>
                                        <td>".$v['count']."</td>
                                        <td>".$v['sum']."</td>
                                        <td>".$v['avg']."</td>
                                        <td>".$v['shop']."</td>
                                        <td>".$v['person']."</td>
                                        <td>".$v['order']."</td>
                                    </tr>";
                            }
                            echo "<tr>
                                    <td>平均</td>
                                    <td>".$order_avg[0]."</td>
                                    <td>".$order_avg[1]."</td>
                                    <td>".$order_avg[2]."</td>
                                    <td>".$order_avg[3]."</td>
                                    <td>".$order_avg[4]."</td>
                                    <td>".$order_avg[5]."</td>
                                </tr>
                                <tr>
                                    <td>总计</td>
                                    <td>".$order_arr[0]."</td>
                                    <td>".$order_arr[1]."</td>
                                    <td>--</td>
                                    <td>".$order_arr[3]."</td>
                                    <td>".$order_arr[4]."</td>
                                    <td>".$order_arr[5]."</td>
                                </tr>

                         </table>
                     </div>
                 </body>
             </html>";
        /*echo "日期\t";
        echo "订单总数\t";
        echo "交易额总数\t";
        echo "订单均价\t";
        echo "交易商家总数\t";
        echo "活跃骑手人均交易额(元)\t";
        echo "活跃骑手人均订单数\t\n";
        foreach($arr as $v){
            echo date('Y-m-d H:i:s',$v["stime"])."\t";
            echo $v['count']."\t";
            if($v['sum'] == null){
                echo "0\t";
            }else{
                echo $v['sum']."\t";
            }
            echo $v['avg']."\t";
            echo $v["shop"]."\t";
            echo $v["person"]."\t";
            echo $v["order"]."\t\n";
        }
        echo "平均\t";
        echo $order_avg[0]."\t";
        echo $order_avg[1]."\t";
        echo $order_avg[2]."\t";
        echo $order_avg[3]."\t";
        echo $order_avg[4]."\t";
        echo $order_avg[5]."\t\n";
        echo "总计\t";
        echo $order_arr[0]."\t";
        echo $order_arr[1]."\t";
        echo "--\t";
        echo $order_arr[3]."\t";
        echo $order_arr[4]."\t";
        echo $order_arr[5]."\t\n";*/

    }

}