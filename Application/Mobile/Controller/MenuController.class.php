<?php
namespace Mobile\Controller;
use \Think\Controller;
class MenuController extends Controller{
    function index(){
        $canpin = M('Canpin');
        $canming = M('Canming');
        $store = M('Store');
        $fav_store = M('Favorites_store');
        $youhui = M("Youhui");

        $goods_count = M('Goods_count');
        $id =I('get.store_id');
        $peisongfei=I('get.peisongfei');
        if(isset($peisongfei)){
            session('peisongfei',$peisongfei);
        }
        $is_open=I('get.is_open');
        if(isset($is_open)){
            session('is_open',$is_open);
        }
        //session('peisongfei',$peisongfei);
        if(session('currstoreid') != ''&&$id != session('currstoreid')){
            A('Cart')->removeAll();
        }
        session("currstoreid",$id);
        //session('ohter',$_GET['ohter']);
        $where = 'store_id = '.$id;
        //查询餐品分类按照指定字段排序显示
        $list_canpin = $canpin->where($where)->order('sort_order asc')->select();

        //查询菜品名称，flag=1 表示启用状态

        $list_canming = $canming->alias('a')->where('a.flag = 1 AND a.store_id = '.$id)->order('sort_order asc')->select();
        foreach($list_canming as $k=>$v){
            $quantity = M("GoodsCount")->where(array('cm_id'=>$v['cm_id']))->sum('quantity');
            $list_canming[$k]['quantity'] = empty($quantity) ? 0 : $quantity;
        }

        //判断当前用户在当前店铺是否已收藏
        $uid =is_member_login();
        if(is_member_login()){
        $list_fav = $fav_store->where("store_id = $id AND uid =$uid")->count();
        }
        //查询显示当前店铺的优惠信息
        $list_youhui = $youhui->where("store_id = $id AND status = 1")->select();

        $list_store = $store->find($id);

        $this->assign('list_store',$list_store);
        $this->assign('list_canpin',$list_canpin);

        $this->assign('list_canming',$list_canming);
        $this->assign('list_fav',$list_fav);
        $this->assign('list_youhui',$list_youhui);

        $this->assign('menu','bottom-all');
        $this->assign('storeid',$id);
        $this->assign('peisongfei',session('peisongfei'));
        $this->assign('is_open',session('is_open'));
        $this->display();
    }

