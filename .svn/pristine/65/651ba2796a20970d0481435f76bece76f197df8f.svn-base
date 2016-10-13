<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/22
 * Time: 15:01
 */

namespace Admin\Controller;
use Common\Util\Think\Page;
use Common\Util\Tree;
use Org\Util\String;
class CompanyController extends AdminController
{
    public function index(){
        $ad = M('Admin_user');
        $keyword   = I('keywords', '', 'string');
        $form_agent = I('form_agent');
        $apply = I('apply_type');
        $state=I('state');

        $is = is_login();
        $wh['id']=$is;
        if(!is_administrator()){//如果不为管理员
            if(is_agent()){//如果是代理商
                $map['c.from_agent_id'] = $where_dl['id'] = $is;
            }else{//如果不是代理商查询用户类型
               $type =  M('admin_user')->where($wh)->getField('user_type');
                if($type == 'direct_sales'){//如果用户是直销人员
                    $map['c.sale_id'] = $is;
                }
                else if($type == 'software_install'){//如果用户是软件实施人员
                    $map['c.software_install_id'] = $is;
                }
            }
        }else{
            $where_dl['user_type'] = 'agent';
        }
        $map['c.status'] = 1;

        //根据state=2来判断是不是新注册用户，=2是新用户注册，!=2不是查询全部
        if($state==2){
            $map['c.from_agent_id'] = 0;
            $map['c.apply_type'] = 'front_register';
        }else{
            if(!empty($form_agent)){
                $map['c.from_agent_id'] = $form_agent;
            }
            !empty($apply) && $map['c.apply_type'] = $apply;
            $condition = array('like','%'.$keyword.'%');
            $map['c.c_name|c.username'] = array($condition, $condition, '_multi'=>true);
        }

        $com = M('Company');
        $count = $com->alias('c')->where($map)->count();
        $Page = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
        $Page->setConfig('first','首页');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('last','末页');
        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
        $show = $Page->show();// 分页显示输出
        $list = $com
                ->alias('c')
                ->join('__SOFTWARE_VIP__ AS s ON c.vip_id=s.id')
                ->join('LEFT JOIN __ADMIN_USER__ AS u ON c.sale_id=u.id')
                ->join('LEFT JOIN __ADMIN_USER__ AS d ON c.software_install_id=d.id')
                ->join('LEFT JOIN __ADMIN_USER__ AS e ON c.from_agent_id=e.id')
                ->field('c.*,s.vip_name,s.store_number,s.rent_time AS vip_rtime,u.nickname AS salename,d.nickname AS installname,e.nickname AS from_agent_name')
                ->where($map)
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('reg_time DESC')
                ->select();
        if(isset($where_dl)){
            $list_dl =$ad->where($where_dl)->select();
        }
        $this->assign('list',$list);
        $this->assign('page',$show);
        $this->assign('is',$is);
        $this->assign('list_dl',$list_dl);
        $this->assign('form_agent',$form_agent);
        $this->assign('apply',$apply);
        $this->assign('keyword',$keyword);
        $this->display();
    }

    public function showDetail($id){
        $map['c.id'] = $id;
        $res = M('Company')
            ->alias('c')
            ->join('LEFT JOIN __ADDRESS_PROVINCE__ AS p ON c.province=p.code')
            ->join('LEFT JOIN __ADDRESS_CITY__ AS t ON c.city=t.code')
            ->join('LEFT JOIN __ADDRESS_TOWN__ AS n ON c.county=n.code')
            ->field('c.*,p.name AS pname,t.name AS tname,n.name AS nname')
            ->where($map)
            ->find();
        return $this->ajaxReturn($res);
    }

