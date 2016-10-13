<?php
/**
 * 外卖首页控制器
 * Auther: <zhangpf41@163.com>
 * Date: 2015/6/12
 * Time: 15:48
 */

namespace Home\Controller;

class WaimaiController extends HomeController{


    /**
     * 外卖餐厅首页
     */
    public function index(){
        $cityName = I("citynames");
        $lng = I('post.jingdu');
        $lat = I('post.weidu');
        $content = I('post.content');
        $citycode = I('post.citycode');
        $position=I('post.content');
        $opentime=C('OPENTIME');
        if(empty($lng)){
            redirect(U('Index/index'));
        }
        $rstinfo = array( 'lng' => $lng, 'lat' => $lat,'position' => $position,'citycode'=>$citycode,'cityname'=>$cityName );
        session("rstinfo", $rstinfo);
        $showall=C('SHOWALLSTORE');
        $showall==1 && $map['city_code']=$citycode;
        $map['status']=1;
        $map['yingye_flag']=1;

        $store=M('Store')->where($map)->order('store_id desc')->select();
        $res1=array();
        //点赞
        foreach($store as $k=>$v){
            $san = M('praise')->where('store_id ='.$v['store_id'])->sum('praise');
            $store[$k]['praise'] = $san;
        }
        //取促销分类
        $cuxiao = M('cuxiao')->select();
        foreach($store as $k=>$v){
            $distance=distanceBetween($rstinfo['lng'],$rstinfo['lat'],$v['lng'],$v['lat']);
            //$scope=(int)C('RST_SCOPE');
            //如果显示全部餐厅开发关闭
            if(!$showall){
                //计算是否在配送范围
                $arr_user = array();
                $arr_user['x'] = $rstinfo['lng'];
                $arr_user['y'] = $rstinfo['lat'];
                $res= explode(",",$v['distribution_range']);
                $arr_text1 = array();
                $arr_text = array();
                for($i=0;$i<count($res);$i+=2){
                    $arr_text1['x'] = $res[$i];
                    $arr_text1['y'] = $res[$i+1];
                    $arr_text[] = $arr_text1;
                }
                if(PointInPoly($arr_user,$arr_text)) {
                    $yh_arr = array();
                    //遍历促销
                    foreach($cuxiao as $cx){
                        if($cx['store_id'] == $v['store_id']){
                            explode('-',$cx['jian']);
                            $yh_arr[] = $cx;
                        }
                    }

                    $v['cuxiao_data'] = $yh_arr;
                    //评论
                    $pl = array();
                    $pl['store_id'] = $v['store_id'];
                    $pinlun_number = M('pingjia')->where($pl)->count("pj_id");
                    $v['pl_number'] = $pinlun_number;

                    $v['is_open']=is_open($v['s_time'],$v['e_time']);

                    $v['yingye_time'] =$opentime;
                    $o_time=explode(',',$v['s_time']);
                    $o1_time=explode(',',$v['e_time']);
                    $v['store_open_time']=$o_time[0].':'.$o_time[1].'-'.$o_time[2].':'.$o_time[3].'/'.$o1_time[0].':'.$o1_time[1].'-'.$o1_time[2].':'.$o1_time[3];
                    $res1[$distance] = $v;
                }
            }else{
                $yh_arr = array();
                //遍历促销
                foreach($cuxiao as $h=>$cx){
                    if($cx['store_id'] == $v['store_id']){
                        $yh_arr[] = $cx;
                    }
                }
                $v['cuxiao_data'] = $yh_arr;

                //评论
                $pl = array();
                $pl['store_id'] = $v['store_id'];
                $pinlun_number = M('pingjia')->where($pl)->count("pj_id");
                $v['pl_number'] = $pinlun_number;

                $v['is_open']=is_open($v['s_time'],$v['e_time']);

                $o_time=explode(',',$v['s_time']);
                $o1_time=explode(',',$v['e_time']);
                $v['store_open_time']=$o_time[0].':'.$o_time[1].'-'.$o_time[2].':'.$o_time[3].'/'.$o1_time[0].':'.$o1_time[1].'-'.$o1_time[2].':'.$o1_time[3];
                $v['yingye_time'] =$opentime;
                $res1[$distance] = $v;
            }

        }

        ksort($res1);

        $this->assign('store_list',$res1);
        //查询人均消费
        //餐厅类型
        $rst_list=M('StoreCategory')->order('sort_order')->limit(5)->select();
        $this->assign('rst_list',$rst_list);
        $this->assign("is_login",is_login());
        $this->assign('_position',$position);
        $this->assign('cityName',$cityName);
        $this->assign('content',$content);

        $this->assign('now_time',date('H',time()));
        $this->assign("_query", array("lng" => $lng, "lat" => $lat,'position' => $position,'citycode'=>$citycode));

        $this->display();

    }

