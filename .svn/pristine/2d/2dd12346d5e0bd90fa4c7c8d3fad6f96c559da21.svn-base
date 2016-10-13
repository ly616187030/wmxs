<?php
/**
 * 购物车类 cookies 保存，保存周期为1天 注意：浏览器必须支持cookie才能够使用
 * Auther: <zhangpf41@163.com>
 * Date: 2015/6/25
 * Time: 10:03
 */

namespace Mobile\Controller;
use \Mobile\Controller;
class CartController extends  MobileController{

    private $cartarray = array(); // 存放购物车的二维数组
    private $cartcount; // 统计购物车数量
    public $expires = 86400; // cookies过期时间，如果为0则不保存到本地 单位为秒

    /**
     * 构造函数 初始化操作 如果$id不为空，则直接添加到购物车
     *
     */
    public function _initialize($id = "",$name = "",$price = "",$count = "",$canpin="",$expires = 86400) {
        parent::_initialize();
        if (!empty($id) && is_numeric($id)) {
            $this->expires = $expires;
            $this->addCart($id,$name,$price,$count,$canpin);
        }
    }

    /**
     * 添加商品到购物车
     *
     * @param int $id 商品的编号
     * @param string $name 商品名称
     * @param decimal $price 商品价格
     * @param int $count 商品数量
     * @param int $storeid 商家id
     * @return 如果商品存在，则在原来的数量上加1，并返回false
     */
    public function addCart($id,$name,$price,$count,$storeid,$canpin) {
        $this->cartarray = $this->cartView(); // 把数据读取并写入数组
        if(!$this->checkStore($storeid) && $this->checkCart()){
            $this->ajaxReturn(array('status'=>1,'storename'=>$this->getStoreName()));
        }
        if ($this->checkItem($id)) { // 检测商品是否存在
            $this->modifyCart($id,$count,0); // 商品数量加$count
            return false;
        }
        $this->cartarray[0][$id] = $id;
        $this->cartarray[1][$id] = $name;
        $this->cartarray[2][$id] = $price;
        $this->cartarray[3][$id] = $count;
        $this->cartarray[4][$id]=$storeid;
        $this->cartarray[5][$id]=$canpin;
        $this->save();
    }
    /**
     * 修改购物车里的商品
     *
     * @param int $id 商品编号
     * @param int $count 商品数量
     * @param int $flag 修改类型 0：加 1:减 2:修改 3:清空
     * @return 如果修改失败，则返回false
     */
    public function modifyCart($id, $count, $flag = "") {
        $tmpid = $id;
        $this->cartarray = $this->cartView(); // 把数据读取并写入数组
        $tmparray = &$this->cartarray;  // 引用
        if (!is_array($tmparray[0])) return false;
        if ($id < 1) {
            return false;
        }
        foreach ($tmparray[0] as $item) {
            if ($item === $tmpid) {
                switch ($flag) {
                    case 0: // 添加数量 一般$count为1
                        $tmparray[3][$id] += $count;
                        break;
                    case 1: // 减少数量
                        $tmparray[3][$id] -= $count;
                        if($tmparray[3][$id]==0){
                            unset($tmparray[0][$id]);
                            unset($tmparray[1][$id]);
                            unset($tmparray[2][$id]);
                            unset($tmparray[3][$id]);
                            unset($tmparray[4][$id]);
                            unset($tmparray[5][$id]);
                        }
                        break;
                    case 2: // 修改数量
                        if ($count == 0) {
                            unset($tmparray[0][$id]);
                            unset($tmparray[1][$id]);
                            unset($tmparray[2][$id]);
                            unset($tmparray[3][$id]);
                            unset($tmparray[4][$id]);
                            unset($tmparray[5][$id]);
                            break;
                        } else {
                            $tmparray[3][$id] = $count;
                            break;
                        }
                    case 3: // 清空商品
                        unset($tmparray[0][$id]);
                        unset($tmparray[1][$id]);
                        unset($tmparray[2][$id]);
                        unset($tmparray[3][$id]);
                        unset($tmparray[4][$id]);
                        unset($tmparray[5][$id]);
                        break;
                    default:
                        break;
                }
            }
        }
        $this->save();
    }

    /**
     * 清空购物车
     *
     */
    public function removeAll() {
        $this->cartarray = array();
        $this->save();
    }
    /**
     * 查看购物车信息
     * @param type 1=ajax 0=array
     * @return array 返回一个二维数组
     */
    public function cartView() {
        $type=I("get.type",0);
        $session = session('cartapi');
        //if (!$session) return false;
        $tmpunserialize = unserialize($session);
        if($type==0){
            return $tmpunserialize;
        }elseif($type==1){
            $this->ajaxReturn($tmpunserialize);
        }
    }

