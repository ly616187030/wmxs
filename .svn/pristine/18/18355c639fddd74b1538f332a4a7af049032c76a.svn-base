<?php
namespace Admin\Controller;
use Think\Model;

class SettlementController extends AdminController
{
    public function index(){
            $post = I('');
            //dump($post);
            if(!empty($post['stime'])){
                if(!empty($post['etime'])){
                    $where['s.dtime']=array('between',array(strtotime($post['stime']),strtotime($post['etime'])+2635200));
                }else{
                    $where['s.dtime']=array('egt',strtotime($post['stime']));
                }
            }else{
                if(!empty($post['etime'])){
                    $where['s.dtime']=array('elt',strtotime($post['etime'])+2635200);
                }
            }

            if($post['xuanxiang']==0){
                if(!empty($post['dailishang'])){
                    $whereuid['from_agent_id'] = $post['dailishang'];
                    $qiyeid = M('company')->where($whereuid)->getField('id',true);
                    if($qiyeid){
                        $where['s.founder_id']=array('in',$qiyeid);
                    }
                }
            }else{
                if(!empty($post['qiye'])){
                    $where['s.founder_id']=$post['qiye'];
                }
            }
            if(!empty($post['qiye'])){
                $where['s.founder_id']=$post['qiye'];
            }
            if(!empty($post['jiesuan'])){
                $where['s.sett_status']=$post['jiesuan'];
            }
            if (is_agent()){
                $agent1['from_agent_id'] = is_login();
                $qyid = M('company')->where($agent1)->getField('id',true);
                if($qyid){
                    $where['s.founder_id']=array('in',$qyid);
                }else{
                    $where['s.founder_id']='';
                }
            }
            $settlement =M('order_settlement');
            $count = $settlement->alias('s')->where($where)->count();
            $Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
            $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
            $Page->setConfig('first','首页');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('last','末页');
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $Page->parameter=I('');
            $show = $Page->show();// 分页显示输出
            $list = $settlement
                ->alias('s')
                ->join('__COMPANY__ AS c ON s.founder_id=c.id')
                ->where($where)
                ->field('s.*,c.c_name')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('s.ctime DESC')
                ->select();

            //总计
            $shangyueyouxiao = $settlement->alias('s')->where($where)->sum('this_valid_order');
            $shangyuequanbu = $settlement->alias('s')->where($where)->sum('this_all_valid_order');
            $quanbuyouxiao = $settlement->alias('s')->where($where)->sum('all_valid_order');
            $quanbu = $settlement->alias('s')->where($where)->sum('all_order');
            $total = $settlement->alias('s')->where($where)->sum('total_fee');
            $this->assign('shangyueyouxiao',$shangyueyouxiao);
            $this->assign('shangyuequanbu',$shangyuequanbu);
            $this->assign('quanbuyouxiao',$quanbuyouxiao);
            $this->assign('quanbu',$quanbu);
            $this->assign('total',$total);
            $this->assign('settlement',$list);
            $this->assign('post',$post);
            $this->assign('page',$show);
        if(is_administrator()){//管理员登录查询所有代理商以及企业
            $wheredaili['user_type'] ='agent';
            $daili = M('admin_user')->where($wheredaili)->select();
            $qiye = M('company')->select();
        }else{//代理商登陆查询其旗下所有企业
            $whereuid['from_agent_id'] = is_login();
            $qiye = M('company')->where($whereuid)->select();
        }
        $this->assign('daili',$daili);
        $this->assign('qiye',$qiye);
        $this->assign('uid',is_login());
        $this->display();
    }
    public function indexqiyezong(){
        if(IS_POST || IS_GET){
            $post = I('');
            //dump($post);
            $stime = strtotime($post['stime']);
            $etime = strtotime($post['etime'])+86399;

            if(!empty($post['qiye'])){
                $all_valid['founder_id'] =$post['qiye'];
                $all['founder_id'] = $post['qiye'];
                $map['id'] = $post['qiye'];
            }
            if(is_agent()){
                $map['from_agent_id'] = is_login();
            }

            $count =  M('company')->where($map)->count();
            $Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
            $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
            $Page->setConfig('first','首页');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('last','末页');
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $Page->parameter=I('');
            $show = $Page->show();// 分页显示输出
            $qiye11 = M('company')->where($map)->limit($Page->firstRow.','.$Page->listRows)->select();
            if(!empty($post['stime'])){
                if(!empty($post['etime'])){//开始结束时间都不为空
                    $all_valid['complete_time'] = array('between', array($stime,$etime));
                    $all['xd_time'] = array('between', array($stime,$etime));
                    if(!empty($post['qiye'])){
                        foreach ($qiye11 as $k=>$v){
                            $all_valid['status'] = array('egt',5);
                            $all_valid_order = M('order')->where($all_valid)->count();
                            $qiye11[$k]['all_valid_order'] = $all_valid_order;
                            $all_order = M('order')->where($all)->count();
                            $qiye11[$k]['all_order'] = $all_order;
                        }
                    }else{
                        foreach ($qiye11 as $k=>$v){
                            $all_valid['complete_time'] = array('between', array($stime,$etime));
                            $all_valid['status'] = array('egt',5);
                            $all_valid['founder_id'] = $v['id'];
                            $all_valid_order = M('order')->where($all_valid)->count();
                            $qiye11[$k]['all_valid_order'] = $all_valid_order;
                            $all['xd_time'] = array('between', array($stime,$etime));
                            $all['founder_id'] = $v['id'];
                            $all_order = M('order')->where($all)->count();
                            $qiye11[$k]['all_order'] = $all_order;
                        }
                    }
                }else{//开始时间有，结束时间为空
                    if(!empty($post['qiye'])){
                        foreach ($qiye11 as $k=>$v){
                            $all_valid['complete_time'] = array('egt',strtotime($post['stime']));
                            $all['xd_time'] = array('egt',strtotime($post['stime']));
                            $all_valid['status'] = array('egt',5);
                            $all_valid_order = M('order')->where($all_valid)->count();
                            $qiye11[$k]['all_valid_order'] = $all_valid_order;
                            $all_order = M('order')->where($all)->count();
                            $qiye11[$k]['all_order'] = $all_order;
                        }
                    }else{
                        foreach ($qiye11 as $k=>$v){
                            $all_valid['complete_time'] = array('egt',strtotime($post['stime']));
                            $all['xd_time'] = array('egt',strtotime($post['stime']));
                            $all_valid['status'] = array('egt',5);
                            $all_valid['founder_id'] = $v['id'];
                            $all_valid_order = M('order')->where($all_valid)->count();
                            $qiye11[$k]['all_valid_order'] = $all_valid_order;
                            $all['founder_id'] = $v['id'];
                            $all_order = M('order')->where($all)->count();
                            $qiye11[$k]['all_order'] = $all_order;
                        }
                    }
                }
            }else{//起始时间为空
                if(!empty($post['etime'])){//无起始时间，有结束时间
                    if(!empty($post['qiye'])){
                        $all_valid['complete_time'] = array('elt',strtotime($post['etime']));
                        $all['xd_time'] = array('elt',strtotime($post['etime']));
                        foreach ($qiye11 as $k=>$v){
                            $all_valid['status'] = array('egt',5);
                            $all_valid_order = M('order')->where($all_valid)->count();
                            $qiye11[$k]['all_valid_order'] = $all_valid_order;
                            $all_order = M('order')->where($all)->count();
                            $qiye11[$k]['all_order'] = $all_order;
                        }
                    }else{
                        foreach ($qiye11 as $k=>$v){
                            $all_valid['complete_time'] = array('elt',strtotime($post['etime']));
                            $all_valid['status'] = array('egt',5);
                            $all_valid['founder_id'] = $v['id'];
                            $all_valid_order = M('order')->where($all_valid)->count();
                            $qiye11[$k]['all_valid_order'] = $all_valid_order;
                            $all['xd_time'] = array('elt',strtotime($post['etime']));
                            $all['founder_id'] = $v['id'];
                            $all_order = M('order')->where($all)->count();
                            $qiye11[$k]['all_order'] = $all_order;
                        }
                    }
                }else{//无时间
                    if(!empty($post['qiye'])){
                        $stime = mktime(0, 0, 0, date("m"), 1, date("Y"));//当前月份的头
                        $etime = mktime(23, 59, 59, date("m"), date("t"), date("Y"));//当前月份的尾
                        foreach ($qiye11 as $k=>$v){
                            $all_valid['complete_time'] = array('between', array($stime,$etime));
                            $all_valid['status'] = array('egt',5);
                            $all_valid_order = M('order')->where($all_valid)->count();
                            $qiye11[$k]['all_valid_order'] = $all_valid_order;
                            $all['xd_time'] = array('between', array($stime,$etime));
                            $all_order = M('order')->where($all)->count();
                            $qiye11[$k]['all_order'] = $all_order;
                        }
                    }else{
                        $stime = mktime(0, 0, 0, date("m"), 1, date("Y"));//当前月份的头
                        $etime = mktime(23, 59, 59, date("m"), date("t"), date("Y"));//当前月份的尾
                        foreach ($qiye11 as $k=>$v){
                            $all_valid['complete_time'] = array('between', array($stime,$etime));
                            $all_valid['status'] = array('egt',5);
                            $all_valid['founder_id'] = $v['id'];
                            $all_valid_order = M('order')->where($all_valid)->count();
                            $qiye11[$k]['all_valid_order'] = $all_valid_order;
                            $all['xd_time'] = array('between', array($stime,$etime));
                            $all['founder_id'] = $v['id'];
                            $all_order = M('order')->where($all)->count();
                            $qiye11[$k]['all_order'] = $all_order;
                        }
                    }
                }
            }


            $this->assign('qiye11',$qiye11);
            $this->assign('page',$show);
            $this->assign('post',$post);
        }
        if(is_administrator()){//管理员登录查询所有代理商以及企业
            $wheredaili['user_type'] ='agent';
            $daili = M('admin_user')->where($wheredaili)->select();
            $qiye = M('company')->select();
        }else{//代理商登陆查询其旗下所有企业
            $whereuid['from_agent_id'] = is_login();
            $qiye = M('company')->where($whereuid)->select();
        }
        $this->assign('daili',$daili);
        $this->assign('qiye',$qiye);
        $this->assign('uid',is_login());
        $this->display();
    }

