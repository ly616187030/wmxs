<?php

namespace Administration\Controller;
use Admin\Controller\AdminController;

class ShareController extends AdminController {
    /**
     * 后台关于微信分享的设置
     * @author CHANGKAI <job_ck@126.com>
     */
    public function index(){
        $award = M('award'); // 实例化User对象
        $count      = $award->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
        $Page->setConfig('first','首页');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('last','末页');
        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $Page->lastSuffix = false;//分页最后的总页数不显示
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $award
            ->order('award_id desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        $re = M('ucenter_member')->where('status=1')->find();
        $this->assign('re',$re['award_id']);// 赋值数据集
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->meta_title = '分享设置管理';
        $this->display();
    }

    public function add(){
        $this->meta_title = '添加奖励';
        if(IS_POST){
            //动态验证
            $rules = array(
                array('award_name','require','请选择奖励类型'),
                array('award_price','require','请填写奖励额度'),
            );
            $award = M("award");
            $award_time = I('award_time');
            if ($award->validate($rules)->create()){
                $award->award_time = strtotime($award_time);
                $add = $award->add();
                if($add){
                    $this->success('奖励设置添加成功', U('index'));
                }
                else{
                    $this->error('奖励设置添加失败');
                }
            }else{
                $this->error($award->getError());
            }
        }else{
            $this->display();
        }
    }

    public function del(){
        $id = array_unique((array)I('award_id',0));
        $in = array('award_id'=> array('in',$id));
        if(M('award')->where($in)->delete()){
            $this->success('删除成功',U('index'));
        }
        else{
            $this->error('删除失败');
        }
    }


    public function edit(){
        if(IS_POST) {
            $rules = array(
                array('award_name','require','请选择奖励类型'),
                array('award_price','require','请填写奖励额度'),
            );
            $award = M("award");
            $award_time = I('award_time');
            $time = strtotime($award_time);
            $where['award_id']=I('post.award_id');
            if ($award->validate($rules)->create()){
                if($time>0){
                    $award->award_time = $time;
                }else{
                    $award->award_time = 0;
                }

                $awardedit = $award->where($where)->save();
                if ($awardedit) {
                    $this->success('更新成功', U('index'));
                } else {
                    $this->error('您没有修改任何内容请返回');
                }
            }
            else{

                $this->error($award->getError());
            }

        }else{
            $id = I('get.award_id');
            $award = M('award')
                ->where("award_id =".I('get.award_id'))
                ->find(); /* 获取数据 */
            if(false === $award){
                $this->error('获取入库信息错误');
            }
            $this->meta_title = '编辑奖励';
            $this->assign('award', $award);
            $this->display();
        }
    }
    public function defaults(){
        $id = I('get.award_id');
        $award = M('ucenter_member')
            ->where('status=1')
            ->setField('award_id',$id);
        if($award){
            $this->success('设为默认成功', U('index'));
        }else{
            $this->error('该记录为默认设置，请选择其他');
        }
    }
   public function detail(){
       if(IS_POST) {
            $time = I('post.time');
           if($time!=''){
               $timelist = explode('-',$time);
               $timemin = mktime(0, 0, 0, $timelist[1], $timelist[2], $timelist[0]);
               $timemax = $timemin+86399;
               $map['award_time']  = array(array('gt',$timemin),array('lt',$timemax),'and');
               $share = M('share_detail'); // 实例化User对象
               $count      = $share->where($map)->count();// 查询满足要求的总记录数
               $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
               $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
               $Page->setConfig('first','首页');
               $Page->setConfig('prev','上一页');
               $Page->setConfig('next','下一页');
               $Page->setConfig('last','末页');
               $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
               $Page->lastSuffix = false;//分页最后的总页数不显示
               $show       = $Page->show();// 分页显示输出
               // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
               $list = $share
                   ->order('share_detail_id desc')
                   ->where($map)
                   ->limit($Page->firstRow.','.$Page->listRows)
                   ->select();
               $this->assign(time,$time);
               $this->assign(page,$show);
               $this->assign('list',$list);
               $this->display();
           }else{
               $share = M('share_detail'); // 实例化User对象
               $count      = $share->count();// 查询满足要求的总记录数
               $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
               $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
               $Page->setConfig('first','首页');
               $Page->setConfig('prev','上一页');
               $Page->setConfig('next','下一页');
               $Page->setConfig('last','末页');
               $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
               $Page->lastSuffix = false;//分页最后的总页数不显示
               $show       = $Page->show();// 分页显示输出
               // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
               $list = $share
                   ->order('share_detail_id desc')
                   ->limit($Page->firstRow.','.$Page->listRows)
                   ->select();
               $this->assign(page,$show);
               $this->assign('list',$list);
               $this->display();
           }
       }else{
           $share = M('share_detail'); // 实例化User对象
           $map['tj_openid']=array('neq','a');
           $map['grant_status']=0;
           $map['award']=2;
           $lists = $share
               ->where($map)
               ->order('share_detail_id DESC')
               ->group('tj_id,tj_openid')
               ->limit($Page->firstRow.','.$Page->listRows)
               ->select();
           $count      = count($lists);// 查询满足要求的总记录数
           $Page       = new \Think\Page($count,100);// 实例化分页类 传入总记录数和每页显示的记录数(25)
           $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
           $Page->setConfig('first','首页');
           $Page->setConfig('prev','上一页');
           $Page->setConfig('next','下一页');
           $Page->setConfig('last','末页');
           $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
           $Page->lastSuffix = false;//分页最后的总页数不显示
           $show       = $Page->show();// 分页显示输出
           // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
           $list = $share
               ->where($map)
               ->order('share_detail_id DESC')
               ->group('tj_id,tj_openid')
               ->limit($Page->firstRow.','.$Page->listRows)
               ->select();
            foreach($list as $k=>$v){
                $map['tj_id']=$v['tj_id'];
                $re = $share->where($map)->count('tj_id');
                array_push($list[$k],$re);
            }
           $this->assign(page,$show);
           $this->assign('list',$list);
           $this->display();
       }
   }
    //设置现金红包兑现状态
    public function setconfig(){
        $tj_id = I('get.tuijian_id');
        $openid = I('get.openid');
        $share_detail_id = I('get.share_detail_id');
        $grant_status = I('get.grant_status');
        if(!empty($tj_id)){
                $where['openid'] = $openid;
                $where['tj_id'] = $tj_id;
                $re = M('share_detail')->where($where)->setField('grant_status', 1);
                if ($re) {
                    $this->success('兑现成功！');
                } else {
                    $this->error('兑现失败！');
                }
        }else{
            if ($grant_status == 0) {
                $where['share_detail_id'] = $share_detail_id;
                $re = M('share_detail')->where($where)->setField('grant_status', 1);
                if ($re) {
                    $this->success('兑现成功！');
                } else {
                    $this->error('兑现失败！');
                }
            }else{
                $where['share_detail_id'] = $share_detail_id;
                $re = M('share_detail')->where($where)->setField('grant_status', 0);
                if ($re) {
                    $this->success('设置未兑现成功！');
                } else {
                    $this->error('设置未兑现失败！');
                }
            }
        }
    }

    public function getSee(){
        $res=M('UcenterMember')->where('mobile=18745836814')->find();
        dump($res);
    }

    public function getSaa(){
        $res=M('ShareDetail')->where('tj_id=24317 AND award=2')->count();
        dump($res);
    }
}