    function pingjia(){

        $fav_store = M('Favorites_store');
        $pingjia = M('pingjia');
        $store_id = session('currstoreid');
        $uid =is_member_login();
        if(is_member_login()){
            $list_fav = $fav_store->where("store_id = $store_id AND uid =$uid")->count();
        }
        $info = $pingjia->alias('pj')
            ->join('LEFT JOIN wm_store as st ON st.store_id = pj.store_id')
            ->join('LEFT JOIN wm_member_user as au ON au.id = pj.uid')
            ->where("st.store_id = $store_id")
            ->field('st.store_name,pj.*,au.username')
            ->select();

        $store_name =$info[0];
        $this->assign('list_store',$store_name);

        $this->pingFen($store_id);
        $this->assign('info',$info);
        $this->assign('menu1','bottom-all');
        $this->assign('storeid',$store_id);
        $this->assign('list_fav',$list_fav);
        $this->display();
    }
    function xiangq(){
        $fav_store = M('Favorites_store');
        $store = M('Store');
        $store_id = session('currstoreid');
        $uid =is_member_login();
        if(is_member_login()){
            $list_fav = $fav_store->where("store_id = $store_id AND uid =$uid")->count();
        }
        $list_store = $store->join('LEFT JOIN wm_pingjia ON wm_pingjia.store_id = wm_store.store_id')->where("wm_store.store_id = $store_id")->find();

        $peisong_all_array=explode(',',$list_store['peisong_price']);
        for($i=0;$i<count($peisong_all_array);$i++){
            $peisong_array = explode('-',$peisong_all_array[$i]);
            $ps_price[]=$peisong_array[1];
        }
        $list_store['peisongfeiqi']=$ps_price[0];
        $list_store['peisongfeizhi']=$ps_price[count($ps_price)-1];

        $timea = explode(',', $list_store['s_time']);
        $timeb = explode(',', $list_store['e_time']);
        $timeaa = array($timea[0].':'.$timea[1],$timea[2].':'.$timea[3]);
        $timebb = array($timeb[0].':'.$timeb[1],$timeb[2].':'.$timeb[3]);
        $this->assign('timea',$timeaa);
        $this->assign('timeb',$timebb);
        $this->assign('list_store',$list_store);
        $this->assign('menu2','bottom-all');
        $this->assign('storeid',$store_id);
        $this->assign('list_fav',$list_fav);
        $this->display();
    }
    function pingFen($id){
        $m = M('Pingjia');

        $list_store_avg = $m->where('store_id ='.$id)->avg('pingfen');//店铺平均分
        $list_store_ps = $m->where('store_id ='.$id)->avg('pj_songda');
        $list_store_all = ($list_store_ps+$list_store_avg)/2;
        //$list_store_sum = $m->where('store_id ='.$store_id)->sum('pingfen');//店铺总分
        $count = $m->where('store_id ='.$id)->count();//评分总数
        $five  = $m->where('pingfen = 5 AND store_id ='.$id)->count();//五星评分的总数
        $four  = $m->where('pingfen = 4 AND store_id ='.$id)->count();//四星评分的总数
        $three = $m->where('pingfen = 3 AND store_id ='.$id)->count();//三星评分的总数
        $two   = $m->where('pingfen = 2 AND store_id ='.$id)->count();//二星评分的总数
        $one   = $m->where('pingfen = 1 AND store_id ='.$id)->count();//一星评分的总数

        $this->assign('five',$five);
        $this->assign('four',$four);
        $this->assign('three',$three);
        $this->assign('two',$two);
        $this->assign('one',$one);
        $this->assign('count',$count);
        $this->assign('list_store_avg',number_format($list_store_avg,1));
        $this->assign('list_store_ps',number_format($list_store_ps,1));
        $this->assign('list_store_all',number_format($list_store_all,1));
    }

    /**
     * 获取当前商家优化活动
     * @return string json
     */
    public function getYouhui($storeid){
        $uid =is_member_login();
        $id=empty($storeid)?session('currstoreid'):$storeid;
        $map['store_id']=$id;
        $map['uid']=$uid;
        $map['status']=array('in','3,4,5,6');
        $is_first=M("Order")->where($map)->find();
        $temps=array();
        $you=M('Youhui');
        if(empty($is_first)){
            $shou=$you->where("store_id={$id} AND yh_type=1 AND status=1")->find();
            return json_encode($shou);
        }else{
            $online=$you->where("store_id={$id} AND yh_type=2 AND status=1")->find();
            $xiadan=$you->where("store_id={$id} AND yh_type=3 AND status=1")->find();
            $y=array($online,$xiadan);
            return json_encode($y);
        }
    }
    /*收藏*/
    public function storeFav(){
        //收藏店铺
        $uid = I('post.uid');
        $store_id = I('post.storeid');
        $m = M('Favorites_store');

        $data['uid'] = $uid;
        $data['store_id'] =$store_id;
        $data['status'] = 1;

        $where['uid'] = $uid;
        $where['store_id'] = $store_id;

        $status = $m->where($where)->count();

        if($status > 0){
            $list = $m->where($where)->delete();
        }else{
            $list = $m->add($data);
        }

        $this->ajaxReturn($list);

    }

    /**
     * 餐品收藏
     * Author:changkai <job_ck@126.com>
     */
    public function canpin_share(){
        if($_POST){
            $data['cm_id']=I('post.cm_id');
            $data['uid']=is_member_login();
            $data['status']=1;
                $re = M('favorites_cm')->where('uid='.$data['uid'].' AND cm_id='.$data['cm_id'])->select();
                if($re){
                    $this->ajaxReturn(false);
                }
                else {
                    $shoucang = M('favorites_cm')->add($data);
                    if ($shoucang) {
                        $this->ajaxReturn(true);
                    }
                }
        }
    }

}