<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/10/22
 * Time: 20:40
 */
namespace Caipin\Controller;
use Admin\Controller\AdminController;
use Common\Util\Think\Page;

class CaipinController extends AdminController
{

    public function index(){

        //从Caipin表查询数据记录
        $Caipin = M('Caipin');
        $cai = $Caipin->select();
        foreach($cai as $k=>$v){
            $v['s_time']=str_replace(",",":",$v['s_time']);
            $v['e_time']=str_replace(",",":",$v['e_time']);
            $cai[$k]['s_time1']=substr($v['s_time'],0,4);
            $cai[$k]['s_time2']=substr($v['s_time'],-5);
            $cai[$k]['e_time1']=substr($v['e_time'],0,4);
            $cai[$k]['e_time2']=substr($v['e_time'],-5);
        }

        //分页数据设置
        $count = count($cai);
        $Page       = new \Think\Page($count,15);
        $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
        $Page->setConfig('first','首页');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('last','末页');
        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $Page->lastSuffix = false;
        $show       = $Page->show();

        $this->assign('cai',$cai);
        $this->assign('page',$show);
        $this->display();
    }


    public function add(){

        $id=I('cp_id');
        $Caipin = D('Caipin');

        if(IS_POST) {
            $stime=I('post.s_time');
            $etime=I('post.e_time');
            $stime[0]==''?$zao='':$zao=implode(',',$stime);
            $etime[0]==''?$wan='':$wan=implode(',',$etime);
            if ($Caipin->create()) {
                $Caipin->s_time = $zao;
                $Caipin->e_time = $wan;
                $Caipin->ctime = time();
                if ($id) {
                    if ($Caipin->save()!==false) {
                        $this->success('更新成功', U('index'));
                    } else {
                        $this->error('更新失败');
                    }

                } else {
                    $Caipin->s_time = $zao;
                    $Caipin->e_time = $wan;
                    if ($Caipin->add()) {
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
            $tstime=explode(',',$info['s_time']);
            $info['zao1']=$tstime[0];
            $info['zao2']=$tstime[1];
            $info['zao3']=$tstime[2];
            $info['zao4']=$tstime[3];

            $tetime=explode(',',$info['e_time']);
            $info['wan1']=$tetime[0];
            $info['wan2']=$tetime[1];
            $info['wan3']=$tetime[2];
            $info['wan4']=$tetime[3];

            $this->assign('info',$info);
            $this->assign('id',$id);
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
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

}
?>       