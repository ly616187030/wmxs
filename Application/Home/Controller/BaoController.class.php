<?php

/**

 * Created by CK.

 * Auther: <job_ck@126.com>

 * Date: 2015/11/03

 * Time: 16:44

 */
namespace Home\Controller;
use Think\Controller;

class BaoController extends HomeController {
    protected function _initialize(){
        parent::_initialize();
        $this->assign('_controller',CONTROLLER_NAME);
    }
    public function index(){
        $this->login();
        $uid = UID;
        $time=time();
        $bao = M('user_bao');//实例化order对象
        $phone = M('ucenter_member')->where('id='.$uid)->field('mobile')->find();
        $where['user_phone']=$phone['mobile'];
            $count = $bao->where($where)->count();// 查询满足要求的总记录数
            $Page = new \Think\Page($count,9);// 实例化分页类 传入总记录数和每页显示的记录数
        $map['a.user_phone']=$phone['mobile'];
            $list = $bao
                ->alias('a')
                ->join('LEFT JOIN wm_bao b ON a.bao_id=b.bao_id')
                ->where($map)
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('a.expire_time desc')
                ->field('a.user_bao_price bao_price,a.expire_time end_time,b.send_time send_time,a.condition_desc bao_condition_desc,a.status status')
                ->select();
            $Page->setConfig('prev',' 上一页 ');
            $Page->setConfig('next','下一页');
            $Page->setConfig('theme', '%UP_PAGE% <span class="current">%NOW_PAGE%</span> %DOWN_PAGE%');
            $show = $Page->show();// 分页显示输出
            $this->assign('page',$show);// 赋值分页输出
            $this->assign('count',$count);//将订单的总数量返回到HTML
            $this->assign("_position","我的红包");
        //查询未使用红包
        $wei['user_phone'] = $phone['mobile'];
        $wei['status'] = 0;
        $wei['expire_time']=array('gt',$time);
        $weishiyong = $bao->where($wei)->count();
        $this->assign('wei',$weishiyong);

        //查询已使用红包
        $yi['user_phone'] = $phone['mobile'];
        $yi['status'] = 1;
        $yishiyong = $bao->where($yi)->count();
        $this->assign('yi',$yishiyong);

        //查询已过期红包
        $guo['user_phone'] = $phone['mobile'];
        $guo['status'] = 0;
        $guo['expire_time']=array('lt',$time);
        $guuoqi = $bao->where($guo)->count();
        $this->assign('guoqi',$guuoqi);
        $this->assign('is_login',is_login());
        $this->assign('hongbao',$list);
        $this->assign('time',$time);
        $this->display();
    }
    public function weishiyong(){
        $uid = UID;
        $time=time();
        $bao = M('user_bao');//实例化order对象
        $phone = M('ucenter_member')->where('id='.$uid)->field('mobile')->find();
        $where['user_phone']=$phone['mobile'];
        $count = $bao->where($where)->count();// 查询满足要求的总记录数
        //查询未使用红包
        $wei['user_phone'] = $phone['mobile'];
        $wei['status'] = 0;
        $wei['expire_time']=array('gt',$time);
        $weishiyong = $bao->where($wei)->count();
        $this->assign('wei',$weishiyong);

        $Page = new \Think\Page($weishiyong,9);// 实例化分页类 传入总记录数和每页显示的记录数
        $weisy['a.user_phone'] = $phone['mobile'];
        $weisy['a.status'] = 0;
        $weisy['a.expire_time']=array('gt',$time);
        $list = $bao
            ->alias('a')
            ->join('LEFT JOIN wm_bao b ON a.bao_id=b.bao_id')
            ->where($weisy)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('a.expire_time desc')
            ->field('a.user_bao_price bao_price,a.expire_time end_time,b.send_time send_time,a.condition_desc bao_condition_desc,a.user_bao_id')
            ->select();
        $Page->setConfig('prev',' 上一页 ');
        $Page->setConfig('next','下一页');
        $Page->setConfig('theme', '%UP_PAGE% <span class="current">%NOW_PAGE%</span> %DOWN_PAGE%');
        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('count',$count);//将订单的总数量返回到HTML
        $this->assign("_position","未使用红包");

        //查询已使用红包
        $yi['user_phone'] = $phone['mobile'];
        $yi['status'] = 1;
        $yishiyong = $bao->where($yi)->count();
        $this->assign('yi',$yishiyong);

        //查询已过期红包
        $guo['user_phone'] = $phone['mobile'];
        $guo['status'] = 0;
        $guo['expire_time']=array('lt',$time);
        $guuoqi = $bao->where($guo)->count();
        $this->assign('guoqi',$guuoqi);

        $this->assign('hongbao',$list);
        $this->assign('time',$time);
        $this->display();
    }