    //点赞
    public function store_id(){
        $uid1['uid'] = I('uid');
        $uid1['store_id'] = I('sid');
        $prs = M('praise')->where($uid1)->select();
        $this->ajaxReturn($prs);
    }
    //店铺评论
    public function rating(){

        //店铺评价

        $store_id = I('get.store_id');

        $m = M('Pingjia');

        $store = M('Store');
        $n = M('Order');
        $z = M('Praise');

        $count = $m->where('store_id = '.$store_id)->count();
        $Page = new \Think\Page($count,6);// 实例化分页类 传入总记录数和每页显示的记录数(10)

        $Page->setConfig('prev','&nbsp;');
        $Page->setConfig('next','&nbsp;');
        $Page->setConfig('theme','  %UP_PAGE% %NOW_PAGE% / %TOTAL_PAGE% %DOWN_PAGE% ');
        $Page->lastSuffix = false;//分页最后的总页数不显示
        $show = $Page->show();// 分页显示输出
        //查询并显示当前评价的会员名称和评价信息

        $list = $m->alias('a')
            ->join('LEFT JOIN wm_member AS b ON a.uid = b.uid')
            ->field('a.*,b.nickname')->where('store_id ='.$store_id)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        foreach($list as $k=>$v){
            $sum = $n->where('status = 6 AND uid = '.$v['uid'])->sum('total');
            if(empty($sum)){
                $list[$k]['count'] = 0;
            }else{
                $list[$k]['count'] = $sum;
            }

        }
        //显示当前店铺信息

        $list_store = $store->alias('a')->join('LEFT JOIN wm_pingjia AS p ON a.store_id = p.store_id')->field('a.*,p.pingfen')->where('a.store_id ='.$store_id)->find();
        $sum_zan = $z->where('store_id = '.$store_id)->sum('praise');
        if(empty($sum_zan)){
            $list_store['count'] = 0;
        }else{
            $list_store['count'] = $sum_zan;
        }

        //营业时间

        if(strpos($list_store['s_time'],",")){

            $t_stime=explode(",",$list_store['s_time']);

            $t_etime=explode(",",$list_store['e_time']);

            $list_store['open_time']="$t_stime[0]-$t_etime[0] / $t_stime[1]-$t_etime[1]";

        }else{

            $list_store['open_time']="{$list_store['s_time']}-{$list_store['e_time']}";

        }

        $list_store['is_open']=is_open($list_store['s_time'],$list_store['e_time']);


        $this->assign('list_store',$list_store);

        $this->assign('list',$list);
        $this->assign('page',$show);

        $this->assign("is_login",is_login());



        $this->display();

    }
    //前台ajax排序
    public function ajax_sort(){
        $sort_id = I('post.sort_id');
        $opentime=C('OPENTIME');
        if($sort_id == 1){
            $rstinfo = session("rstinfo");
            $map['status']=1;
            $showall=C('SHOWALLSTORE');
            $showall==1 && $map['city_code']=$rstinfo['citycode'];
            $map['yingye_flag']=1;
            $store=M('Store')->where($map)->order('store_id desc')->select();
            $res1=array();
            //点赞
            foreach($store as $k=>$v){
                $san = M('praise')->where('store_id ='.$v['store_id'])->sum('praise');
                $store[$k]['praise'] = $san;
            }
            //取优惠分类
            $cuxiao = M('cuxiao')->select();
            foreach($store as $k=>$v){
               $distance=distanceBetween($rstinfo['lng'],$rstinfo['lat'],$v['lng'],$v['lat']);
//                $scope=(int)C('RST_SCOPE');
                if(!$showall){
                    //计算是否在配送范围
                    $arr_user = array();
                    $arr_user['x'] = $rstinfo['lng'];
                    $arr_user['y'] = $rstinfo['lat'];
                    $res= explode(",",$v['distribution_range']);
                    $arr_text1 = array();
                    $arr_text = array();
                    for($i=0;$i<count($res);$i+=2){
                        $arr_text1['x'] = $res[$i];
                        $arr_text1['y'] = $res[$i+1];
                        $arr_text[] = $arr_text1;
                    }
                    if(PointInPoly($arr_user,$arr_text)) {
                        $yh_arr = array();
                        //遍历促销
                        foreach($cuxiao as $cx){
                            if($cx['store_id'] == $v['store_id']){
                                $yh_arr[] = $cx;
                            }
                        }
                        $v['cuxiao_data'] = $yh_arr;
                        //评论
                        $pl = array();
                        $pl['store_id'] = $v['store_id'];
                        $pinlun_number = M('pingjia')->where($pl)->count("pj_id");
                        $v['pl_number'] = $pinlun_number;

                        $v['is_open']=is_open($v['s_time'],$v['e_time']);

                        $o_time=explode(',',$v['s_time']);
                        $o1_time=explode(',',$v['e_time']);
                        $v['store_open_time']=$o_time[0].':'.$o_time[1].'-'.$o_time[2].':'.$o_time[3].'/'.$o1_time[0].':'.$o1_time[1].'-'.$o1_time[2].':'.$o1_time[3];
                        $v['yingye_time'] =$opentime;
                        $v['juli'] = $distance;
                        $v['store_logo_add'] = get_cover($v['store_logo_id']);
                        //取营业时间
                        $res1[]=$v;
                    }
                    //默认排序
                    $ages = array();
                    foreach ($res1 as $user) {
                        $ages[] = $user['juli'];
                    }
                    array_multisort($ages, SORT_ASC, $res1);
                }else{
                    $yh_arr = array();
                    //遍历优惠
                    foreach($cuxiao as $cx){
                        if($cx['store_id'] == $v['store_id']){
                            $yh_arr[] = $cx;
                        }
                    }
                    $v['cuxiao_data'] = $yh_arr;
                    //评论
                    $pl = array();
                    $pl['store_id'] = $v['store_id'];
                    $pinlun_number = M('pingjia')->where($pl)->count("pj_id");
                    $v['pl_number'] = $pinlun_number;

                    $v['is_open']=is_open($v['s_time'],$v['e_time']);

                    $v['yingye_time'] =$opentime;

                    $o_time=explode(',',$v['s_time']);
                    $o1_time=explode(',',$v['e_time']);
                    $v['store_open_time']=$o_time[0].':'.$o_time[1].'-'.$o_time[2].':'.$o_time[3].'/'.$o1_time[0].':'.$o1_time[1].'-'.$o1_time[2].':'.$o1_time[3];

                    $v['juli'] = $distance;
                    $v['store_logo_add'] = get_cover($v['store_logo_id']);
                    //取营业时间
                    $res1[]=$v;

                    //默认排序
                    $ages = array();
                    foreach ($res1 as $user) {
                        $ages[] = $user['juli'];
                    }
                    array_multisort($ages, SORT_ASC, $res1);
                }

            }

        }elseif($sort_id == 2){
            $rstinfo = session("rstinfo");
            $map['status']=1;
            $showall=C('SHOWALLSTORE');
            $showall==1 && $map['city_code']=$rstinfo['citycode'];
            $map['yingye_flag']=1;
            $store=M('Store')->where($map)->order('store_id desc')->select();
            $res1=array();
            //点赞
            foreach($store as $k=>$v){
                $san = M('praise')->where('store_id ='.$v['store_id'])->sum('praise');
                $store[$k]['praise'] = $san;
            }
            //取优惠分类
            $cuxiao = M('cuxiao')->select();
            foreach($store as $k=>$v){
                $distance=distanceBetween($rstinfo['lng'],$rstinfo['lat'],$v['lng'],$v['lat']);
               // $scope=(int)C('RST_SCOPE');
                //计算是否在配送范围
                if(!$showall){
                    $arr_user = array();
                    $arr_user['x'] = $rstinfo['lng'];
                    $arr_user['y'] = $rstinfo['lat'];
                    $res= explode(",",$v['distribution_range']);
                    $arr_text1 = array();
                    $arr_text = array();
                    for($i=0;$i<count($res);$i+=2){
                        $arr_text1['x'] = $res[$i];
                        $arr_text1['y'] = $res[$i+1];
                        $arr_text[] = $arr_text1;
                    }
                    if(PointInPoly($arr_user,$arr_text)) {
                        $yh_arr = array();
                        //遍历优惠
                        foreach($cuxiao as $cx){
                            if($cx['store_id'] == $v['store_id']){
                                $yh_arr[] = $cx;
                            }
                        }
                        $v['cuxiao_data'] = $yh_arr;

                        //评论
                        $pl = array();
                        $pl['store_id'] = $v['store_id'];
                        $pinlun_number = M('pingjia')->where($pl)->count("pj_id");
                        $v['pl_number'] = $pinlun_number;

                        $v['is_open']=is_open($v['s_time'],$v['e_time']);


                        $v['juli'] = $distance;
                        $v['store_logo_add'] = get_cover($v['store_logo_id']);
                        $o_time=explode(',',$v['s_time']);
                        $o1_time=explode(',',$v['e_time']);
                        $v['store_open_time']=$o_time[0].':'.$o_time[1].'-'.$o_time[2].':'.$o_time[3].'/'.$o1_time[0].':'.$o1_time[1].'-'.$o1_time[2].':'.$o1_time[3];

                        //取营业时间
                        $v['yingye_time'] =$opentime;
                        $res1[]=$v;
                    }
                    //好评排序
                    $ages = array();
                    foreach ($res1 as $user) {
                        $ages[] = $user['pingfen'];
                    }
                    array_multisort($ages,SORT_DESC, $res1);
                }else{
                    $yh_arr = array();
                    //遍历优惠
                    foreach($cuxiao as $cx){
                        if($cx['store_id'] == $v['store_id']){
                            $yh_arr[] = $cx;
                        }
                    }
                    $v['cuxiao_data'] = $yh_arr;

                    //评论
                    $pl = array();
                    $pl['store_id'] = $v['store_id'];
                    $pinlun_number = M('pingjia')->where($pl)->count("pj_id");
                    $v['pl_number'] = $pinlun_number;

                    $v['is_open']=is_open($v['s_time'],$v['e_time']);


                    $v['juli'] = $distance;
                    $v['store_logo_add'] = get_cover($v['store_logo_id']);

                    $o_time=explode(',',$v['s_time']);
                    $o1_time=explode(',',$v['e_time']);
                    $v['store_open_time']=$o_time[0].':'.$o_time[1].'-'.$o_time[2].':'.$o_time[3].'/'.$o1_time[0].':'.$o1_time[1].'-'.$o1_time[2].':'.$o1_time[3];

                    //取营业时间
                    $v['yingye_time'] =$opentime;
                    $res1[]=$v;
                    //好评排序
                    $ages = array();
                    foreach ($res1 as $user) {
                        $ages[] = $user['pingfen'];
                    }
                    array_multisort($ages,SORT_DESC, $res1);
                }

            }
        }elseif($sort_id == 3){
            $rstinfo = session("rstinfo");
            $map['status']=1;
            $showall=C('SHOWALLSTORE');
            $showall==1 && $map['city_code']=$rstinfo['citycode'];
            $map['yingye_flag']=1;
            $store=M('Store')->where($map)->order('store_id desc')->select();
            $res1=array();
            //点赞
            foreach($store as $k=>$v){
                $san = M('praise')->where('store_id ='.$v['store_id'])->sum('praise');
                $store[$k]['praise'] = $san;
            }
            //取优惠分类
            $cuxiao = M('cuxiao')->select();
            foreach($store as $k=>$v){
                $distance=distanceBetween($rstinfo['lng'],$rstinfo['lat'],$v['lng'],$v['lat']);
//                $scope=(int)C('RST_SCOPE');
                if(!$showall){
                    //计算是否在配送范围
                    $arr_user = array();
                    $arr_user['x'] = $rstinfo['lng'];
                    $arr_user['y'] = $rstinfo['lat'];
                    $res= explode(",",$v['distribution_range']);
                    $arr_text1 = array();
                    $arr_text = array();
                    for($i=0;$i<count($res);$i+=2){
                        $arr_text1['x'] = $res[$i];
                        $arr_text1['y'] = $res[$i+1];
                        $arr_text[] = $arr_text1;
                    }
                    if(PointInPoly($arr_user,$arr_text)) {
                        $yh_arr = array();
                        //遍历优惠
                        foreach($cuxiao as $cx){
                            if($cx['store_id'] == $v['store_id']){
                                $yh_arr[] = $cx;
                            }
                        }
                        $v['cuxiao_data'] = $yh_arr;

                        //评论
                        $pl = array();
                        $pl['store_id'] = $v['store_id'];
                        $pinlun_number = M('pingjia')->where($pl)->count("pj_id");
                        $v['pl_number'] = $pinlun_number;

                        $o_time=explode(',',$v['s_time']);
                        $o1_time=explode(',',$v['e_time']);
                        $v['store_open_time']=$o_time[0].':'.$o_time[1].'-'.$o_time[2].':'.$o_time[3].'/'.$o1_time[0].':'.$o1_time[1].'-'.$o1_time[2].':'.$o1_time[3];
                        $v['is_open']=is_open($v['s_time'],$v['e_time']);


                        $v['juli'] = $distance;
                        $v['store_logo_add'] = get_cover($v['store_logo_id']);

                        //取营业时间
                        $v['yingye_time'] =$opentime;
                        $res1[]=$v;
                    }
                    //人均消费排序
                    $ages = array();
                    foreach ($res1 as $user) {
                        $ages[] = $user['qisong_price']/2;
                    }
                    array_multisort($ages,SORT_ASC, $res1);
                }else{
                    $yh_arr = array();
                    //遍历优惠
                    foreach($cuxiao as $cx){
                        if($cx['store_id'] == $v['store_id']){
                            $yh_arr[] = $cx;
                        }
                    }
                    $v['cuxiao_data'] = $yh_arr;

                    //评论
                    $pl = array();
                    $pl['store_id'] = $v['store_id'];
                    $pinlun_number = M('pingjia')->where($pl)->count("pj_id");
                    $v['pl_number'] = $pinlun_number;

                    $v['is_open']=is_open($v['s_time'],$v['e_time']);


                    $v['juli'] = $distance;
                    $v['store_logo_add'] = get_cover($v['store_logo_id']);

                    $o_time=explode(',',$v['s_time']);
                    $o1_time=explode(',',$v['e_time']);
                    $v['store_open_time']=$o_time[0].':'.$o_time[1].'-'.$o_time[2].':'.$o_time[3].'/'.$o1_time[0].':'.$o1_time[1].'-'.$o1_time[2].':'.$o1_time[3];

                    //取营业时间
                    $v['yingye_time'] =$opentime;
                    $res1[]=$v;

                    //人均消费排序
                    $ages = array();
                    foreach ($res1 as $user) {
                        $ages[] = $user['qisong_price']/2;
                    }
                    array_multisort($ages,SORT_ASC, $res1);
                }

            }
        }elseif($sort_id == 4){
            $rstinfo = session("rstinfo");
            $map['status']=1;
            $showall=C('SHOWALLSTORE');
            $showall==1 && $map['city_code']=$rstinfo['citycode'];
            $map['yingye_flag']=1;
            $store=M('Store')->where($map)->order('store_id desc')->select();
            $res1=array();
            //点赞
            foreach($store as $k=>$v){
                $san = M('praise')->where('store_id ='.$v['store_id'])->sum('praise');
                $store[$k]['praise'] = $san;
            }
            //取优惠分类
            $cuxiao = M('cuxiao')->select();
            foreach($store as $k=>$v){
                $distance=distanceBetween($rstinfo['lng'],$rstinfo['lat'],$v['lng'],$v['lat']);
                //$scope=(int)C('RST_SCOPE');
                //计算是否在配送范围
                if(!$showall){
                    $arr_user = array();
                    $arr_user['x'] = $rstinfo['lng'];
                    $arr_user['y'] = $rstinfo['lat'];
                    $res= explode(",",$v['distribution_range']);
                    $arr_text1 = array();
                    $arr_text = array();
                    for($i=0;$i<count($res);$i+=2){
                        $arr_text1['x'] = $res[$i];
                        $arr_text1['y'] = $res[$i+1];
                        $arr_text[] = $arr_text1;
                    }
                    if(PointInPoly($arr_user,$arr_text)) {
                        $yh_arr = array();
                        //遍历优惠
                        foreach($cuxiao as $cx){
                            if($cx['store_id'] == $v['store_id']){
                                $yh_arr[] = $cx;
                            }
                        }
                        $v['cuxiao_data'] = $yh_arr;
                        //评论
                        $pl = array();
                        $pl['store_id'] = $v['store_id'];
                        $pinlun_number = M('pingjia')->where($pl)->count("pj_id");
                        $v['pl_number'] = $pinlun_number;


                        $o_time=explode(',',$v['s_time']);
                        $o1_time=explode(',',$v['e_time']);
                        $v['store_open_time']=$o_time[0].':'.$o_time[1].'-'.$o_time[2].':'.$o_time[3].'/'.$o1_time[0].':'.$o1_time[1].'-'.$o1_time[2].':'.$o1_time[3];
                        $v['is_open']=is_open($v['s_time'],$v['e_time']);

                        $v['juli'] = $distance;
                        $v['store_logo_add'] = get_cover($v['store_logo_id']);

                        //取营业时间
                        $v['yingye_time'] =$opentime;
                        $res1[]=$v;
                    }

                }else{
                    $yh_arr = array();
                    //遍历优惠
                    foreach($cuxiao as $cx){
                        if($cx['store_id'] == $v['store_id']){
                            $yh_arr[] = $cx;
                        }
                    }
                    $v['cuxiao_data'] = $yh_arr;
                    //评论
                    $pl = array();
                    $pl['store_id'] = $v['store_id'];
                    $pinlun_number = M('pingjia')->where($pl)->count("pj_id");
                    $v['pl_number'] = $pinlun_number;

                    $o_time=explode(',',$v['s_time']);
                    $o1_time=explode(',',$v['e_time']);
                    $v['store_open_time']=$o_time[0].':'.$o_time[1].'-'.$o_time[2].':'.$o_time[3].'/'.$o1_time[0].':'.$o1_time[1].'-'.$o1_time[2].':'.$o1_time[3];

                    $v['is_open']=is_open($v['s_time'],$v['e_time']);

                    $v['juli'] = $distance;
                    $v['store_logo_add'] = get_cover($v['store_logo_id']);

                    //取营业时间
                    $v['yingye_time'] =$opentime;
                    $res1[]=$v;

                    //起送价排序
                    $ages = array();
                    foreach ($res1 as $user) {
                        $ages[] = $user['qisong_price'];
                    }
                    array_multisort($ages,SORT_ASC, $res1);
                }

            }
        }

        $this->ajaxReturn($res1);

    }

