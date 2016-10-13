<?php

namespace User\Controller\Admin;
use Admin\Controller\AdminController;
use Common\Util\Tree;
class MessageController extends AdminController{
    public function yewumsg(){
        if(IS_POST){
            $message = M('message');
            $where['to_uid'] = UID;
            $where['type'] = 0;
            $mm = $message->where($where)->setField('is_read',1);
            if($mm){
                $this->success('已全部标记为已读');
            }else{
                $this->error('没有未读消息！');
            }
        }else{
            $message = M('message');
            $where['to_uid'] = UID;
            $where['type'] = 0;
            $count=$message->where($where)->count();
            $Page = new \Think\Page($count, 10);
            // 实例化分页类 传入总记录数和每页显示的记录数
            $Page->setConfig('header','条');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('theme', '共计：%TOTAL_ROW% %HEADER%&emsp;第%NOW_PAGE%页&emsp;共%TOTAL_PAGE%页&emsp;%UP_PAGE% %DOWN_PAGE% ');
            $show = $Page -> show();
            // 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $message->where($where)->order('create_time desc')-> limit($Page -> firstRow . ',' . $Page -> listRows) ->select();
            $this -> assign('list', $list);
            // 赋值数据集
            $this -> assign('page', $show);
            // 赋值分页输出
            $this -> display();
            // 输出模板
        }
    }
    public function yewumsgedit(){
            $message = M('message');
            $where['from_uid'] = UID;
            $where['type'] = 0;
            $where['status'] = 1;
            $list1 = $message->where($where)->group('create_time,title,content,type')->select();
            $count=count($list1);
            $Page = new \Think\Page($count, 10);
            // 实例化分页类 传入总记录数和每页显示的记录数
            $Page->setConfig('header','条');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('theme', '共计：%TOTAL_ROW% %HEADER%&emsp;第%NOW_PAGE%页&emsp;共%TOTAL_PAGE%页&emsp;%UP_PAGE% %DOWN_PAGE% ');
            $show = $Page -> show();
            // 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $message->where($where)->order('create_time desc')->group('create_time,title,content,type')->limit($Page -> firstRow . ',' . $Page -> listRows) ->select();
            $this -> assign('list', $list);
            // 赋值数据集
            $this -> assign('page', $show);
            // 赋值分页输出
            $this -> display();
            // 输出模板
    }
    public function yewumsgadd(){
        if(IS_POST){
            $where['founder_id']=FID;
            $person =  M('admin_user')->where($where)->select();
            $this->ajaxReturn($person);
        }
        $this->display();
    }
    public function yewumsgdetail(){
        if(IS_GET){
            $id = I('get.id');
            $where['id']=$id;
            $msg =  M('message')->where($where)->find();
            if($msg){
              M('message')->where($where)->setField('is_read',1);
            }
            $this->assign('msg',$msg);
        }
        $this->display();
    }
    public function yewumsgeditdetail(){
        if(IS_GET){
            $id = I('get.id');
            $where['id']=$id;
            $msg =  M('message')->where($where)->find();
            $this->assign('msg',$msg);
        }
        $this->display();
    }
    public function add(){
        if(IS_POST){
            $configname = I('post.configname');
            $configtext = I('post.configtext');
            $configrenyuan = I('post.configrenyuan');
            
            if($configrenyuan==1){
                 $where['founder_id']=FID;
                 $uid =  M('admin_user')->where($where)->getField('id',true);
            }else{
                $uid = I('post.uid');
            }
            $message = M("message"); // 实例化User对象
                if ($configname==''){
                    $this->error('请填写信息标题！');
                 }else if ($configtext==''){
                    $this->error('请填写信息内容！');
                 }else{
                    if(count($uid)==0){
                        $this->error('请至少选择一个发送对象！');
                    }else if(count($uid)>0 &&count($uid)<2){
                        $data['title']=$configname;
                        $data['content']=$configtext;
                        $data['to_uid']=$uid;
                        $data['from_uid']=UID;
                        $data['create_time']=time();
                        $re = $message->add($data);
                        action_log('add_action','message',$re,UID);//记录行为
                        $this->success('业务消息发送成功！',U('yewumsg'));
                    }else{
                        $arr = array();
                        for ($i=0; $i <count($uid) ; $i++) { 
                            $data['title']=$configname;
                            $data['content']=$configtext;
                            $data['to_uid']=$uid[$i];
                            $data['from_uid']=UID;
                            $data['create_time']=time();
                            $res = $message->add($data);
                            array_push($arr,$res);
                        }
                        $res1 = implode(',',$arr);
                        action_log('add_action','message',$res1,UID);//记录行为
                        $this->success('业务消息发送成功！',U('yewumsg'));
                    }
                }
        }
    }

