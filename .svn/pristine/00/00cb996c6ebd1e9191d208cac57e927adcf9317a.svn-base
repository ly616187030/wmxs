<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Util\Think\Page;
/**
 * 系统配置控制器
 * @author jry <598821125@qq.com>
 */
class UserconfigController extends AdminController {
    /**
     * 配置列表
     * @param $tab 配置分组ID
     * @author jry <598821125@qq.com>
     */
    public function index() {

        $map['group']= 1;

        //获取当前登录用户的创建者下的所有配置
        $uid=is_login();
        $user=M('admin_user');
        $adminuser=$user->where('id='.$uid)->field('founder_id')->find();

        //获取配置中founder_id等于注册公司ID 的所有配置
        $map['uc.founder_id']=$adminuser['founder_id'];

        // 获取所有配置
        $config_object = M('user_config');
        $count = $config_object->alias('uc')
            ->where($map)
            ->order('sort asc,id asc')
            ->select();

        $count = count($count);
        $Page = new \Think\Page($count, 15);
        $Page->setConfig('header', '<span class="rows">共 %TOTAL_ROW% 条记录</span>');
        $Page->setConfig('first', '首页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '末页');
        $Page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $Page->lastSuffix = false;
        $show = $Page->show();


        $data_list = $config_object->alias('uc')
            ->where($map)
            ->order('sort asc,id asc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();

        $this->assign('info',$data_list);
        $this->assign('page',$show);
        $this->display();
    }

    //分组显示列表
    public function groupone() {

        $group=I('group');
        $map['group']=$group;

        //获取当前登录用户的创建者下的所有配置
        $uid=is_login();
        $user=M('admin_user');
        $adminuser=$user->where('id='.$uid)->field('founder_id')->find();

        //获取配置中founder_id等于注册公司ID 的所有配置
        $map['uc.founder_id']=$adminuser['founder_id'];

        // 获取所有配置
        $config_object = M('user_config');
        $count = $config_object->alias('uc')
            ->where($map)
            ->order('sort asc,id asc')
            ->select();

        $count = count($count);
        $Page = new \Think\Page($count, 15);
        $Page->setConfig('header', '<span class="rows">共 %TOTAL_ROW% 条记录</span>');
        $Page->setConfig('first', '首页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '末页');
        $Page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $Page->lastSuffix = false;
        $show = $Page->show();

        // 获取所有配置
        $config_object = M('user_config');
        $data_list = $config_object->alias('uc')
            ->where($map)
            ->order('sort asc,id asc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();

        $this->assign('info',$data_list);
        $this->assign('page',$show);

        if($group==1){
            $this->display('groupone');
        }elseif($group==2){
            $this->display('grouptwo');
        }elseif($group==3){
            $this->display('groupthree');
        }elseif($group==4){
            $this->display('groupfour');
        }elseif($group==5){
            $this->display('groupfive');
        }elseif($group==6){
            $this->display('groupsix');
        }
    }

    /**
     * 新增配置/编辑配置
     * @author jry <598821125@qq.com>
     */

    public function add(){

        $id=I('id');
        $user_config = D('user_config');

        if(IS_POST) {
            $data=I('post');
            if ($user_config->create($data)) {
                $founder_info=M('admin_user')->where('id='.is_login())->field('founder_id')->find();
                $user_config->founder_id=$founder_info['founder_id'];
                if ($id) {
                    if ($user_config->save()!==false) {
                        $this->success('更新成功', U('index'));
                    } else {
                        $this->error('更新失败');
                    }

                } else {
                    if ($user_config->add()) {
                        $this->success('添加成功', U('index'));
                    } else {
                        $this->error('添加失败');
                    }
                }
            } else {
                $this->error($user_config->getError());
            }
        }

        if($id){
            $info = $user_config->where('id = '.$id)->find();
            $this->assign('info',$info);
            $this->assign('id',$id);
        }

        //获取配置数据
        $form_item_type = C('FORM_ITEM_TYPE');
            foreach ($form_item_type as $key => $val) {
                $a[]=$key;
                $b[]=$val[0];
            }
        $item_type=array_combine($a,$b);

        $this->assign('CONFIG_GROUP_LIST',C('CONFIG_GROUP_LIST'));
        $this->assign('item_type',$item_type);
        $this->display();

    }

    //删除列表中数据记录
    public function del(){
        $m=I('get.m');
        $id = I('id');
//        $this->ajaxReturn($id);
        $n=M($m);
        //判断id是否是数组类型
        if (is_array($id)) {
            //执行批量删除操作
            $where = 'id in (' . implode(',', $id) . ')';
        } else {
            //执行单挑删除操作
            $where = 'id =' . $id;
        }

        $list = $n->where($where)->delete();
        if ($list > 0) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }


}