    //前台ajax搜索
    public function ajax_search(){
        $search_num = I('post.search_num');
        if(count($search_num)>0){
            $rstinfo = session("rstinfo");         //去除地址信息
            $map['store_name'] = array('like', '%'.(string)I('post.search_num').'%');     //模糊查询
            $map['status']=1;
            $map['city_code']=$rstinfo['citycode'];
            $map['yingye_flag']=1;
            $open=C('OPENTIME');                //常量   营业时间
            $open=explode('-',$open);
            $store=M('Store')->where($map)->order('store_id desc')->select();          //查询结果


            foreach($store as $l=>$n){
                $san = M('praise')->where('store_id ='.$n['store_id'])->sum('praise');     //遍历每个店铺点赞总数
                $store[$l]['praise'] = $san;
            }

            //取优惠分类
            $cuxiao = M('cuxiao')->select();
            $res1 =array();
            foreach($store as $k=>$v){
                $distance=distanceBetween($rstinfo['lng'],$rstinfo['lat'],$v['lng'],$v['lat']);      //取$rstionfo下的精度纬度   和$store下的精度纬度

                $arr_user = array();              //计算是否在配送范围
                $arr_user['x'] = $rstinfo['lng'];
                $arr_user['y'] = $rstinfo['lat'];

                $res= explode(",",$v['distribution_range']);       //分隔店铺配送范围

                $arr_text1 = array();     //坐标经纬度范围
                $arr_text = array();      //坐标范围

                for($i=0;$i<count($res);$i+=2){
                    $arr_text1['x'] = $res[$i];
                    $arr_text1['y'] = $res[$i+1];
                    $arr_text[] = $arr_text1;
                }

                if(PointInPoly($arr_user,$arr_text)) {          //判断坐标在不在指定坐标范围


                    $yh_arr = array();
                    //遍历优惠
                    foreach($cuxiao as $cx){
                        if($cx['store_id'] == $v['store_id']){
                            $yh_arr[] = $cx;
                        }
                    }

                    $v['cuxiao_data'] = $yh_arr;


                    //评论
                    $pl = array();
                    $pl['store_id'] = $v['store_id'];
                    $pinlun_number = M('pingjia')->where($pl)->count("pj_id");      //评价查询

                    $v['pl_number'] = $pinlun_number;

                    $v['is_open']=is_open($open[0],$open[1]);



                    $v['juli'] = $distance;

                    $v['store_logo_add'] = get_cover($v['store_logo_id']);
                    //取营业时间
                    $v['yingye_time'] =$open[0].'-'.$open[1];

                    $res1[]=$v;
                }
                //默认排序
                $ages = array();
                foreach ($res1 as $user) {
                    $ages[] = $user['juli'];
                }
                array_multisort($ages, SORT_ASC, $res1);
            }

        }
        $this->ajaxReturn($res1);
    }





