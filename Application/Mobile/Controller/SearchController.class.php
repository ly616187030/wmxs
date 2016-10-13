<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/8/10 0010
 * Time: 下午 2:47
 */

namespace Mobile\Controller;
use Think\Controller;


class SearchController extends Controller{
    public function storeSearch(){
        $key=I('get.key','');

        $store = M('store');
        $lng = session('lng');
        $lat = session('lat');
        $cityName=session('cityName');
        $qy_id=$_SESSION['companyid'];
        //$cityName=A('Index')->trimAll($cityName);
        //$code = M('address_city')->where("name='$cityName'")->field('code')->find();
        $map['wm_store.status']=1;
        $map['wm_store.yingye_flag']=1;
        //$map['wm_store.city_code']=$code['code'];
        $map['wm_store.store_name|wm_store.store_name_py']=array('like',"%$key%");
        $map['wm_store.founder_id']=$qy_id;
        if(empty($key)){
            exit;
    }
        $p=$store->join('LEFT JOIN wm_picture ON wm_store.store_logo_id = wm_picture.id')
            //->join('LEFT JOIN wm_city ON wm_store.city_code = wm_city.city_code')
            ->where($map)
            ->field('wm_store.*,wm_picture.path')
            ->select();
        $res=array();
        foreach($p as $k=>$v){
            $jwd=explode(',',$v['lng_lat']);
            $distance=distanceBetween($lng,$lat,$jwd[0],$jwd[1]);
            $peisong=explode(',',$v['peisong_price']);
            if($distance/1000<=substr(end($peisong),0,strpos(end($peisong),"-"))){
                for( $i=0;$i<count($peisong);$i++) {
                    if($distance/1000<substr($peisong[$i],0,strpos($peisong[$i],"-"))){
                        $peisongfei=substr($peisong[$i],strpos($peisong[$i],"-")+1,count($peisong));
                        $v['peisongfei']=$peisongfei;
                        break;
                    }
                }
                if($qy_id){
                    //营业时间
                    $timea = explode(',', $v['s_time']);
                    $timeb = explode(',', $v['e_time']);
                    $timeaa = array($timea[0] . ':' . $timea[1], $timea[2] . ':' . $timea[3]);
                    $timebb = array($timeb[0] . ':' . $timeb[1], $timeb[2] . ':' . $timeb[3]);
                    $stime = implode(',',$timeaa);
                    $etime = implode(',',$timebb);

                    //查图片路径
                    $picture=M('picture');
                    if($v['store_logo_id']){
                        $v['lujing']=$picture->where('id='.$v['store_logo_id'])->field('path')->find();
                    }else{
                        $v['lujing']=$picture->where('id='.C('WEB_SITE_STORE_PICTURE'))->field('path')->find();
                    }
//                        $v['lujing']=$picture->where('id='.$v['store_logo_id'])->field('path')->find();
//                        if($v['lujing']==null){
//                            $v['lujing']['path']="/./Application/Mobile/View/Public/img/zhg_common_pic.jpg";
//                        }
                    $where['store_id']=$v['store_id'];
                    $where['year']=date("Y",time());
                    $where['month']=date("n",time());
                    $sum = M('order')->where($where)->Sum('total')/ M('order')->where($where)->count('order_id');
                    $quantity=M("GoodsCount")->where($where)->sum('quantity');
                    $v['sale_num']=empty($quantity)?0:$quantity;
                    $v['is_open']=is_open($stime,$etime);
                    $v['youhui']=A('index')->get_youhui($v['store_id']);
                    $pf=M("pingjia")->where("store_id=".$v['store_id'])->avg("pingfen");
                    $v['pingfen']=empty($pf)?0:($pf/5)*100;
                    $v['renjun']=$v['qisong_price']/2;
                    $v['yingye_is_two']=yingye_is_two($v['s_time']);
                    $v['juli'] = $distance/1000;
                    $v['url']=U('Menu/index',array('store_id'=>$v['store_id'],'peisongfei'=>$peisongfei,'is_open'=>is_open($stime,$etime)));
                    $res[]=$v;
                }
            }
        }
        $this->ajaxReturn($res);
    }

    /*public function commoditySearch() {
        $key=I('get.key','');
        $last=I('post.last','');
        $amount=I('post.amount','');
        $store = M('canming');
        $lng = session('lng');
        $lat = session('lat');
        $cityName=session('cityName');
        $cityName=A('Index')->trimAll($cityName);
        $code = M('address_city')->where("name='$cityName'")->field('code')->find();
        $map['wm_store.status']=1;
        $map['wm_store.yingye_flag']=1;
        $map['wm_store.city_code']=$code['code'];
        $map['wm_canming.cm_name']=array('like',"%$key%");
        $p=$store->join('LEFT JOIN wm_picture ON wm_canming.big_img = wm_picture.id')
            ->join('LEFT JOIN wm_store ON wm_store.store_id = wm_canming.store_id')
            ->join('LEFT JOIN wm_city ON wm_store.city_code = wm_city.city_code')
            ->where($map)
            ->field('wm_store.*,wm_picture.path,wm_canming.*')
            ->limit($last,$amount)
            ->select();
        $count=count($p);
        $open = C('OPENTIME');
        $open = explode('-',$open);

        foreach($p as $k=>$v){
            //$cx_store[$k]['juli']=distanceBetween($lng,$lat,$v['lng'],$v['lat'])/1000;
            $s1 = MPointInPoly($lng,$lat,$v['distribution_range']);
            if($s1==true){
                $where['cm_id']=$v['cm_id'];
                $where['year']=date("Y",time());
                $where['month']=date("n",time());
                //$sum = M('order')->where($where)->Sum('total')/ M('order')->where($where)->Count('order_id');
                $quantity=M("GoodsCount")->where($where)->sum('quantity');
                $v['sale_num']=empty($quantity)?0:$quantity;
                //$v['is_open']=is_open($open[0],$open[1]);
                //$v['youhui']=A('Index')->get_youhui($v['store_id']);
                //$v['yingye_is_two']=yingye_is_two($v['s_time']);
                $v['url']=U('Menu/index',array('store_id'=>$v['store_id']));
                $res[]=$v;
            }
        };
        $totalCommodity=array($res,$count);
        $this->ajaxReturn($totalCommodity, 'JSON');
    }*/

    public function search(){
        $key=I('post.key','');
        if($_GET){
            $cityName=$_GET['cityName'];
            $lng = $_GET['lng'];
            $lat = $_GET['lat'];
            session('lng',$lng);
            session('lat',$lat);
            session('cityName',$cityName);
        }
        $this->assign('key',$key);
        $this->display();
   }

}

?>