<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/10/22
 * Time: 20:40
 */
namespace Business\Controller;
use Admin\Controller\AdminController;
use Common\Util\Think\Page;

class CaipinController extends AdminController
{

    public function index(){

        //从Caipin表查询数据记录
        $Caipin = M('Caipin');
        $fids['founder_id'] =FID;

        $uid=is_login();
        $founder_info=M('admin_user')->where('id='.$uid)->field('founder_id,user_type')->find();

        //如果是企业账号登陆
        if($founder_info['user_type']=='company'){
            $count = $Caipin->where($fids)->count();
        }

        //如果是企业下创建的商家
        if($founder_info['user_type']=='company_member'){
            $count = $Caipin->where('uid='.$uid)->count();
        }

        //分页数据设置
        $Page       = new \Think\Page($count,15);
        $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
        $Page->setConfig('first','首页');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('last','末页');
        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $Page->lastSuffix = false;
        $show       = $Page->show();


        //如果是企业账号登陆
        if($founder_info['user_type']=='company'){
            $cai = $Caipin->where($fids)->select();
        }

        //如果是企业下创建的商家
        if($founder_info['user_type']=='company_member'){
            $cai = $Caipin->where('uid='.$uid)->select();
        }


        foreach($cai as $k=>$v){
            $v['s_time']=str_replace(",",":",$v['s_time']);
            $v['e_time']=str_replace(",",":",$v['e_time']);
            $cai[$k]['s_time1']=substr($v['s_time'],0,4);
            $cai[$k]['s_time2']=substr($v['s_time'],-5);
            $cai[$k]['e_time1']=substr($v['e_time'],0,4);
            $cai[$k]['e_time2']=substr($v['e_time'],-5);
        }

        $this->assign('cai',$cai);
        $this->assign('page',$show);
        $this->display();
    }


    public function add(){

        $id=I('cp_id');
        $Caipin = D('Caipin');

        if(IS_POST) {

            $timetype = I('saletime_type');
            if($timetype == 1) {
                unset($_POST['sale_time']);
                unset($_POST['week']);
            }
            if($timetype == 2){
                $salet = I('sale_time');
                $ts = '';
                foreach($salet as $val){
                    $s = '';
                    foreach($val as $v){
                        $s.= $v==''?'00':(strlen($v)==1?'0'.$v:$v);
                    }
                    $ts.=$s.'|';
                }
            }
            $week = I('week');
            if(!empty($week)){
                $w = implode(',',$week);
            }

            $rules=array(
                array('cp_name','require','请输入菜品名称',1,'regex',3),
                array('now_price','require','请输入菜品价格',1,'regex',3),
                array('sort_order','number','排序必须是数字',2,'regex',3),
            );
            if ($Caipin->validate($rules)->create()) {
                $timetype == 2 && $Caipin->sale_time = rtrim($ts,'|');
                !empty($week) && $Caipin->week = $w;
                $Caipin->ctime = time();
                $Caipin->flag = 1;

                $uid=is_login();
                $founder_info=M('admin_user')->where('id='.$uid)->field('founder_id')->find();
                $Caipin->founder_id=$founder_info['founder_id'];
                $Caipin->uid = $uid;

                if ($id) {
                    if ($Caipin->save()!==false) {
                        action_log('edit_action','canpin',$id,is_login());
                        $this->success('更新成功', U('index'));
                    } else {
                        $this->error('更新失败');
                    }
                } else {
                    $add_id = $Caipin->add();
                    if ($add_id > 0) {
                        action_log('add_action','canpin',$add_id,is_login());
                        $this->success('添加成功', U('index'));
                    } else {
                        $this->error('添加失败');
                    }
                }
            } else {
                $this->error($Caipin->getError());
            }
        }

        if($id){
            $info = $Caipin->where('cp_id = '.$id)->find();
            $this->assign('info',$info);
            $this->assign('id',$id);
//            dump($info['sale_time']);
        }
        $this->display();
    }


    /**

     * 汉字转拼音

     */

    public function cu2py()

    {
        $str = I('get.keys');
        if (!empty($str)) {
            $str = \Vendor\CUtf8_PY::encode($str);
            $this->ajaxReturn(strtoupper($str));
        }
    }

    public function getPicture(){

        $id=I('post.id');
        $Caipin = M('Caipin');
        $cai = $Caipin->where('cp_id='.$id)->field('cp_big_img')->find();
        $img=get_new_cover($cai['cp_big_img']);
        $this->ajaxReturn($img);
    }

    //删除列表中数据记录
    public function del(){
        $m=I('get.m');
        $id = I('cp_id');
//        $this->ajaxReturn($id);
        $n=M($m);
        //判断id是否是数组类型
        if (is_array($id)) {
            //执行批量删除操作
            $where = 'cp_id in (' . implode(',', $id) . ')';
        } else {
            //执行单挑删除操作
            $where = 'cp_id =' . $id;
        }

        $list = $n->where($where)->delete();
        if ($list > 0) {
            action_log('delete_action',$m,is_array($id)?implode(',', $id):$id,is_login());
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

}
?>       