    /**
     * 前台筛选排序
     */
    public function sortOrder()
    {
        $options = I('post.type');

        if($options == 'Fastest'){
            $order = 's.store_id desc';
        }
        if($options == 'Price'){
            $order ='s.qisong_price desc';
        }
        $rstinfo = session("rstinfo");
        $store = M('Store')->alias("s")->join('LEFT JOIN wm_picture AS p ON s.store_logo_id=p.id')->field("s.*,p.path")->order($order)->select();
        if (!empty($store)) {
            $res = array();
            foreach ($store as $k => $v) {
                $distance = distanceBetween($rstinfo['lng'], $rstinfo['lat'], $v['lng'], $v['lat']);
                $s1 = HPointInPoly($rstinfo['lng'], $rstinfo['lat'],$v['distribution_range']);
                if($s1==true){
                    $where['store_id']=$v['store_id'];
                    $where['year']=date("Y",time());
                    $where['month']=date("n",time());
                    $sum = M('order')->where($where)->sum('total')/M('order')->where($where)->count('order_id');
                    $quantity=M("GoodsCount")->where($where)->sum('quantity');
                    $v['sale_num']=empty($quantity)?0:$quantity;
                    $v['is_open']=is_open($v['s_time'],$v['e_time']);
                    $v['youhui']=$this->get_youhui($v['store_id']);
                    $pf=M("pingjia")->where("store_id=".$v['store_id'])->avg("pingfen");
                    $v['pingfen']=empty($pf)?0:($pf/5)*100;
                    array_push($v,round($sum,1));
                    $res[] = $v;
                }
            }
            $this->ajaxReturn($res);
        } else {
            $this->ajaxReturn(array("status" => 0));
        }

    }

