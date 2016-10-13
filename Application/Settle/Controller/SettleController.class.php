<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/10/22
 * Time: 20:40
 */
namespace Settle\Controller;
use Admin\Controller\AdminController;

class SettleController extends AdminController{

    public function index(){

        $jintian=I('datevalue');
        $zt=I('startdate');
        $jst=I('overdate');

        $datevalue = strtotime($jintian);
        $startdate = strtotime($zt);
        $overdate = strtotime($jst);
        $definedStart = strtotime(I('definedStart'));
        $definedOver = strtotime(I('definedOver'));
        $deliver_id=I('deliver');
        $orderId=I('orderId');
        $ordertype=I('ordertype');
        $date = I('date');
        $store_id=I('store_id');

        if(!empty($datevalue)) {
            $condition['od.complete_time']=array('between',array($datevalue,$datevalue+86400));
        }elseif(!empty($startdate)&&!empty($overdate)) {
            $condition['od.complete_time']=array('between',array($startdate,$overdate+86400));
        }elseif(!empty($definedStart)&&!empty($definedOver)){
            $condition['od.complete_time']=array('between',array($definedStart,$definedOver));
        }else{
            //默认为今天数据
            $time=time();
            $jtTime=date("y-m-d",$time);
            $datevalue=strtotime($jtTime);
            $condition['od.complete_time'] =array('between', array($datevalue, $datevalue + 86400));
        }

        if(!empty($orderId)) {
            $condition['order_sn']=$orderId;
        }

        if(!empty($deliver_id)) {
            $condition['od.person_id']=$deliver_id;
        }

        if( $ordertype== 4 ) {
            $condition['source']=$ordertype;
        }else{
            $condition['pay_type']=$ordertype;
        }

//        $condition['ord_peisong_status']=4;
        $condition['od.status']  = array('in',array(5,6));

        $user = M('admin_user');
        $Store=M('store');
        $uid=is_login();
        $admin=$user->where('id='.$uid)->field('user_type,founder_id')->find();

        $DeliveryPerson=M('DeliveryPerson');
        $condition20['de.uid']=UID;
        $deliver=$DeliveryPerson->alias('de')->where($condition20)->select();   //搜索项骑手姓名查询

        //注册公司获得所有商家
        if($admin['user_type']=='company'){
            $condition2['founder_id']=$admin['founder_id'];
            $info3=$Store->where($condition2)->field('store_id,store_name')->select();
            if($info3){
                foreach($info3 as $k=>$v){
                    $infomation[]=$v['store_id'];
                }
                $infomationID=implode(',',$infomation);

                if(!empty($store_id)) {
                    $condition['od.store_id']=$store_id;
                }else{
                    $condition['od.store_id']=array('in',$infomationID);
                }
            }
        }
        //普通用户登陆
        if($admin['user_type']=='company_member'){

            $info12=$Store->where('uid='.$uid)->getField('store_id',true);
            $assigned_store=M('assigned_store');
            $info11=$assigned_store->where('user_id='.UID)->getField('store_id',true);

            if($info12 && $info11){
                    $infomation=array_merge($info12,$info11);
                    $infomationID=implode(',',$infomation);

                    $where['store_id']=array('in',$infomationID);
                    $info3=$Store->where($where)->field('store_id,store_name')->select();
                }
            if($info12 && empty($info11)){
                $infomationID=implode(',',$info12);
                $where['store_id']=array('in',$infomationID);
                $info3=$Store->where($where)->field('store_id,store_name')->select();
            }

            if($info11 && empty($info12)){
                $infomationID=implode(',',$info11);
                $where['store_id']=array('in',$infomationID);
                $info3=$Store->where($where)->field('store_id,store_name')->select();
            }
            if(!empty($store_id)) {
                $condition['od.store_id']=$store_id;
            }else{
                $condition['od.store_id']=array('in',$infomationID);
            }
//            dump($condition);
        }

        $Order=M('Order');
        if($info3){
            $count = $Order->alias('od')->where($condition)->count();

            $Page = new \Think\Page($count,15);
            //分页显示设置

            $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
            $Page->setConfig('first','首页');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('last','末页');
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
            $Page->lastSuffix = false;  //分页最后的总页数不显示

            $show = $Page->show();

            //连表查询所需数据
            $infos=$Order->alias('od')
                ->join("LEFT JOIN __DELIVERY_PERSON__ as de ON de.person_id = od.person_id")
                ->join("LEFT JOIN __STORE__ as st ON st.store_id = od.store_id")
                ->field("od.*,de.person_name,st.store_name,st.*")
                ->limit($Page->firstRow.','.$Page->listRows)
                ->where($condition)
                ->order('od.order_id desc')
                ->select();
//        dump($condition);

            foreach($infos as $key=>$value){
                $infos[$key]['ddjg']=round($value['ps_cost']+$value['total']+$value['canju_total']-$value['yh_price'],2);  //订单总价
                $infos[$key]['qcsf']=round($value['total']*(1-$value['store_rebate']/100),2);  //取餐实付=菜品原价-商家返点
                $infos[$key]['ccje']=round($value['total']*$value['store_rebate']/100,2); //抽成金额
                $infos[$key]['jmhd']=round($value['yh_price']+$value['juan_price'],2); //优惠金额

                //判断货到付款和在线支付，货到付款=0，在线支付=1
                if($value['pay_type']==0){
                    $infos[$key]['scss']=round($value['ps_cost']+$value['total']+$value['canju_total']-$value['yh_price'],2);  //送餐实收=货到付款的订单总价
                    $infos[$key]['ysjje']=round($infos[$key]['scss']-$infos[$key]['qcsf'],2);  //货到付款应上缴金额
                    $infos[$key]['xszf']=0;  //货到付款时的在线支付金额=0
                }elseif($value['pay_type']==1){
                    $infos[$key]['scss']=0;  //在线支付的送餐实收=0
                    $infos[$key]['ysjje']=round($infos[$key]['scss']-$infos[$key]['qcsf'],2);  //在线支付应上缴金额
                    $infos[$key]['xszf']=round($value['ps_cost']+$value['total']+$value['canju_total']-$value['yh_price'],2);  //线上支付
                }elseif($value['pay_type']==4){
                    $infos[$key]['scss']=round($value['ps_cost']+$value['total']+$value['canju_total']-$value['yh_price'],2);  //送餐实收=货到付款的订单总价
                    $infos[$key]['ysjje']=round($infos[$key]['scss']-$infos[$key]['qcsf'],2);  //货到付款应上缴金额
                    $infos[$key]['xszf']=0;  //货到付款时的在线支付金额=0
                }
            }

            //导出表格需要数据
            $datas=$Order->alias('od')
                ->join("LEFT JOIN __DELIVERY_PERSON__ as de ON de.person_id = od.person_id")
                ->join("LEFT JOIN __STORE__ as st ON st.store_id = od.store_id")
                ->field("od.*,de.person_name,st.store_name,st.*")
                ->where($condition)
                ->order('od.order_id desc')
                ->select();

            foreach($datas as $k=>$v){
                $datas[$k]['ddjg']=round($v['ps_cost']+$v['total']+$v['canju_total']-$v['yh_price'],2);  //订单总价
                $datas[$k]['qcsf']=round($v['total']*(1-$v['store_rebate']/100),2);  //取餐实付=菜品原价-商家返点
                $datas[$k]['ccje']=round($v['total']*$v['store_rebate']/100,2); //抽成金额
                $datas[$k]['jmhd']=round($v['yh_price']+$v['juan_price'],2); //优惠金额

                //判断货到付款和在线支付，货到付款=0，在线支付=1
                if($v['pay_type']==0){
                    $datas[$k]['scss']=round($v['ps_cost']+$v['total']+$v['canju_total']-$v['yh_price'],2);  //送餐实收=货到付款的订单总价
                    $datas[$k]['ysjje']=round($datas[$k]['scss']-$datas[$k]['qcsf'],2);  //货到付款应上缴金额
                    $datas[$k]['xszf']=0;  //货到付款时的在线支付金额=0
                }elseif($v['pay_type']==1){
                    $datas[$k]['scss']=0;  //在线支付的送餐实收=0
                    $datas[$k]['ysjje']=round($datas[$k]['scss']-$datas[$k]['qcsf'],2);  //在线支付应上缴金额
                    $datas[$k]['xszf']=round($v['ps_cost']+$v['total']+$v['canju_total']-$v['yh_price'],2);  //线上支付
                }elseif($v['pay_type']==4){
                    $datas[$k]['scss']=round($v['ps_cost']+$v['total']+$v['canju_total']-$v['yh_price'],2);  //送餐实收=货到付款的订单总价
                    $datas[$k]['ysjje']=round($datas[$k]['scss']-$datas[$k]['qcsf'],2);  //货到付款应上缴金额
                    $datas[$k]['xszf']=0;  //货到付款时的在线支付金额=0
                }
            }



            session('datas',null);
            session('datas',$datas);

            //各种合计
            $sumccje=round($Order->alias('od')
                ->join("LEFT JOIN __STORE__ as st ON st.store_id = od.store_id")
                ->field("od.*,st.store_rebate")
                ->where($condition)
                ->sum('total*store_rebate/100'),2);   //抽成总额
            $sumCpyj = $Order->alias('od')->where($condition)->sum('total');     //菜品原价总额
            $sumPscost = $Order->alias('od')->where($condition)->sum('ps_cost');   //邮资总额
            $sumddjg=$Order->alias('od')->where($condition)->sum('ps_cost+total+canju_total-yh_price'); //订单总额
            $sumqcsf=$Order->alias('od')   //取餐实付总额
            ->join("LEFT JOIN __STORE__ as st ON st.store_id = od.store_id")
                ->field("od.*,st.store_rebate")
                ->where($condition)
                ->sum("total*(1-store_rebate/100)");

            $sumcjze=$Order->alias('od')->where($condition)->sum('canju_total');  //餐具费用总额
            $sumyhze=$Order->alias('od')->where($condition)->sum('yh_price+juan_price');   //优惠总额

            if($ordertype!=1 ) {
                $condition['pay_type']=0;
                $sumscss=$Order->alias('od')->where($condition)->sum('ps_cost+total+canju_total-yh_price');
            }else{
                $sumscss=$Order->alias('od')->where($condition)->sum('ps_cost+total+canju_total-yh_price');
            }

            $sumxszf=$sumddjg-$sumscss; //线上支付总额
            $sumyssje=$sumscss-$sumqcsf;   //应上缴金额总额
        }

        $this->assign('deliver',$deliver);
        $this->assign('infos',$infos);
        $this->assign('page',$show);
        $this->assign('sumCpyj',round($sumCpyj,2));
        $this->assign('sumPscost',round($sumPscost,2));
        $this->assign('sumddjg',round($sumddjg,2));
        $this->assign('sumscss',round($sumscss,2));
        $this->assign('sumyssje',round($sumyssje,2));
        $this->assign('sumqcsf',round($sumqcsf,2));
        $this->assign('sumcjze',round($sumcjze,2));
        $this->assign('sumyhze',round($sumyhze,2));
        $this->assign('sumxszf',round($sumxszf,2));
        $this->assign('sumccje',round($sumccje,2));
        $this->assign('definedStart',$definedStart);
        $this->assign('definedOver',$definedOver);
        $this->assign('deliver_id',$deliver_id);
        $this->assign('orderId',$orderId);
        $this->assign('date',$date);
        $this->assign('ordertype',$ordertype);
        $this->assign('store_id',$store_id);
        $this->assign('info',$info3);
        $this->assign('jintian', $jintian);
        $this->assign('zt', $zt);
        $this->assign('jst', $jst);
        $this->assign('jtTime', $jtTime);
        $this->display();
    }

