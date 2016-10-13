<?php

namespace Administration\Controller;

use Admin\Controller\AdminController;
/**
 * 后台库存报表控制器
 * @author CHANGKAI <job_ck@126.com>
 */
class BaoController extends AdminController {
    /**
     * 后台平台库存报表index
     * @author CHANGKAI <job_ck@126.com>
     */
    public function index(){
        $Bao = M('bao'); // 实例化User对象
        $count      = $Bao->count();// 查询满足要求的总记录数
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
        $list = M('bao')
            ->order('bao_id desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->meta_title = '红包管理';
        $this->display();
    }

    public function add(){
        $this->meta_title = '发放红包';
        if(IS_POST){
            //动态验证
            $rules = array(
                array('bao_price','require','请填写红包金额'),
                array('bao_price','/^[1-9]\d*$/','红包金额须大于0!',1),
                array('end_time','require','请选择红包到期时间'),
                array('bao_lowest','require','请填写抢红包最低限额'),
                array('bao_heighest','require','请填写抢红包最高限额'),
                array('bao_condition','require','请填写红包使用条件金额'),
                array('bao_condition','/^[0-9]\d*$/','红包使用条件金额须大于或等于0!',1),
                array('bao_condition_desc','require','请填写红包使用条件说明'),
            );
            $Bao = M("bao");
            $data['send_time']=time();
            $data['bao_status']=0;
            $data['bao_price']=I('post.bao_price');
            $data['end_time']=strtotime(trim(I('post.end_time'),'-'));
            $data['bao_lowest']=I('post.bao_lowest');
            $data['bao_heighest']=I('post.bao_heighest');
            $data['bao_condition']=I('post.bao_condition');
            $data['bao_condition_desc']=I('post.bao_condition_desc');
            $data['sort_order']=I('post.sort_order');
            if ($Bao->validate($rules)->create()){
                $add = $Bao->add($data);
                if($add){
                        $this->success('红包发放成功', U('index'));
                }
                else{
                    $this->error('红包发放失败');
                }
            }else{
                $this->error($Bao->getError());
            }

        }else{

            $this->display();

        }
    }

    public function del(){
        $id = array_unique((array)I('bao_id',0));
        $in = array('bao_id'=> array('in',$id));
            if(M('bao')->where($in)->delete()){
                $this->success('删除成功',U('index'));
            }
        else{
            $this->error('删除失败');
        }
    }


    public function edit(){
        if(IS_POST) {
            $rules1 = array(
                array('bao_price', 'require', '请填写红包金额'),
                array('bao_price', '/^[1-9]\d*$/', '红包金额须大于0!', 1),
                array('end_time', 'require', '请选择红包到期时间'),
                array('bao_lowest','require','请填写抢红包最低限额'),
                array('bao_heighest','require','请填写抢红包最高限额'),
                array('bao_condition', 'require', '请填写红包使用条件金额'),
                array('bao_condition_desc', 'require', '请填写红包使用条件说明'),
            );
            $Bao = M("bao");
            $data['bao_id'] =I('post.bao_id');
            $data['bao_price'] = I('post.bao_price');
            $data['end_time'] = strtotime(trim(I('post.end_time'), '-'));
            $data['bao_lowest']=I('post.bao_lowest');
            $data['bao_heighest']=I('post.bao_heighest');
            $data['bao_condition'] = I('post.bao_condition');
            $data['bao_condition_desc'] = I('post.bao_condition_desc');
            $data['sort_order']=I('post.sort_order');
            $where['bao_id']=$data['bao_id'];
            if ($Bao->validate($rules1)->create()){
                $Baoedit = $Bao->where($where)->save($data);
                if ($Baoedit) {
                    $this->success('更新成功', U('index'));
                } else {
                    $this->error('您没有修改任何内容请返回');
                }
            }
        else{

                $this->error($Bao->getError());
            }

        }else{
            $id = I('get.bao_id');
            $bao = M('bao')
                ->where("bao_id =".I('get.bao_id'))
                ->find(); /* 获取数据 */
            if(false === $bao){
                $this->error('获取入库信息错误');
            }
            $this->meta_title = '编辑红包';
            $this->assign('bao', $bao);
            $this->display();
        }
    }
    public function hongbao(){
        $Bao = M('user_bao'); // 实例化User对象
        $count      = $Bao->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
        $Page->setConfig('first','首页');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('last','末页');
        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $Page->lastSuffix = false;//分页最后的总页数不显示
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = M('user_bao')
            ->alias('u')
            ->join('LEFT JOIN wm_bao b ON u.bao_id=b.bao_id')
            ->order('u.user_bao_id desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$show);// 赋值分页输出
        $this->meta_title = '用户红包';
        $this->display();
    }

    /*
  * 导出excel文件
  */
    public function excel(){

        $Model=new \Think\Model();
        $str = M('user_bao')
            ->alias('u')
            ->join('LEFT JOIN wm_bao b ON u.bao_id=b.bao_id')
            ->order('u.user_bao_id asc')
            ->select();

        foreach($str as $key=>$val){
            switch ($val['status']){
                case 0:$str[$key]['status']="未使用";break;
                case 1:$str[$key]['status']="已使用";break;
                case 2:$str[$key]['status']="已过期";break;
            }
        }
        header("Content-type:application/vnd.ms-excel;charset=UTF-8");
        header("Content-disposition:filename='用户红包数据".date('Ymd',time()).".xls");

        echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>
             <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
             <html>
                 <head>
                    <meta http-equiv='Content-type' content='text/html;charset=UTF-8' />
                 </head>
                 <body>
                     <div align=center x:publishsource='Excel'>
                         <table x:str border=1 cellpadding=0 cellspacing=0 width=100% style='border-collapse: collapse'>
                            <tr>
                            <th>红包编号</th>
                            <th>红包金额(元)</th>
                            <th>用户名</th>
                            <th>联系方式</th>
                            <th>使用条件</th>
                            <th>使用条件明细</th>
                            <th>发放日期</th>
                            <th>到期日期</th>
                            <th>使用状态</th>
                            </tr>";
        foreach($str as $v){
            echo "<tr>
                            <td>".$v['user_bao_id']."</td>
                            <td>".$v['user_bao_price']."</td>
                            <td>".get_user_name($v['user_phone'])."</td>
                            <td>".$v['user_phone']."</td>
                            <td>".$v['user_condition']."</td>
                            <td>".$v['condition_desc']."</td>
                            <td>".date('Y-m-d',$v["send_time"])."</td>
                            <td>".date('Y-m-d',$v["expire_time"])."</td>
                            <td>".$v['status']."</td></tr>";
        }

        echo "</table>
                     </div>
                 </body>
             </html>";
    }
}