    /**
     * 外卖搜索
     */
    public function searchRestaurant()
    {
        $opt = I("get.");
        $rstinfo = session("rstinfo");
        if (!empty($opt['word'])) {
            $str = "";
            //汉字检索
            if ($opt['type'] == 'fullword') {
                $str = $opt['word'];
                $map['store_name'] = array('like', '%' . $str . '%');
                $map2['cm_name'] = array('like', '%' . $str . '%');
            } else {
                //拼音检索
                $w = rtrim($opt['word'], ",");
                $exp = explode(",", $w);
                foreach ($exp as $wordv) {
                    $str .= chr($wordv);
                }
                $map['store_name_py'] = array('like', '%' . $str . '%');
                $map2['cm_name_py'] = array('like', '%' . $str . '%');
            }
            $map['city_code'] = $map3['city_code'] = $rstinfo['citycode'];
            $store = M('Store')->where($map)->select();

            if (!empty($store)) {
                $res = array();
                foreach ($store as $k => $v) {
                    $distance = distanceBetween($rstinfo['lng'], $rstinfo['lat'], $v['lng'], $v['lat']);
                    $s1 = HPointInPoly($rstinfo['lng'], $rstinfo['lat'],$v['distribution_range']);
                    if($s1==true){
                        $res[] = $v;
                    }
                }
                //$this->ajaxReturn(M("Store")->_sql());
            }
            $store3 = M('Store')->where($map3)->select();
            if (!empty($store3)) {
                $res3 = $ids = array();
                foreach ($store3 as $k3 => $v3) {
                    $distance3 = distanceBetween($rstinfo['lng'], $rstinfo['lat'], $v3['lng'], $v3['lat']);
                    $s1 = HPointInPoly($rstinfo['lng'], $rstinfo['lat'],$v['distribution_range']);
                    if($s1==true){
                        $res3[] = $v3;
                    }
                }
                foreach ($res3 as $resv) {
                    $ids[] = $resv['store_id'];
                }
                $map2['store_id'] = array('in', $ids);
                $cm = M("Canming")->where($map2)->select();
                foreach ($cm as & $cmv) {
                    $sname = M("Store")->getFieldByStoreId($cmv['store_id'], 'store_name');
                    $cmv['store_name'] = $sname;
                }
            }
            //if(!empty($opt['show']) && $opt['show']=='new') $this->display('result');
            $this->ajaxReturn(array($res, $cm));
        }
    }