    public function orderdetail(){
        $Order = M('Order');
        $dingdanId=I('dingdanId');
        $tiaojian['od.order_id']=$dingdanId;

        $orderDetail=$Order->alias('od')
            ->join("LEFT JOIN __ORDER_DETAIL__ as detail ON detail.order_id = od.order_id")
            ->join("LEFT JOIN __CANMING__ as canming ON canming.cm_id = detail.cm_id")
            ->where($tiaojian)
//            ->field('')
            ->select();
        $this->ajaxReturn($dingdanId) ;
    }

/*
* 导出EXCEL
*/
    public function excel(){

        $Model=new \Think\Model();
        $data=session('datas');
//        dump($str);exit;

        foreach($data as $k=>$v){
            if($v['pay_type'] == 0){
                $data[$k]['pay_type'] = '货到付款';
            }else if($v['pay_type'] == 1){
                $data[$k]['pay_type'] = '在线支付';
            }else if($v['pay_type'] == 4){
                $data[$k]['pay_type'] = '快速下单';
            }
        }

        header("Content-type:application/vnd.ms-excel;charset=UTF-8");
        header("Content-disposition:filename='站点对账".date('Ymd',time()).".xls'");

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
                            <th>订单显示ID</th>
                            <th>订单已送达时间</th>
                            <th>商家名称</th>
                            <th>商家结算方式</th>
                            <th>订单类型</th>
                            <th>骑手姓名</th>
                            <th>订单总价</th>
                            <th>活动减免</th>
                            <th>菜品原价</th>
                            <th>打包费</th>
                            <th>邮资</th>
                            <th>抽成</th>
                            <th>取餐应付</th>
                            <th>送餐应收</th>
                            <th>线上支付金额</th>
                            <th>应上缴金额</th>
                            <th>备注</th>
                            </tr>";
        foreach($data as $v){
            echo "<tr>
                            <td>".$v['order_sn']."</td>
                            <td>".date('Y-m-d H:i:s',$v['ps_time'])."</td>
                            <td>".$v['store_name']."</td>
                            <td>".''."</td>
                            <td>".$v['pay_type']."</td>
                            <td>".$v['person_name']."</td>
                            <td>".$v['ddjg']."</td>
                            <td>".$v['jmhd']."</td>
                            <td>".$v['total']."</td>
                            <td>".$v['canju_total']."</td>
                            <td>".$v['ps_cost']."</td>
                            <td>".$v['ccje']."</td>
                            <td>".$v['qcsf']."</td>
                            <td>".$v['scss']."</td>
                            <td>".$v['xszf']."</td>
                            <td>".$v['ysjje']."</td>
                            <td>".''."</td>
                            </tr>";
        }

        echo "</table>
                     </div>
                 </body>
             </html>";
    }