    /**
     * 检查购物车是否有商品
     *
     * @return bool 如果有商品，返回true，否则false
     */
    public function checkCart() {
        $tmparray = $this->cartView();
        if (count($tmparray[0]) < 1) {
            return false;
        }
        return true;
    }
    /**
     * 商品统计
     *
     * @return array 返回一个一维数组 $arr[0]:产品1的总价格  $arr[1]:产品的总数量
     */
    public function countPrice() {
        $tmparray = $this->cartarray = $this->cartView();
        $outarray = array(); //一维数组
        // 0 是产品1的总价格
        // 1 是产品的总数量
        $i = 0;
        if (is_array($tmparray[0])) {
            foreach ($tmparray[0] as $key=>$val) {
                $outarray[0] += $tmparray[2][$key] * $tmparray[3][$key];
                $outarray[1] += $tmparray[3][$key];
                $i++;
            }
        }
        //return $outarray;
        $this->ajaxReturn($outarray);
    }
    public function countPriceReturn() {
        $tmparray = $this->cartarray = $this->cartView();
        $outarray = array(); //一维数组
        $storeid='';
        // 0 是产品1的总价格
        // 1 是产品的总数量
        $i = 0;
        if (is_array($tmparray[0])) {
            foreach ($tmparray[0] as $key=>$val) {
                $outarray[0] += $tmparray[2][$key] * $tmparray[3][$key];
                $outarray[1] += $tmparray[3][$key];
                $i++;
            }
        }
        return $outarray;
    }
    /**
     * 统计商品数量
     *
     * @return int
     */
    public function cartCount() {
        $tmparray = $this->cartView();
        if(empty($tmparray)){
            $this->ajaxReturn(0);
        }
        $tmpcount = count($tmparray[0]);
        $this->cartcount = $tmpcount;
        //return $tmpcount;
        $this->ajaxReturn($tmpcount);
    }
    /**
     * 保存商品 如果不使用构造方法，此方法必须使用
     *
     */
    public function save() {
        $tmparray = $this->cartarray;
        $tmpserialize = serialize($tmparray);
        session("cartapi",$tmpserialize,time()+$this->expires);
    }
    /**
     * 检查购物车商品是否存在
     *
     * @param int $id
     * @return bool 如果存在 true 否则false
     */
    private function checkItem($id) {
        $tmparray = $this->cartarray;
        if (!is_array($tmparray[0])) return;
        foreach ($tmparray[0] as $item) {
            if ($item === $id) return true;
        }
        return false;
    }
    /**
     * 检查商家是否已经存在
     * @param int $storeid
     * @return bool 如果存在 true 否则false
     */
    private function checkStore($storeid){
        $tmparray = $this->cartarray;
        if (!is_array($tmparray[4])) return;
        foreach ($tmparray[4] as $item) {
            if ($item === $storeid) return true;
        }
        return false;
    }
    /**
     *
     * 获取某个商品的数量
     * @param int $id
     */
    public  function getOneCount($id){
        $tmparray = $this->cartView();
        $this->ajaxReturn($tmparray[3][$id]);
    }
    public function getStoreId(){
        $tmparray = $this->cartView();
        foreach($tmparray[4] as $item){
            return $item;
        }
    }