    public function yishiyong(){
        $uid = UID;
        $time=time();
        $bao = M('user_bao');//实例化order对象
        $phone = M('ucenter_member')->where('id='.$uid)->field('mobile')->find();
        $where['user_phone']=$phone['mobile'];
        //查询已使用红包
        $yi['user_phone'] = $phone['mobile'];
        $yi['status'] = 1;
        $yishiyong = $bao->where($yi)->count();
        $this->assign('yi',$yishiyong);

        $count = $bao->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($yishiyong,9);// 实例化分页类 传入总记录数和每页显示的记录数
        $yisy['a.user_phone'] = $phone['mobile'];
        $yisy['a.status'] = 1;
        $list = $bao
            ->alias('a')
            ->join('LEFT JOIN wm_bao b ON a.bao_id=b.bao_id')
            ->where($yisy)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('a.expire_time desc')
            ->field('a.user_bao_price bao_price,a.expire_time end_time,b.send_time send_time,a.condition_desc bao_condition_desc')
            ->select();
        $Page->setConfig('prev',' 上一页 ');
        $Page->setConfig('next','下一页');
        $Page->setConfig('theme', '%UP_PAGE% <span class="current">%NOW_PAGE%</span> %DOWN_PAGE%');
        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('count',$count);//将订单的总数量返回到HTML
        $this->assign("_position","已使用红包");
        //查询未使用红包
        $wei['user_phone'] = $phone['mobile'];
        $wei['status'] = 0;
        $wei['expire_time']=array('gt',$time);
        $weishiyong = $bao->where($wei)->count();
        $this->assign('wei',$weishiyong);

        //查询已过期红包
        $guo['user_phone'] = $phone['mobile'];
        $guo['status'] = 0;
        $guo['expire_time']=array('lt',$time);
        $guuoqi = $bao->where($guo)->count();
        $this->assign('guoqi',$guuoqi);

        $this->assign('hongbao',$list);
        $this->assign('time',$time);
        $this->display();
    }

    public function guoqi(){
        $uid = UID;
        $time=time();
        $bao = M('user_bao');//实例化order对象
        $phone = M('ucenter_member')->where('id='.$uid)->field('mobile')->find();
        $where['user_phone']=$phone['mobile'];
        //查询已过期红包
        $guo['user_phone'] = $phone['mobile'];
        $guo['status'] = 0;
        $guo['expire_time']=array('lt',$time);
        $guuoqi = $bao->where($guo)->count();
        $this->assign('guoqi',$guuoqi);

        $count = $bao->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($guuoqi,9);// 实例化分页类 传入总记录数和每页显示的记录数
        $guosy['a.user_phone'] = $phone['mobile'];
        $guosy['a.status'] = 0;
        $guosy['a.expire_time']=array('lt',$time);
        $list = $bao
            ->alias('a')
            ->join('LEFT JOIN wm_bao b ON a.bao_id=b.bao_id')
            ->where($guosy)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('a.expire_time desc')
            ->field('a.user_bao_price bao_price,a.expire_time end_time,b.send_time send_time,a.condition_desc bao_condition_desc')
            ->select();
        $Page->setConfig('prev',' 上一页 ');
        $Page->setConfig('next','下一页');
        $Page->setConfig('theme', '%UP_PAGE% <span class="current">%NOW_PAGE%</span> %DOWN_PAGE%');
        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('count',$count);//将订单的总数量返回到HTML
        $this->assign("_position","过期红包");
        //查询未使用红包
        $wei['user_phone'] = $phone['mobile'];
        $wei['status'] = 0;
        $wei['expire_time']=array('gt',$time);
        $weishiyong = $bao->where($wei)->count();
        $this->assign('wei',$weishiyong);

        //查询已使用红包
        $yi['user_phone'] = $phone['mobile'];
        $yi['status'] = 1;
        $yishiyong = $bao->where($yi)->count();
        $this->assign('yi',$yishiyong);



        $this->assign('hongbao',$list);
        $this->assign('time',$time);
        $this->display();
    }
}