    public function shopsettlement(){

        $date = I('date');
        $jintian=I('datevalue');
        $zt=I('startdate');
        $jst=I('overdate');

        $datevalue = strtotime($jintian);
        $startdate = strtotime($zt);
        $overdate = strtotime($jst);
        $definedStart = strtotime(I('definedStart'));
        $definedOver = strtotime(I('definedOver'));
        $store_id=I('store_id');

        if(!empty($datevalue)) {
            $condition['complete_time']=array('between',array($datevalue,$datevalue+86400));
            $condition2['a.complete_time']=array('between',array($datevalue,$datevalue+86400));
        }elseif(!empty($startdate)&&!empty($overdate)) {
            $condition['complete_time']=array('between',array($startdate,$overdate+86400));
            $condition2['a.complete_time']=array('between',array($startdate,$overdate+86400));
        }elseif(!empty($definedStart)&&!empty($definedOver)){
            $condition['complete_time']=array('between',array($definedStart,$definedOver));
            $condition2['a.complete_time']=array('between',array($definedStart,$definedOver));
        }else{
            //默认为今天数据
            $time=time();
            $jtTime=date("y-m-d",$time);
            $datevalue=strtotime($jtTime);
            $condition['complete_time'] =array('between', array($datevalue, $datevalue + 86400));
            $condition2['a.complete_time'] =array('between', array($datevalue, $datevalue + 86400));
        }

        $user = M('admin_user');
        $Store=M('store');
        $uid=is_login();
        $admin=$user->where('id='.$uid)->field('user_type,founder_id')->find();

        //注册公司获得所有商家
        if($admin['user_type']=='company'){
            $condition8['founder_id']=$admin['founder_id'];
            $info3=$Store->where($condition8)->field('store_id,store_name')->select();
            if($info3){
                foreach($info3 as $k=>$v){
                    $infomation[]=$v['store_id'];
                }
                $infomationID=implode(',',$infomation);

                if(!empty($store_id)) {
                    $condition['store_id']=$store_id;
                    $condition2['a.store_id']=$store_id;
                }else{
                    $condition['store_id']=array('in',$infomationID);
                    $condition2['a.store_id']=array('in',$infomationID);
                }
            }
        }
        //普通用户登陆
        if($admin['user_type']=='company_member'){

            $info12=$Store->where('uid='.$uid)->getField('store_id',true);

            $assigned_store=M('assigned_store');
            $info11=$assigned_store->where('user_id='.UID)->getField('store_id',true);

            if($info11 && $info12){
                $infomation=array_merge($info11,$info12);
                $infomationID=implode(',',$infomation);

                $where['store_id']=array('in',$infomationID);
                $info3=$Store->where($where)->field('store_id,store_name')->select();
            }

            if($info11 && empty($info12)){
                $infomationID=implode(',',$info11);

                $where['store_id']=array('in',$infomationID);
                $info3=$Store->where($where)->field('store_id,store_name')->select();
            }

            if($info12 && empty($info11)){
                $infomationID=implode(',',$info12);

                $where['store_id']=array('in',$infomationID);
                $info3=$Store->where($where)->field('store_id,store_name')->select();
            }
            if(!empty($store_id)) {
                 $condition['store_id']=$store_id;
                 $condition2['a.store_id']=$store_id;
             }else{
                 $condition['store_id']=array('in',$infomationID);
                 $condition2['a.store_id']=array('in',$infomationID);
            }
        }

//        $condition2['ord_peisong_status']=4;
        $condition2['a.status']  = array('in',array(5,6));

        $Order=M('Order');
        if($info3){
            $count = $Store->where($condition)->count();
//        dump($count);
            $Page = new \Think\Page($count,15);
            //分页设置

            $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
            $Page->setConfig('first','首页');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('last','末页');
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $Page->lastSuffix = false;  //分页最后的总页数不显示

            $show = $Page->show();

            $store =M('store')->where($condition)->limit($Page->firstRow.','.$Page->listRows)->select();
            $order =$Order
                ->alias('a')
                ->join('LEFT JOIN __STORE__ AS b ON b.store_id = a.store_id')
                ->where($condition2)
                ->order('order_id desc')
                ->select();
//        dump($condition2);

            $zongji['numtotal']=0;
            $zongji['heji']=0;
            $zongji['peisong']=0;
            $zongji['money']=0;
            $zongji['dabaofei']=0;
            $zongji['choucheng']=0;
            foreach($store as $k=>$v){
                //设置初始值为零
                $total=0;
                $store[$k]['num']=0;
                $store[$k]['total']=0;
                $store[$k]['peisong_total']=0;
                $store[$k]['yh_price']=0;
                $store[$k]['canju_total']=0;
                $store[$k]['total_money']=0;
                $store[$k]['store_rebate']=0;
                foreach($order as $kk=>$vv){
                    if($v['store_id']==$vv['store_id']){
                        $total++;
                        $store[$k]['total']+=round($vv['total'],2); //菜品原价
                        $store[$k]['peisong_total']+=round($vv['ps_cost'],2); //配送费
                        $store[$k]['yh_price']+=round($vv['yh_price'],2); //优惠金额
                        $store[$k]['canju_total']+=round($vv['canju_total'],2); //打包费
                        $store[$k]['store_rebate']+=round($vv['total']*$vv['store_rebate']/100,2); //抽成金额
                    }
                }
                $store[$k]['num']=$total; //订单量
                $store[$k]['total_money']=round($store[$k]['total']+$store[$k]['peisong_total']+$store[$k]['canju_total']-$store[$k]['yh_price'],2);//订单金额
            }

            $zongji['numtotal']= round($Order->alias('a')->where($condition2)->count(),2); //订单量总额
            $zongji['heji']=round($Order->alias('a')->where($condition2)->sum('ps_cost+total+canju_total-yh_price'),2); //订单总金额
            $zongji['peisong']=round($Order->alias('a')->where($condition2)->sum('ps_cost'),2); //邮资总额
            $zongji['money']=round($Order->alias('a')->where($condition2)->sum('total'),2); //菜品原价总额
            $zongji['dabaofei']=round($Order->alias('a')->where($condition2)->sum('canju_total'),2); //打包费总额
            $zongji['choucheng']=round($Order
                ->alias('a')
                ->join('LEFT JOIN __STORE__ AS b ON b.store_id = a.store_id')
                ->where($condition2)
                ->sum('total*store_rebate/100'),2); //抽成总额

            //输出表格需要的数据
            $storeExcel =M('store')->where($condition)->select();
            foreach($storeExcel as $key=>$value){
                //设置初始值为零
                $total=0;
                $storeExcel[$key]['num']=0;
                $storeExcel[$key]['total']=0;
                $storeExcel[$key]['peisong_total']=0;
                $storeExcel[$key]['yh_price']=0;
                $storeExcel[$key]['canju_total']=0;
                $storeExcel[$key]['total_money']=0;
                $storeExcel[$key]['store_rebate']=0;
                foreach($order as $kk=>$vv){
                    if($value['store_id']==$vv['store_id']){
                        $total++;
                        $storeExcel[$key]['total']+=$vv['total']; //菜品原价
                        $storeExcel[$key]['peisong_total']+=$vv['ps_cost']; //配送费
                        $storeExcel[$key]['yh_price']+=$vv['yh_price']; //优惠金额
                        $storeExcel[$key]['canju_total']+=$vv['canju_total']; //打包费
                        $storeExcel[$key]['store_rebate']+=$vv['total']*$vv['store_rebate']/100; //抽成金额
                    }
                }
                $storeExcel[$key]['num']=$total; //订单量
                $storeExcel[$key]['total_money']=$store[$key]['total']+$store[$key]['peisong_total']+$store[$key]['canju_total']-$store[$key]['yh_price'];//订单金额
            }

        }

        session('storeExcel',null);
        session('storeExcel',$storeExcel);
        session('zongji',null);
        session('zongji',$zongji);

        $this->assign('info',$info3);
        $this->assign('page',$show);
        $this->assign('store',$store);
        $this->assign('zongji',$zongji);
        $this->assign('store_id',$store_id);
        $this->assign('date',$date);
        $this->assign('store_id',$store_id);
        $this->assign('info',$info3);
        $this->assign('jintian', $jintian);
        $this->assign('zt', $zt);
        $this->assign('jst', $jst);
        $this->assign('jtTime', $jtTime);
        $this->display();
    }