    /**
     * 获取当前购车中商家名称
     * @return mixed
     */
    public function getStoreName(){
        $id=$this->getStoreId();
        $sname=M("Store")->where("store_id=".$id)->getField('store_name');
        return $sname;
    }
    /**
     * 获取当前购车中商家名称
     * @return mixed
     */
    public function getStoreSn(){
        $id=$this->getStoreId();
        $sn=M("Store")->where("store_id=".$id)->getField('store_sn');
        return $sn;
    }
    /**
     * 结账
     */
    public function index(){
        if(!is_member_login()){
            redirect(U("Index/getFollow"));
        }
        $basket=$this->cartView();
        if(empty($basket[0])) redirect(U('Index/index'));
        $storeid=$this->getStoreId();
        $storename=$this->getStoreName();
        $store_sn=$this->getStoreSn();
        $store=M("Store")->alias('s')->join('wm_store_category AS c ON s.store_c_id=c.store_c_id')->where('store_id='.$storeid)->field("s.*,c.store_c_name")->find();
        $timea = explode(',', $store['s_time']);
        $timeb = explode(',', $store['e_time']);
        $timeaa = array($timea[0].':'.$timea[1],$timea[2].':'.$timea[3]);
        $timebb = array($timeb[0].':'.$timeb[1],$timeb[2].':'.$timeb[3]);
        $stime = implode(',',$timeaa);
        $etime = implode(',',$timebb);
        $is_open = is_open($stime,$etime);
        $this->assign('is_open',$is_open);
        $this->assign('stime',$timea);
        $this->assign('etime',$timeb);

        $m = M('User_address');
        //A('Wxpay')->js_api_call();
        $uid = is_member_login();
        $address_id = I('address_id',0,'intval');
        if(empty($address_id)){
            $address = $m->where("uid = $uid and flag = 1")->select();

        }else{
            $address =$m->where("uid = $uid and address_id = $address_id")->select();
            $m->where('address_id='.$address_id)->setField('flag','1');
            $m->where('address_id!='.$address_id)->setField('flag','0');
        }
        foreach($address as $k=>$v) {
            $addr = explode("&",$v['detail_address']);
            $address[$k]['detail_address']=$addr;
            $jwd=explode(',',$store['lng_lat']);
            $distance=distanceBetween($v['lng'],$v['lat'],$jwd[0],$jwd[1]);
            $peisong=explode(',',$store['peisong_price']);
            if($distance/1000<=substr(end($peisong),0,strpos(end($peisong),"-"))) {
                for ($i = 0; $i < count($peisong); $i++) {
                    if ($distance / 1000 < substr($peisong[$i], 0, strpos($peisong[$i], "-"))) {
                        session('peisongfei',null);
                        session('peisongfei', substr($peisong[$i], strpos($peisong[$i], "-") + 1, count($peisong)));
                        $store['juli']=$distance/1000;
                        break;
                    }
                }
            }
        }


        //$peisongfei=I('peisongfei');
        //session('peisongfei',$peisongfei);
        $this->assign("peisongfei",session('peisongfei'));

        $this->assign("store_id",$storeid);
        $this->assign("store_sn",$store_sn);

        $canjutotal=0;

            foreach($basket[0] as $v){
                $canju = M('canming')->where('wm_canming.cm_id ='.$basket[0][$v])->field('food_box_price,food_box_number')->find();
                $canjutotal += $canju['food_box_price']*$basket[3][$v];
                $temp[]=array("id"=>$basket[0][$v],"name"=>$basket[1][$v],"price"=>$basket[2][$v],"count"=>$basket[3][$v],"subtotal"=>$basket[2][$v]*$basket[3][$v],"storeid"=>$basket[4][$v],'canju'=>$canju);
            }
        $store_num=$this->countPriceReturn();

        $this->assign('canjutotal',round($canjutotal));
        $this->assign('_store_info',$store);
        //$this->assign('guoju',$guoju);
        $this->assign("store_name",$storename);
        $this->assign("store_list",$temp);
        $this->assign("store_num",$store_num[1]);
        $this->assign("store_total",$store_num[0]);

            $this->assign("yhui",json_encode(A('Index')->get_youhui($storeid)));
            //$this->assign('kanjia_total',0);

        $this->assign('address',$address);

        //优惠
        /*  $youhui =M('cuxiao')->where("store_id = $storeid AND state = 0")->select();
          $colors=array("cc22e2","52af27","ff4e00","dc0411","9071cb");
          foreach($youhui as  $k=> & $v){
              $v['color']=$colors[$k];
          }*/
        $this->assign('youhui',A('Index')->get_youhui($storeid));
        //代金券
        $this->assign('daijinquan',$this->daijinquan());
       // $this->assign('kanjia',$kanjiaid);
        $this->display();
    }


    public function goon(){
        $this->assign("storeid",session("currstoreid"));
        $this->display("empty");
    }

    public function pay(){
        $store_name = I('store_name');
        $cm_name = I('cm_name');
        $cm_num = I('cm_num');
        $ordsn=I('order_sn');
        $arr_cm=explode(',',$cm_name);
        $arr_num=explode(',',$cm_num);
        foreach($arr_cm as $k=>$v){
            $list[]=array('cm_name'=>$v,'cm_num'=>$arr_num[$k]);
        }
        $total = I('total');
        $pushid=M('Store')->getFieldByStoreId(session('currstoreid'),'push_msg_id');
        $this->assign('pushid',$pushid);
        $this->assign("store_name",$store_name);
        $this->assign("list",$list);
        $this->assign("total",$total);
        $this->assign("ordsn",$ordsn);
        $this->display();
    }
    public function daijinquan(){
        $bao_id=I('get.user_bao_id');
        $uid =is_member_login();
        $time = strtotime(date('Y-m-d',time()));
        $quan = M('user_bao');
        $where['wm_member_user.id']=$uid;
        $where['wm_user_bao.expire_time']=array('egt',$time);
        $where['wm_user_bao.status']=0;
        if(empty($bao_id)){
            $info =$quan->join('LEFT JOIN wm_member_user ON wm_member_user.mobile = wm_user_bao.user_phone')
                ->where($where)
                ->select();
            return json_encode($info);
        }else{
            $where['wm_user_bao.user_bao_id']=$bao_id;
            $info =$quan->join('LEFT JOIN wm_member_user ON wm_member_user.mobile = wm_user_bao.user_phone')
                ->where($where)
                ->select();
            return json_encode($info);
        }
    }
}