    public function yewumsgdel(){
        if(IS_GET){
            $title = I('get.title');
            $type = I('get.type');
            $create_time = I('get.create_time');
            $where['title']=$title;
            $where['type']=$type;
            $where['create_time']=$create_time;
            $msgid = M('message')->where($where)->getField('id',true);
            $msg =  M('message')->where($where)->delete();
            $re = implode(',',$msgid);
            if($msg&&$msgid){
              action_log('delete_action','message',$re,UID);//记录行为
              $this->success('删除信息成功！');
            }else{
              $this->error('删除信息失败！');
            }
        }
    }


    public function sysmsg(){
            $message = M('message');
            $where['type'] = 1;
            $count=$message->where($where)->count();
            $Page = new \Think\Page($count, 10);
            // 实例化分页类 传入总记录数和每页显示的记录数
            $Page->setConfig('header','条');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('theme', '共计：%TOTAL_ROW% %HEADER%&emsp;第%NOW_PAGE%页&emsp;共%TOTAL_PAGE%页&emsp;%UP_PAGE% %DOWN_PAGE% ');
            $show = $Page -> show();
            // 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $message->where($where)->order('create_time desc')-> limit($Page -> firstRow . ',' . $Page -> listRows) ->select();
            $this -> assign('list', $list);
            // 赋值数据集
            $this -> assign('page', $show);
            // 赋值分页输出
            $this -> assign('uid',UID);
            $this -> assign('time',time());
            $this -> display();
            // 输出模板
        }
        public function sysmsgadd(){
          if(IS_POST){
            $configname = I('post.configname');
            $configtext = I('post.configtext');
            $uid = null;
            $message = M("message"); // 实例化User对象
                if ($configname==''){
                    $this->error('请填写公告标题！');
                 }else if ($configtext==''){
                    $this->error('请填写公告内容！');
                 }else{
                            $data['title']=$configname;
                            $data['content']=$configtext;
                            $data['type']=1;
                            $data['to_uid']=$uid;
                            $data['from_uid']=UID;
                            $data['create_time']=time();
                            $re = $message->add($data);
                        action_log('add_action','message',$re,UID);//记录行为
                        $this->success('系统公告发送成功！',U('sysmsg'));
                    }
        }else{
           $this->display();
        }
    }
    public function sysmsgdetail(){
        if(IS_GET){
            $id = I('get.id');
            $where['id']=$id;
            $msg =  M('message')->where($where)->find();
            if($msg){
              M('message')->where($where)->setField('is_read',1);
            }
            $this->assign('msg',$msg);
        }
        $this->display();
    }
    public function sysmsgeditdetail(){
        if(IS_GET){
            $id = I('get.id');
            $where['id']=$id;
            $msg =  M('message')->where($where)->find();
            $this->assign('msg',$msg);
        }
        $this->display();
    }
     public function sysmsgedit(){
            $message = M('message');
            $where['from_uid'] = UID;
            $where['type'] = 1;
            $where['status'] = 1;
            $list1 = $message->where($where)->select();
            $count=count($list1);
            $Page = new \Think\Page($count, 10);
            // 实例化分页类 传入总记录数和每页显示的记录数
            $Page->setConfig('header','条');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('theme', '共计：%TOTAL_ROW% %HEADER%&emsp;第%NOW_PAGE%页&emsp;共%TOTAL_PAGE%页&emsp;%UP_PAGE% %DOWN_PAGE% ');
            $show = $Page -> show();
            // 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $message->where($where)->order('create_time desc')->limit($Page -> firstRow . ',' . $Page -> listRows) ->select();
            $this -> assign('list', $list);
            // 赋值数据集
            $this -> assign('page', $show);
            // 赋值分页输出
            $this -> display();
            // 输出模板
    }
    public function sysmsgdel(){
        if(IS_GET){
            $id = I('get.id');
            $where['id']=$id;
            $msg =  M('message')->where($where)->delete();
            if($msg){
                action_log('delete_action','message',$id,UID);//记录行为
              $this->success('删除信息成功！');
            }else{
              $this->error('删除信息失败！');
            }
        }
    }