    public function indexdailizong(){
        $post = I('');
        //dump($post);
        $stime = strtotime($post['stime']);
        $etime = strtotime($post['etime'])+86399;
        if(!empty($post['dailishang'])){
            if(!empty($post['stime'])){
                if(!empty($post['etime'])){
                    $all_valid['complete_time'] = array('between', array($stime,$etime));
                    $all['xd_time'] = array('between', array($stime,$etime));
                }else{
                    $all_valid['complete_time'] = array('egt',$stime);
                    $all['xd_time'] = array('egt', $stime);
                }
            }else{
                if(!empty($post['etime'])){
                    $all_valid['complete_time'] = array('elt',$etime);
                    $all['xd_time'] = array('elt',$etime);
                }else{
                    $stime = mktime(0, 0, 0, date("m"), 1, date("Y"));//当前月份的头
                    $etime = mktime(23, 59, 59, date("m"), date("t"), date("Y"));//当前月份的尾
                    $all_valid['complete_time'] = array('egt',$stime);
                    $all['xd_time'] = array('egt', $stime);
                }
            }
            $dailishang = $post['dailishang'];
            $whereqiye['from_agent_id'] =$dailishang;
            $count =  M('company')->where($whereqiye)->count();
            $Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
            $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
            $Page->setConfig('first','首页');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('last','末页');
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $Page->parameter=I('');
            $show = $Page->show();// 分页显示输出
            $qiye11 =M('company')->where($whereqiye)->limit($Page->firstRow.','.$Page->listRows)->select();
            if($qiye11){
                foreach($qiye11 as $k=>$v){
                    $all_valid['status'] = array('egt',5);
                    $all_valid['founder_id'] = $v['id'];
                    $all_valid_order = M('order')->where($all_valid)->count();
                    $qiye11[$k]['all_valid_order'] = $all_valid_order;
                    $all['founder_id'] = $v['id'];
                    $all_order = M('order')->where($all)->count();
                    $qiye11[$k]['all_order'] = $all_order;
                }
            }
        }else{
            if(!empty($post['stime'])){
                if(!empty($post['etime'])){
                    $all_valid['complete_time'] = array('between', array($stime,$etime));
                    $all['xd_time'] = array('between', array($stime,$etime));
                }else{
                    $all_valid['complete_time'] = array('egt',$stime);
                    $all['xd_time'] = array('egt', $stime);
                }
            }else{
                if(!empty($post['etime'])){
                    $all_valid['complete_time'] = array('elt',$etime);
                    $all['xd_time'] = array('elt',$etime);
                }else{
                    $stime = mktime(0, 0, 0, date("m"), 1, date("Y"));//当前月份的头
                    $etime = mktime(23, 59, 59, date("m"), date("t"), date("Y"));//当前月份的尾
                    $all_valid['complete_time'] = array('egt',$stime);
                    $all['xd_time'] = array('egt', $stime);
                }
            }
            $count =  M('company')->count();
            $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
            $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
            $Page->setConfig('first','首页');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('last','末页');
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $Page->parameter=I('');
            $show = $Page->show();// 分页显示输出
            $qiye11 =M('company')->limit($Page->firstRow.','.$Page->listRows)->select();
            $stime = mktime(0, 0, 0, date("m"), 1, date("Y"));//当前月份的头
            $etime = mktime(23, 59, 59, date("m"), date("t"), date("Y"));//当前月份的尾
            foreach ($qiye11 as $k=>$v){
                $all_valid['status'] = array('egt',5);
                $all_valid['founder_id'] = $v['id'];
                $all_valid_order = M('order')->where($all_valid)->count();
                $qiye11[$k]['all_valid_order'] = $all_valid_order;
                $all['founder_id'] = $v['id'];
                $all_order = M('order')->where($all)->count();
                $qiye11[$k]['all_order'] = $all_order;
            }
        }

        if(is_administrator()){//管理员登录查询所有代理商以及企业
            $wheredaili['user_type'] ='agent';
            $daili = M('admin_user')->where($wheredaili)->select();
            $qiye = M('company')->select();
        }else{//代理商登陆查询其旗下所有企业
            $whereuid['from_agent_id'] = is_login();
            $qiye = M('company')->where($whereuid)->select();
        }
        $this->assign('daili',$daili);
        $this->assign('qiye',$qiye);
        $this->assign('qiye11',$qiye11);
        $this->assign('page',$show);
        $this->assign('post',$post);
        $this->assign('uid',is_login());
        $this->display();
    }
}