    public function postSearchRestaurant()
    {

        $opt = I('post.');
        //dump($opt);
        $rstinfo = session("rstinfo");
        $map['city_code'] = $map3['city_code'] = $rstinfo['citycode'];
        $map['store_name'] = array('like', '%' . $opt['word'] . '%');
        $store = M('Store')->where($map)->select();
        //dump($aa);
        if (!empty($store)) {

            $res = array();
            foreach ($store as $k => $v) {
                $distance = distanceBetween($rstinfo['lng'], $rstinfo['lat'], $v['lng'], $v['lat']);
                $s1 = HPointInPoly($rstinfo['lng'], $rstinfo['lat'],$v['distribution_range']);
                if($s1==true){
                    $where['store_id']=$v['store_id'];
                    $where['year']=date("Y",time());
                    $where['month']=date("n",time());



                    $quantity=M("GoodsCount")->where($where)->sum('quantity');
                    $v['youhui']=$this->get_youhui($v['store_id']);
                    $v['sale_num']=empty($quantity)?0:$quantity;
                    $v['is_open']=is_open($v['s_time'],$v['e_time']);
                    $pf=M("pingjia")->where("store_id=".$v['store_id'])->avg("pingfen");
                    $v['pingfen']=empty($pf)?0:($pf/5)*100;
                    $res[] = $v;
                }
            }
        }
        //菜品搜索
        $store3 = M('Store')->where($map3)->select();
        if (!empty($store3)) {
            $res3 = $ids = array();
            foreach ($store3 as $k3 => $v3) {
                $distance3 = distanceBetween($rstinfo['lng'], $rstinfo['lat'], $v3['lng'], $v3['lat']);
                $s1 = HPointInPoly($rstinfo['lng'], $rstinfo['lat'],$v['distribution_range']);
                if($s1==true){
                    $res3[] = $v3;
                }
            }
            foreach ($res3 as $resv) {
                $ids[] = $resv['store_id'];
            }
            $map2['store_id'] = array('in', $ids);
            $map2['cm_name'] = array('like', '%' . $opt['word'] . '%');
            $cm = M("Canming")->where($map2)->select();
            foreach ($cm as & $cmv) {
                $sname = M("Store")->getFieldByStoreId($cmv['store_id'], 'store_name');
                $cmv['store_name'] = $sname;
            }
        }
        $this->assign("caipin", $cm);
        $this->assign("is_login", is_login());
        $this->assign('_position', $opt['position']);
        $this->assign('store_list', $res);
        $this->display('result');
    }
    public static function get_youhui($storeid){
        $huodong=M("youhui");
        $map['store_id']=$storeid;
        $map['status']=1;
        $res=$huodong->where($map)->select();
        $colors=array("cc22e2","52af27","ff4e00","dc0411","9071cb");
        foreach($res as  $k=> & $v){
            $v['color']=$colors[$k];
        }
        return $res;
    }

