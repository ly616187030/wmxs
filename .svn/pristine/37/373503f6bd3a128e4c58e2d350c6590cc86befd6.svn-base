<?php

/**

 * 购物车类 cookies 保存，保存周期为1天 注意：浏览器必须支持cookie才能够使用

 * Auther: <zhangpf41@163.com>

 * Date: 2015/6/25

 * Time: 10:03

 */



namespace Home\Controller;



class CartController extends  HomeController{



    private $cartarray = array(); // 存放购物车的二维数组

    private $cartcount; // 统计购物车数量

    public $expires = 86400; // cookies过期时间，如果为0则不保存到本地 单位为秒



    /**

     * 构造函数 初始化操作 如果$id不为空，则直接添加到购物车

     *

     */

    public function _initialize($id = "",$name = "",$price = "",$count = "",$expires = 86400) {

        parent::_initialize();

        if (!empty($id) && is_numeric($id)) {

            $this->expires = $expires;

            $this->addCart($id,$name,$price,$count);

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
     * @param int $category_id餐品分类id
     * @param string $category_name 餐品分类名称

     * @return 如果商品存在，则在原来的数量上加1，并返回false

     */

    public function addCart($id,$name,$price,$count,$storeid,$category_id,$category_name) {

        $this->cartarray = $this->cartView(); // 把数据读取并写入数组

        if(!$this->checkStore($storeid) && $this->checkCart()){


            $this->ajaxReturn(array('status'=>1));
            //$this->removeAll();

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
        $this->cartarray[5][$id]=$category_id;
        $this->cartarray[6][$id]=$category_name;
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
                            unset($tmparray[6][$id]);



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
                            unset($tmparray[6][$id]);

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
                        unset($tmparray[6][$id]);

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
        session('cartapi',null);

        $this->save();

    }

    /**

     * 查看购物车信息

     * @param type 1=ajax 0=array

     * @return array 返回一个二维数组

     */

    public function cartView() {

        $type=I("get.type",0);

        //$cookie = cookie('cartapi');
        $cookie = session('cartapi');

        if (!$cookie) return false;

        $tmpunserialize = unserialize($cookie);

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

        //cookie("cartapi",$tmpserialize,time()+$this->expires);
        session("cartapi",$tmpserialize);

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

     * 结账

     */

    public function settlement(){
        //dump($_REQUEST);

       if(!is_login()){

            redirect(U("User/login"));

        }

        $loginis = is_login();

        $basket=$this->cartView();

        if(empty($basket[0])) redirect(U('Waimai/index'));

        $m = M('User_address');

        $address = $m->where('uid = '.UID)->select();

        $p = M('Address_province');

        $list = $p->select();

        $canju_num= I('canju_num',0,'intval');

        $canju_yj = I('canju_yj',0,'intval');

        $canju_total=intval($canju_yj)*intval($canju_num);
        $storeid=$this->getStoreId();

        $this->assign("store_id",$storeid);

        $store_num=$this->countPriceReturn();

        $store=M("Store")->alias('s')->join('wm_store_category AS c ON s.store_c_id=c.store_c_id')->where('store_id='.$storeid)->field("s.*,c.store_c_name")->find();
        $lng_st = $store['lng'];
        $lat_st = $store['lat'];

        //$temp=array();
        $canjutotal=0;
        foreach($basket[0] as $v){
            $canju = M('shangpin')->join('LEFT JOIN wm_canming ON wm_canming.sp_id = wm_shangpin.sp_id')->where('wm_canming.cm_id ='.$basket[0][$v])->field('wm_shangpin.sp_danjia')->find();
            $canjutotal += $canju['sp_danjia']*$basket[3][$v];
            $cateid[]=$basket[5][$v];
            $temp[]=array("id"=>$basket[0][$v],"name"=>$basket[1][$v],"price"=>$basket[2][$v],"count"=>$basket[3][$v],"subtotal"=>$basket[2][$v]*$basket[3][$v],"storeid"=>$basket[4][$v],'category_id'=>$basket[5][$v],'category_name'=>$basket[6][$v]);
        }

        $cateid=array_unique($cateid);
        foreach($cateid as $key=> $val){
            foreach($temp as $v){
                if($val==$v['category_id']){
                    $temp1[$key]['category_id']=$v['category_id'];
                    $temp1[$key]['category_name']=$v['category_name'];
                    $temp1[$key]['child'][]=$v;
                    $temp1[$key]['count']=count($temp1[$key]['child']);
                }
            }
        }
        $add = M('UserAddress')->where('flag=1 AND uid='.UID)->field('detail_address,address_id,lng,lat')->find();
        $lng_add = $add['lng'];
        $lat_add = $add['lat'];

        //获取配置信息
        $peisong_all_array = explode(',',C('PEISONG_SCOPE'));
        for($i=0;$i<count($peisong_all_array);$i++){
            $peisong_array = explode('|',$peisong_all_array[$i]);
            $rd[] = $peisong_array[0];
            $ps_price[]=$peisong_array[1];
        }

        //营业时间
        $pstime=C('OPENTIME');
        //$open=explode('-',$opentime);
        //$isopen=is_open($open[0],$open[1]);
        $this->assign('pstime',$pstime);
        $ida = session('currstoreid');
        $this->assign('canjutotal',round($canjutotal));
        $this->assign('_store_info',$store);
        $this->assign('canju_num',$canju_num);
        $this->assign('canju_yj',$canju_yj);
        $this->assign('_address',$add);
        $this->assign('canju_total',$canju_total);

        //dump($temp1);

        $this->assign("store_name",$this->getStoreName());
        $this->assign("store_list",$temp1);
        $this->assign("store_num",$store_num[1]);   //总数量
        $this->assign("store_total",$store_num[0]);  //总价
        $this->assign('yajin',$canju_total);
        $this->assign('list',$list);
        $this->assign("yhui",json_encode(A('Stores')->get_youhui($storeid)));
        $this->assign('address',$address);
        $this->assign('is_login',$loginis);

        $this->assign('lng_add',$lng_add);
        $this->assign('lat_add',$lat_add);
        $this->assign('lng_st',$lng_st);
        $this->assign('lat_st',$lat_st);
        $this->assign('rd',json_encode($rd));
        $this->assign('ps_price',json_encode($ps_price));
        $this->assign('yhdesc',A('Stores')->get_youhui($storeid));
        //代金券
        $this->assign('daijinquan',$this->daijinquan());
        $this->display();

    }



    public function goon(){

        $this->assign("storeid",session("currstoreid"));

        $this->display("empty");

    }


    public function payresult(){
        $loginis = is_login();
        $order_bh =  session('order_bh');
        $ord_id = M('order')->where('order_sn='.$order_bh)->field('order_id')->find();
        $this->assign('ord_id',$ord_id);
        $zongjia = session('zongjia');
        $zhifufangshi = session('zhifufangshi');
        $this->assign('order_bh',$order_bh);
        $this->assign('zongjia',$zongjia);
        $this->assign('zhifufangshi',$zhifufangshi);
        $this->assign('is_login',$loginis);
        $this->display();
    }




    public function address(){
        $m = D('User_address');
        $uid = UID;

        if(IS_POST){
            if($m->create()){
                $m->uid = $uid;

                if($insertid=$m->add()){
                    $this->ajaxReturn(array("info"=>'地址添加成功','status'=>1,'insertid'=>$insertid,'url'=>U('Cart/settlement')));
                }else{
                    $this->error('地址添加失败');
                }
            }else{
                $this->error($m->getError());
            }
        }
    }

        public function setDefaultAddress(){
        $uid=I('uid');
        $addressid=I('addressid');
        $add=M('UserAddress');
        $map['address_id']=$addressid;
        $data['flag']=1;
        $data1['flag']=0;
        $res=$add->where($map)->save($data);
        $res1=$add->where('uid='.$uid.' AND address_id!='.$addressid)->save($data1);
        /*if($res!==false && $res1!==false){
            $this->ajaxReturn($res1);
        }else{
            $this->ajaxReturn(0);
        }*/

    }
    public function daijinquan(){
        $bao_id=I('get.user_bao_id',0,'intval');
        $uid =is_login();
        $time = strtotime(date('Y-m-d',time()));
        $quan = M('user_bao');
        $where['wm_ucenter_member.id']=$uid;
        $where['wm_user_bao.expire_time']=array('egt',$time);
        $where['wm_user_bao.status']=0;
        if(empty($bao_id)){
            $info =$quan->join('LEFT JOIN wm_ucenter_member ON wm_ucenter_member.mobile = wm_user_bao.user_phone')
                ->where($where)
                ->select();
            return json_encode($info);
        }else{
            $where['wm_user_bao.user_bao_id']=$bao_id;
            $info =$quan->join('LEFT JOIN wm_ucenter_member ON wm_ucenter_member.mobile = wm_user_bao.user_phone')
                ->where($where)
                ->select();
            return json_encode($info);
        }
    }
}