    public function edit($id){
        if(IS_POST){
            $v = I('vip_id',0,'intval');
            $sale_id = I('sale_id');
            $software_install_id = I('software_install_id');
            $from_agent_id = I('from_agent_id');
            empty($v) && $this->error('vip等级不能为空');
            /*empty($can) && $this->error('可创建商铺数量不能为空');
            empty($month) && $this->error('租用时间不能为空');*/
            $c = M('Company');
            $data['vip_id'] = $v;
            $data['sale_id'] = $sale_id;
            $data['software_install_id'] = $software_install_id;
            !empty($from_agent_id) && $data['from_agent_id'] = $from_agent_id;
            $updatewhere['id']=$id;
            if($c->where($updatewhere)->save($data)!==false){
                $this->success('修改成功',U('index'));
            }else{
                $this->error('修改失败');
            }

        }else{
            $info = M('Company')->field('id,vip_id,can_create_num,rent_time,sale_id,software_install_id,from_agent_id')->find($id);
            if($info['from_agent_id']!=0){
                $wheresal1['from_agent_id'] =$info['from_agent_id'];
                $wheresal1['user_type'] ='direct_sales';
                $whereins1['from_agent_id'] =$info['from_agent_id'];
                $whereins1['user_type'] = 'software_install';
                $sale = M('admin_user')->where($wheresal1)->select();
                $install = M('admin_user')->where($whereins1)->select();
                $whereang1['user_type'] = 'agent';
                $agent = M('admin_user')->where($whereang1)->select();
            }else{
                $saleman['user_type'] ='direct_sales';
                $sale = M('admin_user')->where($saleman)->select();
                $installman['user_type'] ='software_install';
                $install = M('admin_user')->where($installman)->select();
                $whereang1['user_type'] = 'agent';
                $agent = M('admin_user')->where($whereang1)->select();
            }
            $wheresal['id'] =$info['sale_id'];
            $whereins['id'] =$info['software_install_id'];
            $whereang['id'] =$info['from_agent_id'];
            $list['sale'] =M('admin_user')->where($wheresal)->field('id,nickname')->find();
            $list['software_install'] =M('admin_user')->where($whereins)->field('id,nickname')->find();
            $list['from_agent_id'] =M('admin_user')->where($whereang)->field('id,nickname')->find();
            $vip = M('SoftwareVip')->select();
            $this->assign('vip',$vip);
            $this->assign('info',$info);
            $this->assign('listsa',$list['sale']);
            $this->assign('listin',$list['software_install']);
            $this->assign('listag',$list['from_agent_id']);
            $this->assign('sale',$sale);
            $this->assign('install',$install);
            $this->assign('agent',$agent);
            $this->assign('admin',is_login());
            $this->display();
        }
    }


    public function adds(){
        $m = M('company_category');
        $list = $m->select();
         $tree = new Tree();
         $list = $tree->toFormatTree($list);
         foreach($list as $k=>$v){
             $list[$k]['margin_left'] = $v['level'] * 21+50;
         }
        $this->assign('list',$list);
        $this->display();
    }
     

    public function add(){
        $m = M('company_category');
        $data['cy_sort'] = 0;
        $data['cy_name'] = I('cy_name');
//        $data['founder_id'] = FID;
        $data['pid'] = I('pid');
        $id = $m->add($data);
        $where['id']=$id;
        $list = $m->where($where)->find();
         $list['level'] = I('level');

        $this->ajaxReturn($list);

    }

    public function del(){
        $m = M('company_category');
        $id = I('id');
        $list = $m->where('pid = ' .$id)->count();
        if($list > 0){
            $data['status'] = -1;
            $data['text'] = '请先删除该栏目下的子栏目';
        }else{
            $where['id']=$id;
            $list = $m->where($where)->delete();
            if($list > 0){
                $data['status'] = 1;
                $data['text'] = '删除成功';
            }else{
                $data['status'] = 0;
                $data['text'] = '删除失败';
            }
        }
        $this->ajaxReturn($data);
    }

    public function edit1(){
        $m = M('company_category');
        $id = I('id');
        $cy_name = I('cy_name');
        $data['cy_name'] = $cy_name;
        $where['id']=$id;
        $list = $m->where($where)->setField($data);
        if($list > 0){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(0);
        }
    }
    //随机生成用户名
    private  function Code($length = 10) {
        return rand(pow(10,($length-1)), pow(10,$length)-1);
    }