    /**
     * 当前餐厅url存入session
     */
    public function saveRstSession()
    {
        $url = I("get.restaurant_url");
        if (!empty($url)) session("restaurant_url", $url);
    }

    public function getRstSession()
    {
        $url = session("restaurant_url");
        $this->ajaxReturn($url);
    }









    public function searchpage(){
        $mdmc = I('Key');
        $store1 = M('store');
        $map['status']=1;
        $map['yingye_flag']=1;
        if(empty($mdmc)){
            $store=$store1->where($map)->order('store_id desc')->select();
            $res=array();
            foreach($store as $k=>$v){
                    $where['store_id']=$v['store_id'];
                    $where['year']=date("Y",time());
                    $where['month']=date("n",time());
                    $sum = M('order')->where($where)->Sum('total')/ M('order')->where($where)->Count('order_id');
                    $quantity=M("GoodsCount")->where($where)->sum('quantity');
                    $v['sale_num']=empty($quantity)?0:$quantity;
                    $v['is_open']=is_open($v['s_time'],$v['e_time']);
                    $pf=M("pingjia")->where("store_id=".$v['store_id'])->avg("pingfen");
                    $v['pingfen']=empty($pf)?0:($pf/5)*100;
                    array_push($v,round($sum,1));
                    $res[]=$v;
            }
            $this->assign('store_list',$res);
        } else{

            $map['store_name']=array('like',"%$mdmc%");
            $store = $store1->where($map)->select();
            $res = array();
            foreach($store as $a=>$b){
                $where['store_id'] = $b['store_id'];
                $sum = M('order')->where($where)->Sum('total')/ M('order')->where($where)->Count('order_id');
                array_push($b,round($sum,1));
                $res[] = $b;
            }
            $this->assign('store_list',$res);
        }
        $bie = I('bie');
        if(!empty($bie)){
            $map['store_name']=array('like',"%$bie%");
            $store = $store1->where($map)->select();
            $res = array();
            foreach($store as $a => $b){
                $where['store_id'] = $b['store_id'];
                $sum = M('order')->where($where)->Sum('total')/M('order')->where($where)->Count('order_id');
                array_push($b,round($sum,1));
                $res[] = $b;
            }
            $this->assign('store_list',$res);
        }
        $this->display();
    }
    public function praise(){
        $uid = I('uid');
        $store_id = I('store_id');
        $map['uid'] = $uid;
        $map['store_id'] = $store_id;
        $map['praise'] = 1;
        if( M('praise')->add($map) != false){
            $pr = M('praise')->where('store_id ='.$store_id)->sum('praise');
            $this->ajaxReturn($pr);
        }
    }
    public function del()
    {
        $uid = I('uid');
        $store_id = I('store_id');
        $map['uid'] = $uid;
        $map['store_id'] = $store_id;
        if (M('praise')->where($map)->delete() != false) {
            $pr = M('praise')->where('store_id =' . $store_id)->sum('praise');
            if($pr == ''){
                $this->ajaxReturn("noData");
            }
            $this->ajaxReturn($pr);
        }
    }


}


//$v['yingye_is_two']=yingye_is_two($v['s_time'])


























