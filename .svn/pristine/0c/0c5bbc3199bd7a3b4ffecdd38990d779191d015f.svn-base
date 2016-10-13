<?php
namespace Admin\Controller;
use Think\Model;
//数据监测控制器
class DatamonitorController extends AdminController
{


    public function index(){
        $company =M('company');
        $order =M('order');
        $yewuyuan = M('admin_user');
        //客户数量
        $kehu = $company->count();
        if(is_agent()){
            $kehu = $company->where('from_agent_id='.is_login())->count();
        }
        //试用客户数量
        $notsign['agreement_status']=0;
        $notsignkehu = $company->where($notsign)->count();
        if(is_agent()){
            $notsign['from_agent_id']=is_login();
            $notsignkehu = $company->where($notsign)->count();
        }
        //签约客户数量
        $sign['agreement_status']=1;
        $signkehu = $company->where($sign)->count();
        if(is_agent()){
            $sign['from_agent_id']=is_login();
            $signkehu = $company->where($sign)->count();
        }
        //总订单数量
        $ordzong = $order->count();
        if(is_agent()){
            $agent['from_agent_id']=is_login();
            $com = $company->where($agent)->getField('id',true);
            if(!is_array($com)) {
                $com = md5('没有数据');
            }
            $comp['founder_id']=array('in',$com);
            $ordzong = $order->where($comp)->count();
        }
        //快速下单数量
        $quickly['from_operation']=array('in','4,5');
        $ordquickly = $order->where($quickly)->count();
        if(is_agent()){
            $quickly['founder_id']=array('in',$com);
            $quickly['from_operation']=array('in','4,5');
            $ordquickly = $order->where($quickly)->count();
        }
        //在线下单数量
        $online['from_operation']=array('in','1,2,3');
        $ordonline = $order->where($online)->count();
        if(is_agent()){
            $online['founder_id']=array('in',$com);
            $online['from_operation']=array('in','1,2,3');
            $ordonline = $order->where($online)->count();
        }
        //当日订单量
        $date['ben_day_tou'] = mktime(0, 0, 0, date("m"),  date("d"), date("Y"));//当天的头
        $date['ben_day_wei'] = mktime(23, 59, 59, date("m"), date("d"), date("Y"));//当天的尾
        $ben_day['xd_time'] = array('between', array($date['ben_day_tou'],$date['ben_day_wei']));
        $ordday = $order->where($ben_day)->count();
        if(is_agent()){
            $ben_day['founder_id']=array('in',$com);
            $ben_day['xd_time'] = array('between', array($date['ben_day_tou'],$date['ben_day_wei']));
            $ordday = $order->where($ben_day)->count();
        }
        //昨日订单量
        $date['last_day_tou'] = mktime(0, 0, 0, date('m'), date('d', strtotime('-1 day')), date("Y"));//昨天的头
        $date['last_day_wei'] = mktime(23, 59, 59, date('m'), date('d', strtotime('-1 day')), date("Y"));//昨天的尾
        $last_day['xd_time'] = array('between', array($date['last_day_tou'],$date['last_day_wei']));
        $ordlastday = $order->where($last_day)->count();
        if(is_agent()){
            $last_day['founder_id']=array('in',$com);
            $last_day['xd_time'] = array('between', array($date['last_day_tou'],$date['last_day_wei']));
            $ordlastday = $order->where($last_day)->count();
        }
        //本周订单量
        $xingqi = date('N',time());//获取星期中的第几天，1（表示星期一）到7（表示星期天）
        if($xingqi == 1){//如果今天是星期一
            $date['week_tou'] = mktime(0, 0, 0, date("m"),  date("d"), date("Y"));//当天的头
        }else{
            $date['week_tou'] = strtotime("last Mon");//上星期一的头
        }
        if($xingqi==7){//如果今天是星期日
            $date['week_wei'] = mktime(23, 59, 59, date("m"),  date("d"), date("Y"));//当天的尾
        }else{
            $date['week_wei'] = strtotime("next Sun +86399seconds");//下星期日的尾
        }
        $week['xd_time'] = array('between', array($date['week_tou'],$date['week_wei']));
        $ordweek = $order->where($week)->count();
        if(is_agent()){
            $week['founder_id']=array('in',$com);
            $week['xd_time'] = array('between', array($date['week_tou'],$date['week_wei']));
            $ordweek = $order->where($week)->count();
        }
        //本月订单量
        $date['ben_month_tou'] = mktime(0, 0, 0, date("m"), 1, date("Y"));//当前月份的头
        $date['ben_month_wei'] = mktime(23, 59, 59, date("m"), date("t"), date("Y"));//当前月份的尾
        $month['xd_time'] = array('between', array($date['ben_month_tou'],$date['ben_month_wei']));
        $ordmonth = $order->where($month)->count();
        if(is_agent()){
            $month['founder_id']=array('in',$com);
            $month['xd_time'] = array('between', array($date['ben_month_tou'],$date['ben_month_wei']));
            $ordmonth = $order->where($month)->count();
        }
        //上月订单量
        $date['last_month_tou'] = mktime(0, 0, 0, date('m', strtotime('-1 month')), 1, date("Y"));//上月份的头
        $date['last_month_wei'] = mktime(23, 59, 59, date('m', strtotime('-1 month')), date("t", strtotime('-1 month')), date("Y"));//上月份的尾
        $last_month['xd_time'] = array('between', array($date['last_month_tou'],$date['last_month_wei']));
        $ordlastmonth = $order->where($last_month)->count();
        if(is_agent()){
            $last_month['founder_id']=array('in',$com);
            $last_month['xd_time'] = array('between', array($date['last_month_tou'],$date['last_month_wei']));
            $ordlastmonth = $order->where($last_month)->count();
        }
        //上月每日平均订单量
        $date['last_month_days'] =  date("t", strtotime('-1 month'));//上月份天数
        $ordlastmonthpingjun = $ordlastmonth/$date['last_month_days'];
        $ordlastmonthjun = round($ordlastmonthpingjun,2);

        //获取这个月的每一天存入数组
        if(is_admin()){
            for ($i=1; $i <=date('t',time()) ; $i++){
                $thismonth_days[] = $i;
                $begin = mktime(0, 0, 0, date('m'),$i, date("Y"));
                $end = mktime(23, 59, 59, date('m'),$i, date("Y"));
                $everyday['xd_time'] = array('between', array($begin,$end));
                $ordeveryday = $order->where($everyday)->count();//查询每一天的订单量
                $ordeverydayarr[] = $ordeveryday;
            }
        }
        else if(is_agent()){
            for ($i=1; $i <=date('t',time()) ; $i++){
                $thismonth_days[] = $i;
                $begin = mktime(0, 0, 0, date('m'),$i, date("Y"));
                $end = mktime(23, 59, 59, date('m'),$i, date("Y"));
                $everyday['xd_time'] = array('between', array($begin,$end));
                $everyday['founder_id']=array('in',$com);
                $ordeveryday = $order->where($everyday)->count();//查询每一天的订单量
                $ordeverydayarr[] = $ordeveryday;
            }
        }
        //查询业务员信息
        $wheresa['user_type'] ='direct_sales';
        $wherein['user_type'] = 'software_install';
        if(is_agent()){
            $wheresa['user_type'] ='direct_sales';
            $wheresa['from_agent_id'] =is_login();
            $ywysa = $yewuyuan->where($wheresa)->field('nickname,id')->select();
        }else{
            $ywysa = $yewuyuan->where($wheresa)->field('nickname,id')->select();
        }
        if(is_agent()){
            $wherein['user_type'] = 'software_install';
            $wherein['from_agent_id'] =is_login();
            $ywyin = $yewuyuan->where($wherein)->field('nickname,id')->select();
        }else{
            $ywyin = $yewuyuan->where($wherein)->field('nickname,id')->select();
        }
        foreach ($ywysa as $k=>$v){
            $wherekesa['sale_id'] = $v['id'];
            $kenumsa = M('company')->where($wherekesa)->getField('id',true);
            $ywysa[$k]['kenumsa'] = count($kenumsa);//业务员的客户数量
            $ywysaord = 0;
            for($i=0;$i<count($kenumsa);$i++){
                $whereordsa['founder_id'] = array('in',$kenumsa);
                $ordnumsa = M('order')->where($whereordsa)->count();
                $ywysaord+=$ordnumsa;
            }
            $ywysa[$k]['ordnumsa'] = $ywysaord;
        }

        foreach ($ywyin as $k=>$v){
            $wherekein['software_install_id'] = $v['id'];
            $kenumin = M('company')->where($wherekein)->getField('id',true);
            $ywyin[$k]['kenumin'] = count($kenumin);//业务员的客户数量
            $ywysaoin = 0;
            for($i=0;$i<count($kenumin);$i++){
                $whereordin['founder_id'] = $kenumin[$i];
                $ordnumin = M('order')->where($whereordin)->count();
                $ywysaoin+=$ordnumin;
            }
            $ywyin[$k]['ordnumin'] = $ywysaoin;
        }
        //dump($ywysa);dump($ywyin);

        $this->assign('thismonth',date('Y年m月',time()));
        $this->assign('thismonth_days',json_encode($thismonth_days));
        $this->assign('thismonth_data',json_encode($ordeverydayarr));

        $this->assign('kehu',$kehu);
        $this->assign('notsignkehu',$notsignkehu);
        $this->assign('signkehu',$signkehu);
        $this->assign('ordzong',$ordzong);
        $this->assign('ordquickly',$ordquickly);
        $this->assign('ordonline',$ordonline);

        $this->assign('ordday',$ordday);
        $this->assign('ordlastday',$ordlastday);
        $this->assign('ordweek',$ordweek);
        $this->assign('ordmonth',$ordmonth);
        $this->assign('ordlastmonth',$ordlastmonth);
        $this->assign('ordlastmonthjun',$ordlastmonthjun);
        $this->assign('ywysa',$ywysa);
        $this->assign('ywyin',$ywyin);
        //dump($date);
        $this->display();
    }
    public  function get_used_status(){
        $fp = popen('top -b -n 2 | grep -E "^(Cpu|Mem|Tasks)"',"r");//获取某一时刻系统cpu和内存使用情况
        $rs = "";
        while(!feof($fp)){
            $rs .= fread($fp,1024);
        }
        pclose($fp);
        $sys_info = explode("\n",$rs);
        $cpu_info = explode(",",$sys_info[4]);  //CPU占有量  数组
        $mem_info = explode(",",$sys_info[5]); //内存占有量 数组

        //CPU占有量
        $cpu_usage = trim(trim($cpu_info[0],'Cpu(s): '),'%us');  //百分比

        //内存占有量
        $mem_total = trim(trim($mem_info[0],'Mem: '),'k total');
        $mem_used = trim($mem_info[1],'k used');
        $mem_usage = round(100*intval($mem_used)/intval($mem_total),2);  //百分比

        $arr =  array('cpu_usage'=>$cpu_usage.'%','mem_usage'=>$mem_usage.'%','c'=>round($cpu_usage,1),'m'=>$mem_usage);
        $this->ajaxReturn($arr);
    }
}