<?php
namespace Mobile\Controller;
use Think\Controller;
class IndexController extends MobileController
{
    private $oauth;
    private $appId;
    private $appSecret;
    private $wx;

    public function _initialize(){
        parent::_initialize();
        //import('Vendor.WechatPhpSdk.Wx');
    }

    public function getWxConfig($companyid){

        $map['founder_id'] = $companyid;
        $data = M('weixin_pay')->where($map)->find();
        if(!empty($data) && strlen($data['appsecret']) == 32){
            $options = array(
                'appid'=>$data['appid'], //填写高级调用功能的app id
                'appsecret'=>$data['appsecret'] //填写高级调用功能的密钥
            );
        }else{
            $options = null;
        }

        return $options;
    }
    public function getFollow()
    {
        import('Vendor.WechatPhpSdk.Wx');
        $companyid = I('companyid');
        $condition = $companyid?:$_SESSION['companyid'];
        if(!empty($companyid)){
            $_SESSION['companyid']=$companyid;
            session('city',null);
            session('addrtitle',null);
            session('addrjingdu',null);
            session('addrweidu',null);
            session('lianxiren',null);
            session('sex',null);
            session('address',null);
            session('menpai',null);
            session('phone',null);
            session('jingdu',null);
            session('weidu',null);
            session('username',null);
        }
        $config = $this->getWxConfig($condition);
        if($config==null){
            $oauth = "http://{$_SERVER['HTTP_HOST']}/index.php?s=/Index/remind";
        }else{
            $urls = "http://{$_SERVER['HTTP_HOST']}/index.php?s=/Index/index";
            $wx=new \TPWechat($config);
            $oauth = $wx->getOauthRedirect($urls);
        }

        Header("Location:$oauth");
    }
    public function index(){

        if(IS_POST){
            $zddd_lng=I('post.lng');  //自动定位完成传回的经纬度
            $zddd_lat=I('post.lat');
            $qy_id=I('post.qy_id');

            $store = M('store');
            $map2['wm_store.status']=1;
            $map2['wm_store.yingye_flag']=1;
            $map2['wm_store.founder_id']=$qy_id;
            $cx_store = $store->join('LEFT JOIN wm_picture ON wm_store.store_logo_id = wm_picture.id')
                ->where($map2)
                ->field('wm_store.*,wm_picture.path')
                ->select();

            foreach($cx_store as $k=>$v){
                $jwd=explode(',',$v['lng_lat']);
                $distance=distanceBetween($zddd_lng,$zddd_lat,$jwd[0],$jwd[1]);
                $peisong=explode(',',$v['peisong_price']);
                if($distance/1000<=substr(end($peisong),0,strpos(end($peisong),"-"))){
                    for( $i=0;$i<count($peisong);$i++) {
                        if($distance/1000<substr($peisong[$i],0,strpos($peisong[$i],"-"))){
                            $v['peisongfei']=substr($peisong[$i],strpos($peisong[$i],"-")+1,count($peisong));
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
                        $v['youhui']=$this->get_youhui($v['store_id']);
                        $pf=M("pingjia")->where("store_id=".$v['store_id'])->avg("pingfen");
                        $v['pingfen']=empty($pf)?0:($pf/5)*100;
                        $v['renjun']=$v['qisong_price']/2;
                        $v['yingye_is_two']=yingye_is_two($v['s_time']);
                        $v['juli'] = $distance/1000;
                        $res[]=$v;
                    }
                }
            }

            ksort($res);
            $this->ajaxReturn($res);
        }else{
            $qy_id = $_SESSION['companyid'];

            $config = $this->getWxConfig($qy_id);

            import('Vendor.WechatPhpSdk.Wx');
            $wx=new \TPWechat($config);
            $re = $wx->getOauthAccessToken();
            $output = $wx->getOauthUserinfo($re['access_token'],$re['openid']);
            if(!empty($output)){
                $member_user = M('member_user');
                $map['username'] = $output['openid'];
                $mot = $member_user->where($map)->find();
                if(!empty($mot)){
                    $member_user->where($map)->setField('update_time',time());
                    $user_object = D('User/User');
                    $id = $user_object->login($output['openid'], $output['openid']);
                }else{
                    if(!empty($output)){
                        $m['nickname'] = $output['nickname'];
                        $m['username'] = $output['openid'];
                        $m['password'] = user_md5($output['openid']);
                        $m['reg_type'] = 'mobile';
                        $m['mobile'] = null;
                        $m['avatar'] = $output['headimgurl'];
                        $m['create_time'] = time();
                        $m['user_type'] = 0;
                        $m['status'] = 1;
                        $m['push_msg_id']=date('YmdHis',time());
                        $m['founder_id']=$qy_id;
                        if($member_user->add($m)){
                            $user_object = D('User/User');
                            $user_object->login($output['openid'], $output['openid']);
                        }
                    }

                }
            }else{
                $this->getFollow();
            }

            $map1['id'] = is_member_login();
            if($member_user->where($map1)->find())
                $member_user->where($map1)->setField('update_time',time());

            $cityName = session('city');
            $addrtitle = session('addrtitle');
            $addrjingdu = session('addrjingdu');
            $addrweidu = session('addrweidu');


            //cookie('companyid',$qy_id);
            //session('companyid',$qy_id);
            $where1['founder_id']=$qy_id;
            $where1['flag']=1;

            //首页轮播图片
            $weixin=M('lunbo_weixin');
            if($qy_id) {
                $lunbo = $weixin->where($where1)->select();
            }

            //首页客服电话
            $other_weixin=M('other_weixin');
            if($qy_id) {
                $other = $other_weixin->where($where1)->find();
            }

            //首页分类显示
            $store_category = M("store_category");
            if($qy_id){
                $data=$store_category->where($where1)->order('store_c_id asc ')->select();
            }

            //首页特价专区显示
            $where2['founder_id']=$qy_id;
            $where2['flag']=1;
            $specialarea=M('specialarea');
            $info=$specialarea->where($where2)->select();
        }

        $this->assign('city',$cityName);
        $this->assign('addrtitle',$addrtitle);
        $this->assign('lng',$addrjingdu);
        $this->assign('lat',$addrweidu);

        $this->assign('red','red');
        $this->assign('data',$data);
        $this->assign('qy_id',$qy_id);
        $this->assign('info',$info);
        $this->assign('lunbo',$lunbo);
        $this->assign('other',$other);
        $this->display();
    }

    public function remind(){
        header('Content-Type:text/html; charset=utf-8');
        exit('<h2 style="margin:50% auto;font-size:60px;">请登录到后台->系统->微信设置->微信支付设置，填写完整相关参数！</h2>');
    }
    public function store(){
        $store = M('store');
        $store_name=I('fenlei');
        $qy_id=I('qy_id');
        $fenlei_id=I('fenlei_id');
        if($_GET){
            $cityName=$_GET['cityName'];
            $cityName=$this->trimAll($cityName);
//            $code = M('address_city')->where("name='$cityName'")->field('code')->find();
            $lng = I('get.lng');
            $lat = I('get.lat');
            $num = $_GET['num'];

            $map['wm_store.status']=1;
            $map['wm_store.yingye_flag']=1;
            $map['wm_store.founder_id']=$qy_id;
            if($fenlei_id){
                $map['wm_store.store_c_id']=$fenlei_id;
            }
            session('lng',$lng);
            session('lat',$lat);
            session('cityName',$cityName);
            $this->assign('reinfo',array('cityName'=>session('cityName'),'lng'=>session('lng'),'lat'=>session('lat')));
            if($num){
                $store_name=$_GET['store_name'];
                $_SESSION['num']=$num;
                if($num == 4){
                    $cx_store = $store->join('LEFT JOIN wm_picture ON wm_store.store_logo_id = wm_picture.id')
                        ->where($map)
                        ->field('wm_store.*,wm_picture.path')
                        ->select();

                    foreach($cx_store as $k=>$v){
                        $jwd=explode(',',$v['lng_lat']);
                        $distance=distanceBetween($lng,$lat,$jwd[0],$jwd[1]);
                        $peisong=explode(',',$v['peisong_price']);
                        if($distance/1000<=substr(end($peisong),0,strpos(end($peisong),"-"))){
                            for( $i=0;$i<count($peisong);$i++) {
                                if($distance/1000<substr($peisong[$i],0,strpos($peisong[$i],"-"))){
                                    $v['peisongfei']=substr($peisong[$i],strpos($peisong[$i],"-")+1,count($peisong));
                                    break;
                                }
                            }

                            //营业时间
                            $timea = explode(',', $v['s_time']);
                            $timeb = explode(',', $v['e_time']);
                            $timeaa = array($timea[0] . ':' . $timea[1], $timea[2] . ':' . $timea[3]);
                            $timebb = array($timeb[0] . ':' . $timeb[1], $timeb[2] . ':' . $timeb[3]);
                            $stime = implode(',',$timeaa);
                            $etime = implode(',',$timebb);
                            if($qy_id){
                                $where['store_id']=$v['store_id'];

                                $where['year']=date("Y",time());
                                $where['month']=date("n",time());
                                $sum = M('order')->where($where)->Sum('total')/ M('order')->where($where)->count('order_id');
                                $quantity=M("GoodsCount")->where($where)->sum('quantity');
                                $v['sale_num']=empty($quantity)?0:$quantity;
                                $v['is_open']=is_open($stime,$etime);
                                $v['youhui']=$this->get_youhui($v['store_id']);
                                $pf=M("pingjia")->where("store_id=".$v['store_id'])->avg("pingfen");
                                $v['pingfen']=empty($pf)?0:($pf/5)*100;
                                $v['renjun']=$v['qisong_price']/2;
                                $v['yingye_is_two']=yingye_is_two($v['s_time']);
                                $v['juli'] = $distance/1000;
                                $res[]=$v;
                            }
                        }
                    }
                    ksort($res);
                    $this->assign('aaa','aaa');
                    $this->assign('store',$res);
                    $this->assign('store_name',$store_name);
                }else{
                    if($num == 1){
                        $this->assign('aaa1','aaa');
                        $order = 'pingfen desc';
                        $this->assign('store_name',$store_name);
                    }
                    if($num == 2){
                        $this->assign('aaa2','aaa');
                        $order = 'qisong_price asc';
                        $this->assign('store_name',$store_name);
                    }
                    if($num == 3){
                        $this->assign('aaa3','aaa');
                        $order = 'sh_time asc';
                        $this->assign('store_name',$store_name);
                    }
                    $cx_store = $store->join('LEFT JOIN wm_picture ON wm_store.store_logo_id = wm_picture.id')
                        ->where($map)
                        ->field('wm_store.*,wm_picture.path')
                        ->order($order)
                        ->select();
                    foreach($cx_store as $k=>$v){
                        $jwd=explode(',',$v['lng_lat']);
                        $distance=distanceBetween($lng,$lat,$jwd[0],$jwd[1]);
                        $peisong=explode(',',$v['peisong_price']);
                        if($distance/1000<=substr(end($peisong),0,strpos(end($peisong),"-"))){
                            for( $i=0;$i<count($peisong);$i++) {
                                if($distance/1000<substr($peisong[$i],0,strpos($peisong[$i],"-"))){
                                    $v['peisongfei']=substr($peisong[$i],strpos($peisong[$i],"-")+1,count($peisong));
                                    break;
                                }
                            }
                            $timea = explode(',', $v['s_time']);
                            $timeb = explode(',', $v['e_time']);
                            $timeaa = array($timea[0] . ':' . $timea[1], $timea[2] . ':' . $timea[3]);
                            $timebb = array($timeb[0] . ':' . $timeb[1], $timeb[2] . ':' . $timeb[3]);
                            $stime = implode(',',$timeaa);
                            $etime = implode(',',$timebb);
                            if($qy_id){
                                $where['store_id']=$v['store_id'];
                                $where['year']=date("Y",time());
                                $where['month']=date("n",time());
                                $sum = M('order')->where($where)->Sum('total')/ M('order')->where($where)->count('order_id');
                                $quantity=M("GoodsCount")->where($where)->sum('quantity');
                                $v['sale_num']=empty($quantity)?0:$quantity;
                                $v['is_open']=is_open($stime,$etime);
                                $v['youhui']=$this->get_youhui($v['store_id']);
                                $pf=M("pingjia")->where("store_id=".$v['store_id'])->avg("pingfen");
                                $v['pingfen']=empty($pf)?0:($pf/5)*100;
                                $v['renjun']=$v['qisong_price']/2;
                                $v['yingye_is_two']=yingye_is_two($v['s_time']);
                                $v['juli'] = $distance/1000;
                                $res[]=$v;
                            }
                        }
                    }
                    $this->assign('store',$res);
                }
            }else{
                //$res = S('store');
                //if(empty($res)){
                $cx_store = $store->join('LEFT JOIN wm_picture ON wm_store.store_logo_id = wm_picture.id')
                    ->where($map)
                    ->field('wm_store.*,wm_picture.path')
                    ->select();
                foreach($cx_store as $k=>$v){
                    $jwd=explode(',',$v['lng_lat']);
                    $distance=distanceBetween($lng,$lat,$jwd[0],$jwd[1]);
                    $peisong=explode(',',$v['peisong_price']);
                    if($distance/1000<=substr(end($peisong),0,strpos(end($peisong),"-"))){
                        for( $i=0;$i<count($peisong);$i++) {
                            if($distance/1000<substr($peisong[$i],0,strpos($peisong[$i],"-"))){
                                $v['peisongfei']=substr($peisong[$i],strpos($peisong[$i],"-")+1,count($peisong));
                                break;
                            }
                        }
                        //营业时间
                        $timea = explode(',', $v['s_time']);
                        $timeb = explode(',', $v['e_time']);
                        $timeaa = array($timea[0] . ':' . $timea[1], $timea[2] . ':' . $timea[3]);
                        $timebb = array($timeb[0] . ':' . $timeb[1], $timeb[2] . ':' . $timeb[3]);
                        $stime = implode(',',$timeaa);
                        $etime = implode(',',$timebb);

                        $where['store_id']=$v['store_id'];
                        $where['year']=date("Y",time());
                        $where['month']=date("n",time());
                        $sum = M('order')->where($where)->Sum('total')/ M('order')->where($where)->count('order_id');
                        $quantity=M("GoodsCount")->where($where)->sum('quantity');
                        $v['sale_num']=empty($quantity)?0:$quantity;
                        $v['is_open']=is_open($stime,$etime);
                        $v['youhui']=$this->get_youhui($v['store_id']);
                        $pf=M("pingjia")->where("store_id=".$v['store_id'])->avg("pingfen");
                        $v['pingfen']=empty($pf)?0:($pf/5)*100;
                        $v['renjun']=$v['qisong_price']/2;
                        $v['yingye_is_two']=yingye_is_two($v['s_time']);
                        $v['juli'] = $distance/1000;
                        $res[]=$v;
                    }
                };
                ksort($res);
                //S('store',$res,array('type'=>'file','expire'=>3600));
                //}
                $this->assign('store',$res);
                $this->assign('aaa','aaa');
            }
        }
        $this->assign('store_name',$store_name);
        $this->assign('qy_id',$qy_id);
        $this->assign('fenlei_id',$fenlei_id);
        $this->display();
    }

    //特价专区商家页显示
    public function specialarea(){

        $store = M('store');
        $specialarea=M('specialarea');
        $qy_id=I('qy_id');
        $tj_id=I('tj_id');
        $tj_name=I('tj_name');
        if($_GET){
            $cityName=$_GET['cityName'];
            $cityName=$this->trimAll($cityName);
//            $code = M('address_city')->where("name='$cityName'")->field('code')->find();
            $lng = I('get.lng');
            $lat = I('get.lat');
            $num = $_GET['num'];

            $map['wm_store.status']=1;
            $map['wm_store.yingye_flag']=1;
            $map['wm_store.founder_id']=$qy_id;

            session('lng',$lng);
            session('lat',$lat);
            session('cityName',$cityName);
            $this->assign('reinfo',array('cityName'=>session('cityName'),'lng'=>session('lng'),'lat'=>session('lat')));
            if($num){
                $tj_name=$_GET['tj_name'];
                $_SESSION['num']=$num;
                if($num == 4){
                    $tj_content=$specialarea->where('tj_id='.$tj_id)->find();
                    $map['wm_store.store_id']=array('in',explode(',',$tj_content['store_id']));
                    $cx_store = $store->join('LEFT JOIN wm_picture ON wm_store.store_logo_id = wm_picture.id')
                        ->where($map)
                        ->field('wm_store.*,wm_picture.path')
                        ->select();

                    foreach($cx_store as $k=>$v) {
                        $jwd = explode(',', $v['lng_lat']);
                        $distance = distanceBetween($lng, $lat, $jwd[0], $jwd[1]);
                        $peisong = explode(',', $v['peisong_price']);
                        if ($distance/1000 <= substr(end($peisong), 0, strpos(end($peisong), "-"))) {
                            for ($i = 0; $i < count($peisong); $i++) {
                                if ($distance/1000 < substr($peisong[$i], 0, strpos($peisong[$i], "-"))) {
                                    $cx_store[$k]['peisongfei'] = substr($peisong[$i], strpos($peisong[$i], "-") + 1, count($peisong));
                                    break;
                                }
                            }
//                            if (empty($cx_store[$k]['peisongfei'])) {
//                                if (count($peisong) == 1) {
//                                    $v['peisongfeiyi'] = substr(reset($peisong), strpos(end($peisong), "-") + 1, count($peisong));
//                                } else {
//                                    $v['peisongfeiqi'] = substr(reset($peisong), strpos(end($peisong), "-") + 1, count($peisong));
//                                    $v['peisongfeizhi'] = substr(end($peisong), strpos(end($peisong), "-") + 1, count($peisong));
//                                }
//                            }
                            //营业时间
                            $timea = explode(',', $v['s_time']);
                            $timeb = explode(',', $v['e_time']);
                            $timeaa = array($timea[0] . ':' . $timea[1], $timea[2] . ':' . $timea[3]);
                            $timebb = array($timeb[0] . ':' . $timeb[1], $timeb[2] . ':' . $timeb[3]);
                            $stime = implode(',', $timeaa);
                            $etime = implode(',', $timebb);

                            $where['store_id'] = $v['store_id'];
                            $where['year'] = date("Y", time());
                            $where['month'] = date("n", time());
                            $sum = M('order')->where($where)->Sum('total') / M('order')->where($where)->count('order_id');
                            $quantity = M("GoodsCount")->where($where)->sum('quantity');
                            $v['sale_num'] = empty($quantity) ? 0 : $quantity;
                            $v['is_open'] = is_open($stime, $etime);
                            $v['youhui'] = $this->get_youhui($v['store_id']);
                            $pf = M("pingjia")->where("store_id=" . $v['store_id'])->avg("pingfen");
                            $v['pingfen'] = empty($pf) ? 0 : ($pf / 5) * 100;
                            $v['renjun'] = $v['qisong_price'] / 2;
                            $v['yingye_is_two'] = yingye_is_two($v['s_time']);
                            $v['juli'] = $distance / 1000;
                            $res[] = $v;
                        }
                    }
                    ksort($res);
                    $this->assign('aaa','aaa');
                    $this->assign('store',$res);
                    $this->assign('tj_name',$tj_name);
                }else{
                    if($num == 1){
                        $this->assign('aaa1','aaa');
                        $order = 'pingfen desc';
                        $this->assign('tj_name',$tj_name);
                    }
                    if($num == 2){
                        $this->assign('aaa2','aaa');
                        $order = 'qisong_price asc';
                        $this->assign('tj_name',$tj_name);
                    }
                    if($num == 3){
                        $this->assign('aaa3','aaa');
                        $order = 'sh_time asc';
                        $this->assign('tj_name',$tj_name);
                    }
                    $tj_content=$specialarea->where('tj_id='.$tj_id)->find();
                    $map['wm_store.store_id']=array('in',explode(',',$tj_content['store_id']));
                    $cx_store = $store->join('LEFT JOIN wm_picture ON wm_store.store_logo_id = wm_picture.id')
                        ->where($map)
                        ->field('wm_store.*,wm_picture.path')
                        ->order($order)
                        ->select();
                    foreach($cx_store as $k=>$v) {
                        $jwd = explode(',', $v['lng_lat']);
                        $distance = distanceBetween($lng, $lat, $jwd[0], $jwd[1]);
                        $peisong = explode(',', $v['peisong_price']);
                        if ($distance/1000 <= substr(end($peisong), 0, strpos(end($peisong), "-"))) {
                            for ($i = 0; $i < count($peisong); $i++) {
                                if ($distance/1000 < substr($peisong[$i], 0, strpos($peisong[$i], "-"))) {
                                    $v['peisongfei'] = substr($peisong[$i], strpos($peisong[$i], "-") + 1, count($peisong));
                                    break;
                                }
                            }
//                            if (empty($cx_store[$k]['peisongfei'])) {
//                                if (count($peisong) == 1) {
//                                    $v['peisongfeiyi'] = substr(reset($peisong), strpos(end($peisong), "-") + 1, count($peisong));
//                                } else {
//                                    $v['peisongfeiqi'] = substr(reset($peisong), strpos(end($peisong), "-") + 1, count($peisong));
//                                    $v['peisongfeizhi'] = substr(end($peisong), strpos(end($peisong), "-") + 1, count($peisong));
//                                }
//                            }
                            $timea = explode(',', $v['s_time']);
                            $timeb = explode(',', $v['e_time']);
                            $timeaa = array($timea[0] . ':' . $timea[1], $timea[2] . ':' . $timea[3]);
                            $timebb = array($timeb[0] . ':' . $timeb[1], $timeb[2] . ':' . $timeb[3]);
                            $stime = implode(',', $timeaa);
                            $etime = implode(',', $timebb);

                            $where['store_id'] = $v['store_id'];
                            $where['year'] = date("Y", time());
                            $where['month'] = date("n", time());
                            $sum = M('order')->where($where)->Sum('total') / M('order')->where($where)->count('order_id');
                            $quantity = M("GoodsCount")->where($where)->sum('quantity');
                            $v['sale_num'] = empty($quantity) ? 0 : $quantity;
                            $v['is_open'] = is_open($stime, $etime);
                            $v['youhui'] = $this->get_youhui($v['store_id']);
                            $pf = M("pingjia")->where("store_id=" . $v['store_id'])->avg("pingfen");
                            $v['pingfen'] = empty($pf) ? 0 : ($pf / 5) * 100;
                            $v['renjun'] = $v['qisong_price'] / 2;
                            $v['yingye_is_two'] = yingye_is_two($v['s_time']);
                            $v['juli'] = $distance / 1000;
                            $res[] = $v;
                        }
                    }
                    $this->assign('store',$res);
                }
            }else{
                $tj_content=$specialarea->where('tj_id='.$tj_id)->find();
                $map['wm_store.store_id']=array('in',explode(',',$tj_content['store_id']));
                $cx_store = $store->join('LEFT JOIN wm_picture ON wm_store.store_logo_id = wm_picture.id')
                    ->where($map)
                    ->field('wm_store.*,wm_picture.path')
                    ->select();

                foreach($cx_store as $k=>$v) {
                    $jwd = explode(',', $v['lng_lat']);
                    $distance = distanceBetween($lng, $lat, $jwd[0], $jwd[1]);
                    $peisong = explode(',', $v['peisong_price']);
                    if ($distance/1000 <= substr(end($peisong), 0, strpos(end($peisong), "-"))) {

                        for ($i = 0; $i < count($peisong); $i++) {
                            if ($distance/1000 < substr($peisong[$i], 0, strpos($peisong[$i], "-"))) {
                                $v['peisongfei'] = substr($peisong[$i], strpos($peisong[$i], "-") + 1, count($peisong));
                                break;
                            }
                        }
//                        if (empty($cx_store[$k]['peisongfei'])) {
//
//                            if (count($peisong) == 1) {
//                                $v['peisongfeiyi'] = substr(reset($peisong), strpos(end($peisong), "-") + 1, count($peisong));
//                                dump($v['peisongfeiyi']);
//                            } else {
//                                $v['peisongfeiqi'] = substr(reset($peisong), strpos(end($peisong), "-") + 1, count($peisong));
//                                $v['peisongfeizhi'] = substr(end($peisong), strpos(end($peisong), "-") + 1, count($peisong));
//                            }
//                        }
                        //营业时间
                        $timea = explode(',', $v['s_time']);
                        $timeb = explode(',', $v['e_time']);
                        $timeaa = array($timea[0] . ':' . $timea[1], $timea[2] . ':' . $timea[3]);
                        $timebb = array($timeb[0] . ':' . $timeb[1], $timeb[2] . ':' . $timeb[3]);
                        $stime = implode(',', $timeaa);
                        $etime = implode(',', $timebb);
                        $where['store_id'] = $v['store_id'];
                        $where['year'] = date("Y", time());
                        $where['month'] = date("n", time());
                        $sum = M('order')->where($where)->Sum('total') / M('order')->where($where)->count('order_id');
                        $quantity = M("GoodsCount")->where($where)->sum('quantity');
                        $v['sale_num'] = empty($quantity) ? 0 : $quantity;
                        $v['is_open'] = is_open($stime, $etime);
//                        dump(is_open($stime, $etime));
                        $v['youhui'] = $this->get_youhui($v['store_id']);
                        $pf = M("pingjia")->where("store_id=" . $v['store_id'])->avg("pingfen");
                        $v['pingfen'] = empty($pf) ? 0 : ($pf / 5) * 100;
                        $v['renjun'] = $v['qisong_price'] / 2;
                        $v['yingye_is_two'] = yingye_is_two($v['s_time']);
                        $v['juli'] = $distance / 1000;
                        $v['distance'] = $distance;
                        $res[] = $v;
                    };
                }
                ksort($res);
                $this->assign('store',$res);
                $this->assign('aaa','aaa');
            }
        }
        $this->assign('qy_id',$qy_id);
        $this->assign('tj_id',$tj_id);
        $this->assign('tj_name',$tj_name);
        $this->display();
    }

    public static function get_youhui($storeid){
        $uid =is_member_login();
        $id=empty($storeid)?session('currstoreid'):$storeid;
        //$where['store_id']=$id;
        $where['uid']=$uid;
        $where['status']=array('in','2,3,4,5,6');
        $is_first=M("Order")->where($where)->find();
        if(empty($is_first)){
            $first_ord=1;
            $map['promotion_type']=5;
        }else{
            $first_ord=2;
            $map['promotion_type']=array('neq','5');
        }
        $huodong=M("cuxiao");
        $map['store_id']=$id;
        $map['state']=0;
        $map2['effective_date']=array('elt',time());
        $map2['termination_date']=array('egt',time());
        $res=$huodong->where($map)->where($map2)->order('first_order desc')->select();
        if(empty($res)){
            $first_ord=2;
            $map1['promotion_type']=array('neq','5');
            $map1['store_id']=$id;
            $map1['state']=0;
            $res=$huodong->where($map1)->where($map2)->order('first_order desc')->select();
        };
        $colors=array("cc22e2","52af27","ff4e00","dc0411","9071cb","0EDD81","802E1A","7F8011","B987FF");
        foreach($res as  $k=> & $v){
            $v['color']=$colors[$k];
            $v['first_ord']=$first_ord;
        }
        return $res;
    }

    function trimAll($str){
        $qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");
        return str_replace($qian,$hou,$str);
    }

    public function qiye(){
        $store = M('Other');
        if($_GET) {
            $cityName = $_GET['cityName'];
            $cityName = $this->trimAll($cityName);

            $code = M('address_city')->where("name='$cityName'")->field('code')->find();

            $lng = $_GET['lng'];
            $lat = $_GET['lat'];

            $num = $_GET['num'];
            $showallStore = C('SHOWALLSTORE');
            if ($showallStore != 1) {
                $map['wm_store.status'] = 1;
                $map['wm_store.yingye_flag'] = 1;
                $map['wm_other.state'] = 0;
                $map['wm_store.city_code'] = $code['code'];
            } else {
                $map['wm_other.state'] = 0;
                $map['wm_store.status'] = 1;
                $map['wm_store.yingye_flag'] = 1;
            }

            session('lng',$lng);
            session('lat',$lat);
            session('cityName',$cityName);
            $this->assign('reinfo', array('cityName' => session('cityName'), 'lng' => session('lng'), 'lat' => session('lat')));
            if ($num) {
                $_SESSION['num'] = $num;
                if ($num == 0) {
                    $cx_store = $store->join('LEFT JOIN wm_store ON wm_store.store_id = wm_other.store_id')
                        ->where($map)
                        ->field('wm_store.store_name,wm_store.lng,wm_store.lat,wm_store.store_logo_id,wm_other.*')
                        ->select();
                    foreach ($cx_store as $k => $v) {
                        if ($showallStore != 1) {
                            $s1 = MPointInPoly($lng, $lat, $v['distribution_range']);
                        } else {
                            $s1 = true;
                        }
                        //$cx_store[$k]['juli']=distanceBetween($lng,$lat,$v['lng'],$v['lat'])/1000;
                        $distance = distanceBetween($lng, $lat, $v['lng'], $v['lat']);
                        //营业时间
                        $timea = explode(',', $v['s_time']);
                        $timeb = explode(',', $v['e_time']);
                        $timeaa = array($timea[0] . ':' . $timea[1], $timeb[0] . ':' . $timeb[1]);
                        $timebb = array($timea[2] . ':' . $timea[3], $timeb[2] . ':' . $timeb[3]);
                        $stime = implode(',', $timeaa);
                        $etime = implode(',', $timebb);
                        if ($s1 == true) {
                            if ($v['start_time'] < time() == false && $v['end_time'] > time() == false) {
                                $v['end'] = 1;
                            }
                            $where['store_id'] = $v['store_id'];
                            $canming = M('canming')->where($where)->field('wm_canming.cm_name,wm_canming.big_img')->select();
                            $v['canming'] = $canming;
                            $v['is_open'] = is_open($stime, $etime);
                            $v['juli'] = $distance / 1000;
                            $res[$distance] = $v;
                        }
                    };
                    ksort($res);
                    $this->assign('store', $res);
                    $this->assign('aaa', 'aaa');
                } else {
                    if ($num == 1) {
                        $this->assign('aaa1', 'aaa');
                        $order = 'wm_store.pingfen desc';
                    }
                    if ($num == 2) {
                        $this->assign('aaa2', 'aaa');
                        $order = 'wm_other.qisong_price asc';
                    }
                    if ($num == 3) {
                        $this->assign('aaa3', 'aaa');
                        $order = 'wm_other.earliest asc';
                    }
                    $cx_store = $store->join('LEFT JOIN wm_store ON wm_store.store_id = wm_other.store_id')
                        ->where($map)
                        ->field('wm_store.store_name,wm_store.lng,wm_store.lat,wm_store.store_logo_id,wm_other.*')
                        ->order($order)
                        ->select();
                    foreach ($cx_store as $k => $v) {
                        if ($showallStore != 1) {
                            $s1 = MPointInPoly($lng, $lat, $v['distribution_range']);
                        } else {
                            $s1 = true;
                        }
                        //$cx_store[$k]['juli']=distanceBetween($lng,$lat,$v['lng'],$v['lat'])/1000;
                        $distance = distanceBetween($lng, $lat, $v['lng'], $v['lat']);
                        $timea = explode(',', $v['s_time']);
                        $timeb = explode(',', $v['e_time']);
                        $timeaa = array($timea[0] . ':' . $timea[1], $timeb[0] . ':' . $timeb[1]);
                        $timebb = array($timea[2] . ':' . $timea[3], $timeb[2] . ':' . $timeb[3]);
                        $stime = implode(',', $timeaa);
                        $etime = implode(',', $timebb);
                        if ($s1 == true) {
                            if ($v['start_time'] < time() == false && $v['end_time'] > time() == false) {
                                $v['end'] = 1;
                            }
                            $where['store_id'] = $v['store_id'];
                            $canming = M('canming')->where($where)->field('wm_canming.cm_name,wm_canming.big_img')->select();
                            $v['canming'] = $canming;
                            $v['is_open'] = is_open($stime, $etime);
                            $v['juli'] = $distance / 1000;
                            $res[] = $v;
                        }
                    }
                    $this->assign('store', $res);
                }
            } else {
                $cx_store = $store->join('LEFT JOIN wm_store ON wm_store.store_id = wm_other.store_id')
                    ->where($map)
                    ->field('wm_store.store_name,wm_store.lng,wm_store.lat,wm_store.store_logo_id,wm_other.*')
                    ->select();
                foreach ($cx_store as $k => $v) {
                    if ($showallStore != 1) {
                        $s1 = MPointInPoly($lng, $lat, $v['distribution_range']);
                    } else {
                        $s1 = true;
                    }
                    //$cx_store[$k]['juli']=distanceBetween($lng,$lat,$v['lng'],$v['lat'])/1000;
                    $distance = distanceBetween($lng, $lat, $v['lng'], $v['lat']);
                    //营业时间
                    $timea = explode(',', $v['s_time']);
                    $timeb = explode(',', $v['e_time']);
                    $timeaa = array($timea[0] . ':' . $timea[1], $timeb[0] . ':' . $timeb[1]);
                    $timebb = array($timea[2] . ':' . $timea[3], $timeb[2] . ':' . $timeb[3]);
                    $stime = implode(',', $timeaa);
                    $etime = implode(',', $timebb);
                    if ($s1 == true) {
                        if ($v['start_time'] < time() == false && $v['end_time'] > time() == false) {
                            $v['end'] = 1;
                        }
                        $where['store_id'] = $v['store_id'];
                        $canming = M('canming')->where($where)->order('sort_order desc')->limit(10)->field('wm_canming.cm_name,wm_canming.big_img')->select();
                        $v['canming'] = $canming;
                        $v['is_open'] = is_open($stime, $etime);
                        $v['juli'] = $distance / 1000;
                        $res[$distance] = $v;
                    }
                };
                ksort($res);
                //S('store',$res,array('type'=>'file','expire'=>3600));
                //}
                $this->assign('store', $res);
                $this->assign('aaa', 'aaa');
            }
        }
        $this->display();
    }

}