    public function addone(){
        $is = is_login();
       if($_POST){
           $aaa = array(
               array('c_name','require','公司名称不能为空'),
               array('c_name','','公司名称已被占用',0,'unique',1),
               array('tel','require','手机不能为空'),
               array('tel','number','手机必须是数字'),
               array('tel','unique','手机号码名称被占用',0,'unique',1),
               array('banben_type','require','版本类型不能为空'),
               array('province','require','省不能为空'),
               array('city','require','市不能为空'),
               array('county','require','区、县不能为空'),
               array('username','require','联系人不能为空'),
               array('post_code','number','邮编格式不正确',2),
               array('qq','number','QQ必须是数字',2),
           );
           $username111 = I('post.username');
           $company = M('company');
           $store_category = M('store_category');
           $admin_user = M('admin_user');
           $role_user1 = M('role_user');
           $ROLE = M('role');
           $auth_access11 = M('auth_access');
           $company_contract=M('company_contract');
           $company->startTrans();

           if($company->validate($aaa)->create()){
               $wh['id']=$is;
               if(!is_administrator()){//如果不为管理员
                   if(is_agent()){//如果是代理商
                       $company->from_agent_id=$is;
                   }else{//如果不是代理商查询用户类型
                       $type =  M('admin_user')->where($wh)->getField('user_type');
                       if($type == 'direct_sales'){//如果用户是直销人员
                           $company->sale_id=$is;
                       }
                       elseif($type == 'software_install_id'){//如果用户是软件实施人员
                           $company->software_install_id=$is;
                       }
                   }
               }
               $seid = String::keyGen();
               $company->id=$seid;
               $company->password =user_md5(I('password'));
                $company->reg_time =time();
                $company->status = 1;
               $company->c_sn=time();
               $company->apply_type = 'backend_write';
               $HTLE = $company->add();
               $wherefounder['founder_id']='administrator';
                   $store_categoro = $store_category->where($wherefounder)->select();
                   foreach($store_categoro as $l => $m){
                       $TOU[] = array(
                           'store_c_name' => $m['store_c_name'],
                           'sort_order' => $m['sort_order'],
                           'flag' => $m['flag'],
                           'store_pic' => $m['store_pic'],
                           'founder_id' => $seid,
                       );}
               $HTLE1 = $store_category->addAll($TOU);
                   $data['mobile'] = I('tel');
                   $data['password'] = user_md5(I('password'));
                   $data['create_time'] = time();
                   $data['reg_ip'] = ip2long(get_client_ip());
                   $data['founder_id'] = $seid;
                   $data['user_type'] = 'company';
                   $data['nickname'] = $username111;
                   $data['username'] = 'U'.generate_username();
                   $data['status'] = 1;
                   $data['push_msg_id'] = time();
                $wheretel['mobile']=$data['mobile'];
               $existmobile = $admin_user->where($wheretel)->find();
                if($existmobile){
                    $this->error('该手机号码已存在');
                }
               $HTLE2 =  $admin_user->add($data);
                        $ON['role_id'] = 2;
                        $ON['user_id'] = $HTLE2;
               $HTLE3 =  $role_user1->add($ON);
                           $MOT['all'] = 2;
                           $ROLES = $ROLE->where($MOT)->select();
                           foreach($ROLES as $v => $n){
                               $KES[] = array(
                                   'name' => $n['name'],
                                   'pid' => $n['pid'],
                                   'status' => $n['status'],
                                   'remark' => $n['remark'],
                                   'create_time' => time(),
                                   'update_time' => time(),
                                   'listorder' => $n['listorder'],
                                   'founder_id' => $seid,
                                   'user_id' => $is,
                               );
                           }
                   $YE = intval($ROLE->addAll($KES));
                           $aaa = intval($YE + count($KES));

                           $dddd = intval($ROLES[0]['id']);
                           for($j=$YE;$j<$aaa;$j++) {
                               $WQ['role_id'] = $dddd;
                               $auth_access = $auth_access11->where($WQ)->select();
                               $KK = '';
                               foreach($auth_access as $v => $n){
                                   $KK[] = array(
                                       'role_id' => $j,
                                       'rule_name' => $n['rule_name'],
                                       'type' => $n['type'],
                                   );
                               }
                   $HTLE5 =  $auth_access11->addAll($KK);
                               $dddd++;
                           }
               if($_POST['banben_type']==2){
                   $company_contract->company_id=$seid;
                   $company_contract->sign_time=strtotime($_POST['sign_time']);
                   $company_contract->expire_time=strtotime($_POST['expire_time']);
                   $company_contract->use_time=$_POST['zhengshi_type']*12;
                   $company_contract->status=1;
                   $HTLE6=$company_contract->add();
               }else if($_POST['banben_type']==1){
                   $HTLE6=true;
               }

               if($HTLE && $HTLE1 && $HTLE2 && $HTLE3 && $YE && $HTLE5 && $HTLE6){
                   $company->commit();//成功则提交
                   $this->success('新增成功',U('index'));
               }else{
                   $company->rollback();//不成功，则回滚
                   $this->error('新增失败');
               }
           }else{
               $this->error($company->getError());
           }
       }else{
           $admin_user = M('admin_user');
           $NO['id'] = $is;
               if(is_agent()) {
                   $listid = $admin_user->where($NO)->getField('id');
                   $ETl11['user_type'] = 'software_install';
                   $ETl11['from_agent_id'] = $listid;
                   $ENT = '1';
                   $LISTO1 = $admin_user->where($ETl11)->select();
                   $ETl22['user_type'] = 'direct_sales';
                   $ETl22['from_agent_id'] = $listid;
                   $LISTO2 = $admin_user->where($ETl22)->select();
               }
           $vip = M('software_vip')->select();
           $province= $this->getProvince(false);
           $this->assign('_province',$province);
           $this->assign('is',$is);
           $this->assign('list1',$LISTO1);
           $this->assign('list2',$LISTO2);
           $this->assign('vip',$vip);
           $this->assign('ent',$ENT);
           $this->assign('list_cc',$this->toFormat());
           $this->display();
       }

    }

