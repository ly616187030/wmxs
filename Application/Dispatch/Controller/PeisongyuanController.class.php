<?php


namespace Dispatch\Controller;

use Admin\Controller\AdminController;

class PeisongyuanController extends AdminController {


     public function index(){
          $person = M('delivery_person');
          if(IS_POST){
               $post = I('post.');
                      $res = array();
                      foreach ($post as $v => $n) {
                            if ($post[$v] != null || $post[$v] != "") {
                                  switch ($v) {
                                        case "person_name":
                                              $v = "a.person_name";
                                              $n = array(
                                                    "LIKE",
                                                    "%" . $n . "%"
                                              );
                                              $res[$v] = $n;
                                              break;
                                        case "phone":
                                              $v = "a.phone";
                                              $n = array(
                                                    "like",
                                                    "%" . $n . "%"
                                              );
                                              $res[$v] = $n;
                                              break;
                                        case 'status':
                                              $v = 'a.status';
                                              $res[$v] = $n;
                                              break;
                                        case 'ygzt':
                                              $v = 'a.ygzt';
                                              $res[$v] = $n;
                                              break;
                                  }
                            }
                      }
//                      var_dump($res);
              $uid=is_login();
              $founder_info=M('admin_user')->where('id='.$uid)->field('founder_id,user_type')->find();

              //如果是企业账号登陆
              if($founder_info['user_type']=='company'){
                  $res['founder_id'] = $founder_info['founder_id'];
              }

              //如果是企业下创建的商家
              if($founder_info['user_type']=='company_member'){
                  $res['uid'] = UID;
              }

                      $peisong = $person
                            ->alias('a')
                            ->join(' LEFT JOIN __ADDRESS_CITY__ AS b ON a.city = b.code')
                            ->where($res)
                            ->field('a.*,b.name')
                            ->select();
                      $count = count($peisong);// 查询满足要求的总记录数
                      $this->assign('count', $count);
                      $this->assign('list', $peisong);
          }else{

              $uid=is_login();
              $founder_info=M('admin_user')->where('id='.$uid)->field('founder_id,user_type')->find();

              //如果是企业账号登陆
              if($founder_info['user_type']=='company'){
                  $map['a.founder_id'] = $founder_info['founder_id'];
              }

              //如果是企业下创建的商家
              if($founder_info['user_type']=='company_member'){
                  $map['a.uid'] = UID;
              }
                $map['a.status']=1;


              $count = $person
                    ->alias('a')
                    ->join(' LEFT JOIN __ADDRESS_CITY__ AS b ON a.city = b.code')
                    ->join(' LEFT JOIN __ADMIN_USER__ AS user ON user.id = a.uid')
                    ->where($map)
                    //->field('a.*,b.name,user.nickname')
                    ->count();
            //$count = count($peisong);
                $Page       = new \Think\Page($count,15);
                $Page->setConfig('header','<span class="rows">共 %TOTAL_ROW% 条记录</span>');
                $Page->setConfig('first','首页');
                $Page->setConfig('prev','上一页');
                $Page->setConfig('next','下一页');
                $Page->setConfig('last','末页');
                $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% ');
                $Page->lastSuffix = false;
                $show       = $Page->show();
                $peisong = $person
                    ->alias('a')
                    ->join(' LEFT JOIN __ADDRESS_CITY__ AS b ON a.city = b.code')
                    ->join(' LEFT JOIN __ADMIN_USER__ AS user ON user.id = a.uid')
                    ->where($map)
                    ->field('a.*,b.name,user.nickname')
                    ->limit($Page->firstRow . ',' . $Page->listRows)
                    ->select();
            $this->assign('page',$show);
            $this->assign('count',$count);
            $this->assign('list',$peisong);
          }

          $this->meta_title = '配送员管理';
	      $this->display();
     }