    /*
   *  导出EXCEL
   */
    public function shopexcel(){

        $Model=new \Think\Model();
        $data=session('storeExcel');
        $zongji=session('zongji');
        $data['zongji']=$zongji;

//        dump($data);
//        exit;

        header("Content-type:application/vnd.ms-excel;charset=UTF-8");
        header("Content-disposition:filename='商家结算对账".date('Ymd',time()).".xls'");

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
                            <th>商家ID</th>
                            <th>商家名称</th>
                            <th>订单量</th>
                            <th>订单金额</th>
                            <th>商品原价</th>
                            <th>打包费</th>
                            <th>配送费</th>
                            <th>抽成</th>
                            <th>订单总量</th>
                            <th>订单总额</th>
                            <th>商品原价总额</th>
                            <th>打包费总额</th>
                            <th>配送费总额</th>
                            <th>抽成总额</th>
                            </tr>";
        foreach($data as $v){
            echo "<tr>
                            <td>".$v['store_id']."</td>
                            <td>".$v['store_name']."</td>
                            <td>".$v['num']."</td>
                            <td>".$v['total_money']."</td>
                            <td>".$v['total']."</td>
                            <td>".$v['canju_total']."</td>
                            <td>".$v['peisong_total']."</td>
                            <td>".$v['store_rebate']."</td>
                            <td>".$v['numtotal']."</td>
                            <td>".$v['heji']."</td>
                            <td>".$v['money']."</td>
                            <td>".$v['dabaofei']."</td>
                            <td>".$v['peisong']."</td>
                            <td>".$v['choucheng']."</td>
                            </tr>";
        }

        echo "</table>
                     </div>
                 </body>
             </html>";

    }