    public function getProvince($type=true){
        $data=M('address_province')->select();
        if($type){
            $this->ajaxReturn($data);
        }else{
            return $data;
        }
    }
    public function getCity(){
        $provinceCode=I('get.provinceCode');
        $map['provinceCode']=$provinceCode;
        $data=M('address_city')->where($map)->select();
        $this->ajaxReturn($data);
    }
    public function getTown(){
        $towncode=I('get.cityCode');
        $map['cityCode']=$towncode;
        $data=M('address_town')->where($map)->select();
        $this->ajaxReturn($data);
    }
    public function toFormat(){
        $list_cc = M('Company_category')->select();
        $tree = new Tree();
        $list_cc = $tree->toFormatTree($list_cc);
        return $list_cc;
    }

//    public function addtwo($ids){
//       if($_POST){
//           $aaa = array(
//               array('nickname','require','姓名不能为空'),
//               array('username','require','登录名称不能为空'),
//               array('username','unique','登录名称被占用'),
//               array('password','require','密码不能为空'),
//               array('mobile','require','手机号码不能为空'),
//               array('user_type','require','用户类型不能为空'),
//               array('email','email','邮箱格式不正确')
//           );
//            if(I('ids') == 0){
//                $this->error('请先录入公司信息',U('addone'));
//            }else{
//                $company = M('admin_user');
//                if($company->validate($aaa)->create()){
//                    $company->password =user_md5(I('password'));
//                    $company->create_time = time();$company->reg_ip = ip2long(get_client_ip());
//                    if($company->add()){
//                        $this->success('新增成功',U('index'));
//                    }else{
//                        $this->error('新增失败',U('addtwo'));
//                    }
//                }else{
//                    $this->error($company->getError());
//                }
//            }
//       }
//           if(!is_administrator() && !is_platform_manage()) {
//               $map['founder_id'] = $map2['founder_id'] = $ids;
//           }else{
//               $map['founder_id'] = $map2['founder_id'] = '';
//           }
//           $dep = M('Department')->where($map2)->select();
//           $province= $this->getProvince(false);
//           $this->assign('_province',$province);
//           $this->assign('list_cc',$this->toFormat());
//           $this->assign('id',$ids);
//           $this->assign('dep',$dep);
//           $this->display();
//
//
//    }

    public function delt($id,$name){
        $company =M('company');
        $map['id'] = $id;
        if($company->where($map)->setField('status',-1)){
            $this->success('删除成功',U('index'));
        }else{
            $this->error('删除失败',U('index'));
        }
    }

}