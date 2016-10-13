<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/20
 * Time: 9:58
 */

namespace Printer\Controller;

use Admin\Controller\AdminController;

class IndexController extends AdminController
{
    public function index(){

        $map['user_id']=session('user_auth.uid');
        $list=M('Printer')->where($map)->select();
        $this->assign('list',$list);
        $this->display();
    }

    public function createPrint($prints,$def){
        $p=rtrim($prints,':');
        $p=explode(':',$p);
        $pr=M('Printer');
        $flag=false;
        $uid=session('user_auth.uid');
        foreach($p as $val){
            $child=explode('|',$val);
            $where['printer_name']=$child[1];
            $where['user_id']=$uid;
            $temp=array('printer_sn'=>$child[0],'printer_name'=>$child[1],'user_id'=>$uid);
            $ist=$pr->where($where)->find();
            if(!$ist){
                $pr->add($temp) && $flag=true;
            }
        }

        $map['printer_name']=$def;
        $map['user_id']=$uid;
        $pr->where($map)->setField('status',1);
        $flag && $this->ajaxReturn('success');
    }

    public function printerSet($ids,$printerto_local=0,$printer_local_num=0,$printerto_gk=0,$printerto_psy=0,$printerto_hc=0)
    {
        $data['printerto_local']=$printerto_local?:0;
        $data['printer_local_num']=$printer_local_num?:0;
        $data['printerto_gk']=$printerto_gk?:0;
        $data['printerto_psy']=$printerto_psy?:0;
        $data['printerto_hc']=$printerto_hc?:0;
        $result=M('Printer')->where('id='.$ids)->save($data);
        if($result!==false){
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('fail');
        }
    }

    public function setPrintDef($v){
        $map['id']=$v;
        $map1['user_id']=session('user_auth.uid');
        $map1['id']=array('neq',$v);
        $res=M('Printer')->where($map)->setField('status',1);
        $res1=M('Printer')->where($map1)->setField('status',0);
        if($res || $res1){
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('fail');
        }
    }

}