    public function deliversettlement(){

        $date = I('date');
        $jintian=I('datevalue');
        $zt=I('startdate');
        $jst=I('overdate');

        $datevalue = strtotime($jintian);
        $startdate = strtotime($zt);
        $overdate = strtotime($jst);
        $definedStart = strtotime(I('definedStart'));
        $definedOver = strtotime(I('definedOver'));
        $person_id=I('deliver');

        if(!empty($datevalue)) {
            $condition['complete_time']=array('between',array($datevalue,$datevalue+86400));
            $condition2['a.complete_time']=array('between',array($datevalue,$datevalue+86400));
        }elseif(!empty($startdate)&&!empty($overdate)) {
            $condition['complete_time']=array('between',array($startdate,$overdate+86400));
            $condition2['a.complete_time']=array('between',array($startdate,$overdate+86400));
        }elseif(!empty($definedStart)&&!empty($definedOver)){
            $condition['complete_time']=array('between',array($definedStart,$definedOver));
            $condition2['a.complete_time']=array('between',array($definedStart,$definedOver));
        }else{
            //默认为今天数据
            $time=time();
            $jtTime=date("y-m-d",$time);
            $datevalue=strtotime($jtTime);
            $condition['complete_time'] =array('between', array($datevalue, $datevalue + 86400));
            $condition2['a.complete_time'] =array('between', array($datevalue, $datevalue + 86400));
        }
        if(!empty($person_id)){
            $condition['dp_delivery_person.person_id']=$person_id;
        }

        $Order=M('Order');
        $user = M('admin_user');
        $Store=M('store');

        $uid=is_login();
        $admin=$user->where('id='.$uid)->field('user_type,founder_id')->find();
        if($admin['user_type']=='company'){
            $condition['founder_id']=$admin['founder_id'];
        }
        if($admin['user_type']=='company_member'){
                $condition['uid']=$uid;
        }
        $DeliveryPerson=M('DeliveryPerson');
        $deliverman =$DeliveryPerson->where($condition)->select();  //搜索项骑手姓名查询

        $count = $DeliveryPerson->where($condition)->count();
//        dump($DeliveryPerson->_sql());
        $Page = new \Think\Page($count,15);

        //分页设置
        $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
        $Page->setConfig('first','首页');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('last','末页');
        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $Page->lastSuffix = false;  //分页最后的总页数不显示

        $show = $Page->show();

        $deliver = $DeliveryPerson-> where($condition)->limit($Page->firstRow.','.$Page->listRows)->select();

        //创建者登陆获得所有商家
        if($admin['user_type']=='company'){
            $condition8['founder_id']=$admin['founder_id'];
            $info3=$Store->where($condition8)->field('store_id,store_name')->select();
            foreach($info3 as $k=>$v){
                $infomation[]=$v['store_id'];
            }
            $infomationID=implode(',',$infomation);
        }

        //普通用户登陆
        if($admin['user_type']=='company_member'){
            $info12=$Store->where('uid='.$uid)->getField('store_id',true);
            $assigned_store=M('assigned_store');
            $info11=$assigned_store->where('user_id='.UID)->getField('store_id',true);
            if($info11 && $info12){
                $infomation=array_merge($info11,$info12);
            }
            if($info11 && empty($info12)){
                $infomation=$info11;
            }
            if($info12 && empty($info11)){
                $infomation=$info12;
            }
            $infomationID=implode(',',$infomation);
        }

        $zongji['numtotal']=0;
        $zongji['hsddzl']=0;
        $zongji['hsgjzl']=0;
        $zongji['yfsjje']=0;
        foreach($deliver as $k=>$v){
            $a['person_id']=$v['person_id'];
//            $a['ord_peisong_status']=4;
            $a['wm_order.status']  = array('in',array(5,6));
            if($infomationID!=null){
                $a['wm_order.store_id']=array('in',$infomationID);
            }
            if(!empty($condition['complete_time'])){
                $a['complete_time']=$condition['complete_time'];
            }
            $deliver[$k]['num']=round($Order->where($a)->count(),2);  //完成订单数量
//            dump($Order->getLastSql());
            $map['person_id']=$v['person_id'];
            $map['canju_status']=2;
//            $map['ord_peisong_status']=4;
            $map['wm_order.status']  = array('in',array(5,6));
            if($infomationID!=null) {
                $map['wm_order.store_id'] = array('in', $infomationID);
            }
            if(!empty($condition['complete_time'])){
                $map['complete_time']=$condition['complete_time'];
            }

            $b['person_id']=$v['person_id'];
            $b['pay_type']=1;
//            $b['ord_peisong_status']=4;
            $b['a.status']  = array('in',array(5,6));
            if($infomationID!=null) {
                $b['a.store_id'] = array('in', $infomationID);
            }
            if(!empty($condition['complete_time'])){
                $b['complete_time']=$condition['complete_time'];
            }
            $deliver[$k]['xszf']=round($Order
                ->alias('a')
                ->join('LEFT JOIN __STORE__ AS b ON b.store_id = a.store_id')
                ->where($b)
                ->sum('total+ps_cost+canju_total-yh_price'),2);  //线上支付金额

            $c['person_id']=$v['person_id'];
            $c['pay_type']=0;
//            $c['ord_peisong_status']=4;
            $c['a.status']  = array('in',array(5,6));
            if($infomationID!=null) {
                $c['a.store_id'] = array('in', $infomationID);
            }
            if(!empty($condition['complete_time'])){
                $c['complete_time']=$condition['complete_time'];
            }
            $deliver[$k]['scss']=round($Order
                ->alias('a')
                ->join('LEFT JOIN __STORE__ AS b ON b.store_id = a.store_id')
                ->where($c)
                ->sum('total+ps_cost+canju_total-yh_price'),2);  //货到付款金额

            $deliver[$k]['ysjje'] = round($deliver[$k]['scss'] -$deliver[$k]['yfsjje'],2);  //应上缴金额

            //求和总计
            $zongji['numtotal']+=$deliver[$k]['num'];
            $zongji['xszfze']+=$deliver[$k]['xszf'];
            $zongji['scss']+=$deliver[$k]['scss'];
            $zongji['ysjjeze']=$zongji['scss']-$zongji['yfsjze'];
        }

        //输出表格需要的数据
        $deliverExcel = $DeliveryPerson-> where($condition)->select();
        foreach($deliverExcel as $kk=>$vv){
            $a['person_id']=$vv['person_id'];
            if(!empty($condition['complete_time'])){
                $a['complete_time']=$condition['complete_time'];
            }
//            $a['ord_peisong_status']=4;
            $a['wm_order.status']  = array('in',array(5,6));
            if(!empty($condition['complete_time'])){
                $a['complete_time']=$condition['complete_time'];
            }
            $deliverExcel[$kk]['num']=$Order->where($a)->count();  //完成订单数量
            $map['person_id']=$vv['person_id'];
            $map['canju_status']=2;
            if(!empty($condition['complete_time'])){
                $map['complete_time']=$condition['complete_time'];
            }
//            $map['ord_peisong_status']=4;
            $map['wm_order.status']  = array('in',array(5,6));
            if($infomationID!=null) {
                $map['wm_order.store_id'] = array('in', $infomationID);
            }
            $b['person_id']=$vv['person_id'];
            if(!empty($condition['complete_time'])){
                $b['complete_time']=$condition['complete_time'];
            }
            $b['pay_type']=1;
//            $b['ord_peisong_status']=4;
            $b['a.status']  = array('in',array(5,6));
            if($infomationID!=null) {
                $b['a.store_id'] = array('in', $infomationID);
            }
            $deliverExcel[$kk]['xszf']=$Order
                ->alias('a')
                ->join('LEFT JOIN __STORE__ AS b ON b.store_id = a.store_id')
                ->where($b)
                ->sum('total+ps_cost+canju_total-yh_price');

            $c['person_id']=$kk['person_id'];
            if(!empty($condition['complete_time'])){
                $c['complete_time']=$condition['complete_time'];
            }
            $c['pay_type']=0;
//            $c['ord_peisong_status']=4;
            $c['a.status']  = array('in',array(5,6));
            if($infomationID!=null) {
                $c['a.store_id'] = array('in', $infomationID);
            }
            $deliverExcel[$kk]['scss']=$Order
                ->alias('a')
                ->join('LEFT JOIN __STORE__ AS b ON b.store_id = a.store_id')
                ->where($c)
                ->sum('total+ps_cost+canju_total-yh_price');

            $deliverExcel[$kk]['ysjje'] = $deliverExcel[$kk]['scss'] -$deliverExcel[$kk]['yfsjje'];
        }

        session('deliverExcel',null);
        session('deliverExcel',$deliverExcel);
        session('zongji',null);
        session('zongji',$zongji);

        $this->assign('deliverman',$deliverman);
        $this->assign('deliver',$deliver);
        $this->assign('page',$show);
        $this->assign('zongji',$zongji);
        $this->assign('person_id',$person_id);
        $this->assign('jintian', $jintian);
        $this->assign('zt', $zt);
        $this->assign('jst', $jst);
        $this->assign('jtTime', $jtTime);
        $this->assign('date', $date);
        $this->display();
    }