     public function updatemsg(){
            $message = M('message');
            $where['type'] = 2;
            $count=$message->where($where)->count();
            $Page = new \Think\Page($count, 10);
            // 实例化分页类 传入总记录数和每页显示的记录数
            $Page->setConfig('header','条');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('theme', '共计：%TOTAL_ROW% %HEADER%&emsp;第%NOW_PAGE%页&emsp;共%TOTAL_PAGE%页&emsp;%UP_PAGE% %DOWN_PAGE% ');
            $show = $Page -> show();
            // 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $message->where($where)->order('create_time desc')-> limit($Page -> firstRow . ',' . $Page -> listRows) ->select();
            $this -> assign('list', $list);
            // 赋值数据集
            $this -> assign('page', $show);
            // 赋值分页输出
            $this -> assign('uid',UID);
             $this -> assign('time',time());
            $this -> display();
            // 输出模板
        }
        public function updatemsgadd(){
          if(IS_POST){
            $configname = I('post.configname');
            $configtext = I('post.configtext');
            $uid = null;
            $message = M("message"); // 实例化User对象
                if ($configname==''){
                    $this->error('请填写公告标题！');
                 }else if ($configtext==''){
                    $this->error('请填写公告内容！');
                 }else{
                            $data['title']=$configname;
                            $data['content']=$configtext;
                            $data['type']=2;
                            $data['to_uid']=$uid;
                            $data['from_uid']=UID;
                            $data['create_time']=time();
                           $re = $message->add($data);
                        action_log('add_action','message',$re,UID);//记录行为
                        $this->success('升级公告发送成功！',U('updatemsg'));
                    }
        }else{
           $this->display();
        }
    }
    public function updatemsgdetail(){
        if(IS_GET){
            $id = I('get.id');
            $where['id']=$id;
            $msg =  M('message')->where($where)->find();
            if($msg){
              M('message')->where($where)->setField('is_read',1);
            }
            $this->assign('msg',$msg);
        }
        $this->display();
    }
    public function updatemsgedit(){
            $message = M('message');
            $where['from_uid'] = UID;
            $where['type'] = 2;
            $where['status'] = 1;
            $list1 = $message->where($where)->select();
            $count=count($list1);
            $Page = new \Think\Page($count, 10);
            // 实例化分页类 传入总记录数和每页显示的记录数
            $Page->setConfig('header','条');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('theme', '共计：%TOTAL_ROW% %HEADER%&emsp;第%NOW_PAGE%页&emsp;共%TOTAL_PAGE%页&emsp;%UP_PAGE% %DOWN_PAGE% ');
            $show = $Page -> show();
            // 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $message->where($where)->order('create_time desc')->limit($Page -> firstRow . ',' . $Page -> listRows) ->select();
            $this -> assign('list', $list);
            // 赋值数据集
            $this -> assign('page', $show);
            // 赋值分页输出
            $this -> display();
            // 输出模板
    }
    public function updatemsgeditdetail(){
        if(IS_GET){
            $id = I('get.id');
            $where['id']=$id;
            $msg =  M('message')->where($where)->find();
            $this->assign('msg',$msg);
        }
        $this->display();
    }
    public function updatemsgdel(){
        if(IS_GET){
            $id = I('get.id');
            $where['id']=$id;
            $msg =  M('message')->where($where)->delete();
            if($msg){
              action_log('delete_action','message',$id,UID);//记录行为
              $this->success('删除信息成功！');
            }else{
              $this->error('删除信息失败！');
            }
        }
    }
}