     /**
      * 添加配送员
      * @author CHANGKAI <job_ck@126.com>
      */
     public function add(){

         $delivery_person = M('delivery_person');
//         C('TOKEN_ON',false);
          if (IS_POST) {

               //创建新增配送员的动态验证
               $rules = array(array('person_name','require','请输入配送员姓名',1,'regex',3),
                     array('gender','require','请选择性别',1,'regex',3),
                     array('person_name','require','配送员姓名不能为空！'),
                     array('person_bianhao','number','配送员编号必须是数字',2,'regex',3),
                     array('password','require','请输入密码！'),
                     array("password",'6,20','密码长度在6-20位',1,'length',1),
                     array("repassword",'password','确认密码不一致',0,'confirm'),
                     array('identity','/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/','请输入正确的身份证号',2,'regex',3),
                     array('phone','require','配送员手机号不能为空！'),
                     array('phone', '', '该手机号码已经存在！', 0, 'unique', 1),
               );

               if ($delivery_person->validate($rules)->create()){
                   $delivery_person->password=md5(I('post.password'));
                   $founder_info=M('admin_user')->where('id='.UID)->field('founder_id')->find();
                   $delivery_person->founder_id=$founder_info['founder_id'];
                   $delivery_person->uid = I('post.uid');
                   $delivery_person->status = 1;
                   $a=$delivery_person->add();
                    if ($a) {
                        action_log('add_action', 'delivery_person', $a, is_login());
                         $this->success('添加配送员成功!', U('index'));
                    } else {
                         $this->error('添加配送员失败!');
                    }
               } else {
                    $this->error($delivery_person->getError());
               }
          } else {
               //城市商圈四级联动
               $province = A('MenuPct1')->getProvince(false);
               $this->assign('_province', $province);

              $deliverInfo=M('admin_user')->where('id='.is_login())->field('founder_id,user_type,nickname,id')->find();
              if($deliverInfo['user_type']=='company'){
                  $where['founder_id']=$deliverInfo['founder_id'];
                  $where['user_type']='company_member';
                  $info=M('admin_user')->where($where)->select();
              }
              if($deliverInfo['user_type']=='company_member'){
                  $where['id']=is_login();
                  $info=M('admin_user')->where($where)->select();
              }

              $this->assign('info', $info);
               $this->meta_title = '添加配送员';
               $this->display();
          }
     }


     /**
      * 编辑配送员
      * @author changkai <job_ck@126.com>
      */
     public function edit(){

//         C('TOKEN_ON',false);
          if(IS_POST){
               $rules = array(
                     array('person_name','require','请输入配送员姓名',1,'regex',3),
                     array('person_bianhao','number','配送员编号必须是数字',2,'regex',3),
                     array('person_name','require','配送员姓名不能为空！'),
                     array('identity','/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/','请输入正确的身份证号',2,'regex',3),
                     array('phone','require','配送员手机号不能为空！'),
                     );

               $delivery_person = M("delivery_person");
               if ($delivery_person->validate($rules)->create()){
                    $id=I('post.person_id');
                    if($delivery_person->where('person_id='.$id)->save()!== false){
                        action_log('edit_action', 'delivery_person', $id, is_login());
                         $this->success('更新成功',U('index'));
                    }else{
                         $this->error('更新失败');
                    }
               }else{
                    $this->error($delivery_person->getError());

               }
          } else {
               //城市商圈四级联动
               $province = A('MenuPct1')->getProvince(false);
               $this->assign('_province', $province);

               //获取配送员信息
               $id = I('get.person_id');
               $list = M("delivery_person")->field(true)->find($id);
               if (false === $list) {
                    $this->error('读取配送员信息失败!');
               }
//              dump($list);
              $list['cityname'] = $this->get_city_name('city',$list['city']);
              $list['townname'] = $this->get_city_name('town',$list['town']);


               $this->assign('list', $list);

              $deliverInfo=M('admin_user')->where('id='.is_login())->field('founder_id,user_type,nickname,id')->find();
              if($deliverInfo['user_type']=='company'){
                  $where['founder_id']=$deliverInfo['founder_id'];
                  $where['user_type']='company_member';
                  $info=M('admin_user')->where($where)->select();
              }
              if($deliverInfo['user_type']=='company_member'){
                  $where['id']=is_login();
                  $info=M('admin_user')->where($where)->select();
              }

              $this->assign('info', $info);

               $this->meta_title = '编辑配送员';
               $this->display();
          }
     }

     public function del(){

          $id = I('person_id');
          if($id){
              $m = M('delivery_person');
              $where['person_id']=$id;
              $data['status']=-1;
              $list = $m->data($data)->where($where)->save();
               if ($list) {
                   action_log('recycle_action', 'delivery_person', $id, is_login());
                    $this->success('删除成功');
               } else {
                    $this->error('删除失败');
               }
          } else{
               $this->error('请选择要操作的数据');
          }

     }

    function get_city_name($type,$code){

        $map['code']=$code;

        if($type=='province'){

            $data=M('address_province')->where($map)->find();

            return $data['name'];

        }elseif($type=='city'){

            $data=M('address_city')->where($map)->find();

            return $data['name'];

        }elseif($type=='town'){

            $data=M('address_town')->where($map)->find();

            return $data['name'];

        }

    }

}