    /*
    * 导出EXCEL
    */
    public function deliverexcel(){

        $Model=new \Think\Model();
        $data=session('deliverExcel');
        $zongji=session('zongji');
        $data['zongji']=$zongji;

        foreach($data as $k=>$v) {
            if ($data[$k]['hsgj'] == null) {
                $data[$k]['hsgj'] = 0 ;
            }
            if ($data[$k]['yfsjje'] == null) {
                $data[$k]['yfsjje'] = 0 ;
            }
            if ($data[$k]['xszf'] == null) {
                $data[$k]['xszf'] = 0 ;
            }
            if ($data[$k]['yajin'] == null) {
                $data[$k]['yajin'] = 0 ;
            }
        }

        header("Content-type:application/vnd.ms-excel;charset=UTF-8");
        header("Content-disposition:filename='配送员结算报表".date('Ymd',time()).".xls'");


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
                            <th>姓名</th>
                            <th>完成订单量</th>
                            <th>线上支付金额</th>
                            <th>应上缴金额</th>
                            <th>完成订单总量</th>
                            <th>线上支付总金额</th>
                            <th>应上缴总金额</th>
                            </tr>";
        foreach($data as $v){
            echo "<tr>
                            <td>".$v['person_name']."</td>
                            <td>".$v['num']."</td>
                            <td>".$v['xszf']."</td>
                            <td>".$v['ysjje']."</td>
                            <td>".$v['numtotal']."</td>
                            <td>".$v['xszfze']."</td>
                            <td>".$v['ysjjeze']."</td>
                            </tr>";
        }

        echo "</table>
                     </div>
                 </body>
             </html>";
    }

    //菜品销量统计
    public function  salesVolume(){

        $jintian=I('datevalue');
        $zt=I('startdate');
        $jst=I('overdate');

        $datevalue = strtotime($jintian);
        $startdate = strtotime($zt);
        $overdate = strtotime($jst);
        $definedStart = strtotime(I('definedStart'));
        $definedOver = strtotime(I('definedOver'));
        $date = I('date');
        $storename=I('storename');
        $cpfl=I('cpfl');


        //时间为空判断
        if (!empty($datevalue)) {
            $condition['od.complete_time'] = array('between', array($datevalue, $datevalue + 86400));
        } elseif (!empty($startdate) && !empty($overdate)) {
            $condition['od.complete_time'] = array('between', array($startdate, $overdate + 86400));
        } elseif (!empty($definedStart) && !empty($definedOver)) {
            $condition['od.complete_time'] = array('between', array($definedStart, $definedOver));
        }else{
            //默认为今天数据
            $time=time();
            $jtTime=date("y-m-d",$time);
            $datevalue=strtotime($jtTime);
            $condition['od.complete_time'] =array('between', array($datevalue, $datevalue + 86400));
        }

        //商家store_id为空判断
        if (!empty($storename)) {
            $condition['od.store_id'] = $storename;
        }
        //产品分类为空判断
        if (!empty($cpfl)) {
            $condition['cm.canping_id'] = $cpfl;
        }
        //确定配送已完成状态
        $condition['od.status'] = array('in',array(5,6));
//        $condition['od.ord_peisong_status']=4;

        //判断是否是管理员，管理员显示全部，否则显示当前登陆用户所分配的商家
        //查出前台商家下拉显示
        $restaurant = M('Store');
        $user=M('admin_user');
        $admin=$user->where('id='.UID)->field('user_type,founder_id')->find();
        if($admin['user_type']=='company'){
            $condition21['founder_id']=$admin['founder_id'];
            $store_id = $restaurant->where($condition21)->field('store_id,store_name')->select();     //管理员查询全部商家
        }
        if($admin['user_type']=='company_member'){
            $info12 = $restaurant->where('uid = '.UID)->getField('store_id',true);   //根据当前登录uid查询店铺名称
            $assigned_store=M('assigned_store');
            $info11=$assigned_store->where('user_id='.UID)->getField('store_id',true);

            if($info11 && $info12){
                $infomationID=array_merge($info11,$info12);
                $where['store_id']=array('in',$infomationID);
                $store_id=$restaurant->where($where)->field('store_id,store_name')->select();
            }
            if($info11 && empty($info12)){
                $where['store_id']=array('in',$info11);
                $store_id=$restaurant->where($where)->field('store_id,store_name')->select();
            }
            if($info12 && empty($info11)){
                $where['store_id']=array('in',$info12);
                $store_id=$restaurant->where($where)->field('store_id,store_name')->select();
            }
        }

        foreach($store_id as $k=>$v){
            $a[]=$v['store_id'];
            $b[]=$v['store_name'];
        }

        $id=implode(',',$a);  //获得所分配商家的store_id
        $store=array_combine(array_filter($a),array_filter($b));  //获得所分配商家的store_id和store_name组成一个对应的数组

        if($admin['user_type']=='company_member'){
            $condition['od.store_id']=array('in',$id);  //判断店铺store_id范围
        }

        //分页所需数据条数
        $Orderdetail = M('order_detail');
        $data1 = $Orderdetail->alias('ord')
            ->join("LEFT JOIN __ORDER__ as od ON od.order_id = ord.order_id")
            ->join("LEFT JOIN __CANMING__ as cm ON cm.cm_id = ord.cm_id")
            ->join("LEFT JOIN __CANPIN__ as cp ON cp.canpin_id = cm.canping_id")
            ->field('cm.cm_name as name, cm.cm_id,SUM(ord.quantity) as xiaoliang,cm.now_price as danjia,SUM(ord.quantity)*cm.now_price as zongjia')
            ->where($condition)
            ->group('cm.cm_id')
            ->select();

        $count = count($data1);
        $Page = new \Think\Page($count, 15);
        $Page->setConfig('header', '<span class="rows">共 %TOTAL_ROW% 条记录</span>');
        $Page->setConfig('first', '首页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '末页');
        $Page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $Page->lastSuffix = false;
        $show = $Page->show();


        //显示在前台的具体数据
        $data = $Orderdetail->alias('ord')
            ->join("LEFT JOIN __ORDER__ as od ON od.order_id = ord.order_id")
            ->join("LEFT JOIN __CANMING__ as cm ON cm.cm_id = ord.cm_id")
            ->join("LEFT JOIN __CANPIN__ as cp ON cp.canpin_id = cm.canping_id")
            ->field('cm.cm_name as name, cm.cm_id,SUM(ord.quantity) as xiaoliang,cm.now_price as danjia,SUM(ord.quantity)*cm.now_price as zongjia')
            ->where($condition)
            ->group('cm.cm_id')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();

        //前台总计数据显示
        $zongji['xlzj']=0;
        $zongji['djzj']=0;
        $zongji['zezj']=0;
        foreach($data1 as $k=>$v){
            $zongji['xlzj']+=$v['xiaoliang'];
            $zongji['djzj']+=$v['danjia'];
            $zongji['zezj']+=$v['zongjia'];
        }


        //导出表格所需的数据
        $exceldata = $Orderdetail->alias('ord')
            ->join("LEFT JOIN __ORDER__ as od ON od.order_id = ord.order_id")
            ->join("LEFT JOIN __CANMING__ as cm ON cm.cm_id = ord.cm_id")
            ->join("LEFT JOIN __CANPIN__ as cp ON cp.canpin_id = cm.canping_id")
            ->field('cm.cm_name as name, cm.cm_id,SUM(ord.quantity) as xiaoliang,cm.now_price as danjia,SUM(ord.quantity)*cm.now_price as zongjia')
            ->where($condition)
            ->group('cm.cm_id')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();

        session('$exceldata',null);
        session('exceldata',$exceldata);
        session('zongji',null);
        session('zongji',$zongji);

        $this->assign('store', $store);
        $this->assign('data', $data);
        $this->assign('date', $date);
        $this->assign('page', $show);
        $this->assign('zongji', $zongji);
        $this->assign('store_id', $storename);
        $this->assign('cpfl', $cpfl);
        $this->assign('definedStart', $definedStart);
        $this->assign('definedOver', $definedOver);
        $this->assign('jintian', $jintian);
        $this->assign('zt', $zt);
        $this->assign('jst', $jst);
        $this->assign('jtTime', $jtTime);
        $this->display();
    }


    public function salesexcel(){

        $Model=new \Think\Model();
        $data=session('exceldata');
        $zongji=session('zongji');
        $data['zongji']=$zongji;

        header("Content-type:application/vnd.ms-excel;charset=UTF-8");
        header("Content-disposition:filename='配送员结算报表".date('Ymd',time()).".xls'");


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
                            <th>菜品名称</th>
                            <th>销售数量</th>
                            <th>销售单价</th>
                            <th>销售总额</th>
                            <th>销售量总计</th>
                            <th>销售单价总计</th>
                            <th>销售额总计</th>
                            </tr>";
        foreach($data as $v){
            echo "<tr>
                            <td>".$v['name']."</td>
                            <td>".$v['xiaoliang']."</td>
                            <td>".$v['danjia']."</td>
                            <td>".$v['zongjia']."</td>
                            <td>".$v['xlzj']."</td>
                            <td>".$v['djzj']."</td>
                            <td>".$v['zezj']."</td>
                            </tr>";
        }

        echo "</table>
                     </div>
                 </body>
             </html>";
    }

    public function getFenlei(){
        $id=I('id');
        $Canpin=M('canpin');
        $info=$Canpin->where('store_id='.$id)->select();
        $this->ajaxReturn($info);
    }

    public function agentsales(){

        $date = I('date');
        $jintian=I('datevalue');
        $zt=I('startdate');
        $jst=I('overdate');

        $datevalue = strtotime($jintian);
        $startdate = strtotime($zt);
        $overdate = strtotime($jst);
        $definedStart = strtotime(I('definedStart'));
        $definedOver = strtotime(I('definedOver'));
        $store_id=I('store_id');

        if(!empty($datevalue)) {
            $condition['complete_time']=array('between',array($datevalue,$datevalue+86400));
            $condition2['a.complete_time']=array('between',array($datevalue,$datevalue+86400));
        }elseif(!empty($startdate)&&!empty($overdate)) {
            $condition['complete_time']=array('between',array($startdate,$overdate+86400));
            $condition2['a.complete_time']=array('between',array($startdate,$overdate+86400));
        }elseif(!empty($definedStart)&&!empty($definedOver)){
            $condition['complete_time']=array('between',array($definedStart,$definedOver));
            $condition2['a.complete_time']=array('between',array($definedStart,$definedOver));
        }else{
            //默认为今天数据
            $time=time();
            $jtTime=date("y-m-d",$time);
            $datevalue=strtotime($jtTime);
            $condition['complete_time'] =array('between', array($datevalue, $datevalue + 86400));
            $condition2['a.complete_time'] =array('between', array($datevalue, $datevalue + 86400));
        }

        $user = M('admin_user');
        $Store=M('store');

        //代理商登陆
        if(is_agent()){
            //获得代理下的公司id
            $uid=is_login();
            $condition9['from_agent_id']=$uid;
            $condition9['user_type']='company';
            $admin=$user->where($condition9)->getField('founder_id',true);
            if($admin){
                //有公司的话获取admin_user表里公司下的商家的id
                    $condition10['u.founder_id']=array('in',$admin);
                    $condition10['u.user_type']='company_member';
                    $shangjia=$user->alias('u')->where($condition10)->getField('id',true);
                //如果有商家的话到store表里获取store_id
                if($shangjia){
                    $condition11['uid']=array('in',$shangjia);
                    $shangjia_id=$Store->where($condition11)->getField('store_id',true);
//                    dump($shangjia_id);
                    $condition8['store_id']=array('in',$shangjia_id);
                    $info3=$Store->where($condition8)->field('store_id,store_name')->select();

                    if(!empty($store_id)) {
                        $condition['store_id']=$store_id;
                        $condition2['a.store_id']=$store_id;
                    }else{
                        $condition['store_id']=array('in',$shangjia_id);
                        $condition2['a.store_id']=array('in',$shangjia_id);
                    }
                }
            }
        }

//        $condition2['ord_peisong_status']=4;
        $condition2['a.status']  = array('in',array(5,6));

        $Order=M('Order');
        if($info3){
            $count = $Store->where($condition)->count();
//        dump($count);
            $Page = new \Think\Page($count,15);
            //分页设置

            $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
            $Page->setConfig('first','首页');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('last','末页');
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $Page->lastSuffix = false;  //分页最后的总页数不显示

            $show = $Page->show();

            $store =M('store')->where($condition)->limit($Page->firstRow.','.$Page->listRows)->select();
            $order =$Order
                ->alias('a')
                ->join('LEFT JOIN __STORE__ AS b ON b.store_id = a.store_id')
                ->where($condition2)
                ->order('order_id desc')
                ->select();
//        dump($condition2);

            $zongji['numtotal']=0;
            $zongji['heji']=0;
            $zongji['choucheng']=0;
            foreach($store as $k=>$v){
                //设置初始值为零
                $total=0;
                $store[$k]['num']=0;
                $store[$k]['total']=0;
                $store[$k]['total_money']=0;
                $store[$k]['store_rebate']=0;
                foreach($order as $kk=>$vv){
                    if($v['store_id']==$vv['store_id']){
                        $total++;
                        $store[$k]['total']+=round($vv['total'],2); //菜品原价
                        $store[$k]['store_rebate']+=round($vv['total']*$vv['store_rebate']/100,2); //抽成金额
                    }
                }
                $store[$k]['num']=$total; //订单量
                $store[$k]['total_money']=round($store[$k]['total']+$store[$k]['peisong_total']+$store[$k]['canju_total']-$store[$k]['yh_price'],2);//订单金额
            }

            $zongji['numtotal']= round($Order->alias('a')->where($condition2)->count(),2); //订单量总额
            $zongji['heji']=round($Order->alias('a')->where($condition2)->sum('ps_cost+total+canju_total-yh_price'),2); //订单总金额
            $zongji['choucheng']=round($Order
                ->alias('a')
                ->join('LEFT JOIN __STORE__ AS b ON b.store_id = a.store_id')
                ->where($condition2)
                ->sum('total*store_rebate/100'),2); //抽成总额

            //输出表格需要的数据
            $storeExcel =M('store')->where($condition)->select();
            foreach($storeExcel as $key=>$value){
                //设置初始值为零
                $total=0;
                $storeExcel[$key]['num']=0;
                $storeExcel[$key]['total']=0;
                $storeExcel[$key]['total_money']=0;
                $storeExcel[$key]['store_rebate']=0;
                foreach($order as $kk=>$vv){
                    if($value['store_id']==$vv['store_id']){
                        $total++;
                        $storeExcel[$key]['total']+=$vv['total']; //菜品原价
                        $storeExcel[$key]['store_rebate']+=$vv['total']*$vv['store_rebate']/100; //抽成金额
                    }
                }
                $storeExcel[$key]['num']=$total; //订单量
                $storeExcel[$key]['total_money']=$store[$key]['total']+$store[$key]['peisong_total']+$store[$key]['canju_total']-$store[$key]['yh_price'];//订单金额
            }

        }

        session('storeExcel',null);
        session('storeExcel',$storeExcel);
        session('zongji',null);
        session('zongji',$zongji);

        $this->assign('info',$info3);
        $this->assign('page',$show);
        $this->assign('store',$store);
        $this->assign('zongji',$zongji);
        $this->assign('store_id',$store_id);
        $this->assign('date',$date);
        $this->assign('store_id',$store_id);
        $this->assign('jintian', $jintian);
        $this->assign('zt', $zt);
        $this->assign('jst', $jst);
        $this->assign('jtTime', $jtTime);
        $this->display();
    }

    public function agentsalesexcel(){

        $Model=new \Think\Model();
        $data=session('exceldata');
        $zongji=session('zongji');
        $data['zongji']=$zongji;

        header("Content-type:application/vnd.ms-excel;charset=UTF-8");
        header("Content-disposition:filename='配送员结算报表".date('Ymd',time()).".xls'");


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
                            <th>菜品名称</th>
                            <th>销售数量</th>
                            <th>销售单价</th>
                            <th>销售总额</th>
                            <th>销售量总计</th>
                            <th>销售单价总计</th>
                            <th>销售额总计</th>
                            </tr>";
        foreach($data as $v){
            echo "<tr>
                            <td>".$v['name']."</td>
                            <td>".$v['xiaoliang']."</td>
                            <td>".$v['danjia']."</td>
                            <td>".$v['zongjia']."</td>
                            <td>".$v['xlzj']."</td>
                            <td>".$v['djzj']."</td>
                            <td>".$v['zezj']."</td>
                            </tr>";
        }

        echo "</table>
                     </div>
                 </body>
             </html>";
    }
}

?>       