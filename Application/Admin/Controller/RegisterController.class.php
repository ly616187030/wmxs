<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\CommonController;
use Think\Verify;
use Org\Util\String;

class RegisterController extends CommonController {

    /**
     * 后台注册
     * @author jry <598821125@qq.com>
     */
    public function register(){
        if(is_waps()){
            exit('<h4 style="padding:20px 30px;font-size:26px;">请使用PC电脑登录！</h4>');
        }
//         C('TOKEN_ON',false);
        if(IS_POST){

            //创建新增配送员的动态验证
            $rules = array(
                array('tel','require','手机号码不能为空'),
                array('tel','/^1[3|4|5|8][0-9]\d{8}$/','请输入正确的手机号',2,'regex',3),
                array('tel', '', '该手机号码已经存在！', 0, 'unique', 1),
                array('verify','require','识别码不能为空！'),
                array('phoneVerify','require','手机动态码不能为空！'),
                array('c_name','require','公司名称不能为空'),
                array('user','require','联系人不能为空'),
                array('pwd','require','密码不能为空'),
                array("pwd",'6,12','密码长度在6-12位',1,'length',1),
                array('email','require','邮箱不能为空'),
            );

            $data=I('post.');
            $company = M('company');
            $user_object = M('admin_user');

            if($company->validate($rules)->create()){

                    $company->startTrans();//开启事务
                    //添加到公司注册表
                    $company->status=1;
                    $company->reg_time=time();
                    $company->password=user_md5($data['pwd']);
                    $company->tel=$data['tel'];
                    $company->username=$data['user'];
                    $company->email=$data['email'];
                    $company->vip_id=5;
                    $company->c_sn=time();
                    $company->c_name=$data['c_name'];
                    $company->banben_type = 1;
                    $company->shiy_type = 2;
                    $company->sy_content = 7;
                    $company->id=$a=String::keyGen();
                    $company->apply_type = 'front_register';
                    $b=$company->add();

                    //将公司ID填到admin_user表中的founder_id字段
                    $user_object->founder_id=$a;
                    $user_object->create_time=time();
                    $user_object->password=user_md5($data['pwd']);
                    $user_object->mobile=$data['tel'];
                    $user_object->user_type='company';
                    $user_object->username='U'.generate_username();
                    $user_object->status=1;
                    $user_object->nickname=$data['c_name'];
                    $user_object->email=$data['email'];
                    $user_object->push_msg_id = time();
                    $c=$user_id=$user_object->add();

                    //添加到wm_store_category表
                    $store_category = M('store_category');
                    $where1['founder_id']='administrator';
                    $storeinfo=$store_category->where($where1)->select();
                    foreach($storeinfo as $key=>$value){
                        $temp1[]=array(
                            "store_c_name"=>$value['store_c_name'],
                            "sort_order"=>$value['sort_order'],
                            "flag"=>$value['flag'],
                            "store_pic"=>$value['store_pic'],
                            "founder_id"=>$a
                        );
                    }
                    $d=$store_category->addAll($temp1);

                    //添加到role表
                    $role = M('role');
                    $where['all']=2;
                    $roleinfo=$role->where($where)->select();
                    foreach($roleinfo as $k=>$v){
                        $temp[]=array(
                            "name"=>$v['name'],
                            "pid"=>$v['pid'],
                            "status"=>$v['status'],
                            "remark"=>$v['remark'],
                            "create_time"=>time(),
                            "update_time"=>$v['update_time'],
                            "listorder"=>$v['listorder'],
                            "founder_id"=>$a,
                            "user_id"=>$user_id,
                            "all"=>0
                        );

                    }
//                    $role->addAll($temp);
                    $e=$role_id=$role->addAll($temp);

                    //role_id范围查询
                    $start_id=$role_id;
                    $count=$start_id+count($temp);
                    $start=$roleinfo[0]['id'];

                    for($i=$start_id;$i<$count;$i++){
                        $auth_access=M('auth_access');
                        $authinfo=$auth_access->where('role_id='.$start)->select();
                        $temp2='';
                        foreach($authinfo as $kk=>$vv){
                            $temp2[]=array(
                                "role_id"=>$i,
                                "rule_name"=>$vv['rule_name'],
                                "type"=>$vv['type'],
                            );
                        }
                        $f=$auth_access->addAll($temp2);
                        $start++;
                    }

                    //添加到用户管理组
                    $role_user=M('role_user');
                    $data['user_id']=$user_id;
                    $data['role_id']=2;
                    $g=$role_user->add($data);

                    if( $b && $c && $d && $e && $f && $g){
                        $company->commit();
                        $return['status'] = true;
                        $return['url'] = U('Public/login');
                        $return['info'] ='注册成功！';
                        $this->ajaxReturn($return, 'JSON');
                    }else{
                        $company->rollback();
                        $return['status'] = false;
                        $return['info'] = '注册失败！';
                        $this->ajaxReturn($return, 'JSON');
                    }
            }else{
                $this->error($company->getError());
            }
        }
            $this->assign('meta_title', '体验注册');
            $this->display();
    }

    //手机是否注册
    public function exist(){

        $mobile=I('get.tel');
        $user_object = M('admin_user');
        $company=M('company');
        $info1=$company->where('tel='.$mobile)->find();
        $info=$user_object->where('mobile='.$mobile)->find();
        if($info || $info1){
            $return['status'] = true;
            $return['info'] = '此手机号已经注册';
            $this->ajaxReturn($return, 'JSON');
        }else{
            $return['status'] = false;
            $return['info'] = '此手机号没有注册';
            $this->ajaxReturn($return, 'JSON');
        }
    }

    //当前手机号设置的密码是否与数据库的密码相同
    public function ypwd(){

        $mobile=I('get.tel');
        $password=I('get.password');
        $user_object = M('admin_user');
        $info=$user_object->where('mobile='.$mobile)->find();
        if(user_md5($password)==$info['password']){
            $return['status'] = false;
            $return['info'] = '密码与原密码相同';
            $this->ajaxReturn($return, 'JSON');
        }else{
            $return['status'] = true;
            $this->ajaxReturn($return, 'JSON');
        }
    }

    //识别码是否正确
    public function shibie(){

        $verify=I('get.verify');
        // 图片验证码校验
        if (!$this->check_verify($verify)) {
            $return['status'] = false;
            $return['info'] = '输入的识别码有误';
            $this->ajaxReturn($return, 'JSON');
        }else{
            $return['status'] = true;
            $this->ajaxReturn($return, 'JSON');
        }
    }

    //验证码是否正确
    public function yanzheng(){
        $phoneVerify=I('get.phoneVerify');
        //手机动态码校验
         if ($phoneVerify != session('mobilecode')) {
            $return['status'] = false;
            $return['info'] = '验证码输入错误,请核对！';
            $this->ajaxReturn($return, 'JSON');
        }else{
             $return['status'] = true;
             $this->ajaxReturn($return, 'JSON');
         }
    }

    //发送验证码
    public function sendSms(){
        $phone=I('get.phone');
        $uid="zy_wmxs";
        $password="wmxsa111111";
        $code=$this->generateCode();
        session('mobilecode',null);
        session('mobilecode',$code);
        $content=iconv('UTF-8','GB2312','您的验证码是:').$code.iconv('UTF-8','GB2312',',请在5分钟内正确输入。');
        $url="http://service.winic.org/sys_port/gateway/?id=".$uid."&pwd=".$password."&to=".$phone."&content=".$content."&time=";
        $res=$this->httpGet($url);
        $this->ajaxReturn($res);
    }

    //随机生成验证码
    private  function generateCode($length = 6) {
        return rand(pow(10,($length-1)), pow(10,$length)-1);
    }

    //随机生成用户名
    private  function Code($length = 10) {
        return rand(pow(10,($length-1)), pow(10,$length)-1);
    }
    /*
     * GET 请求
     *
     * @param string $url
     */
    private function httpGet($url) {
        $oCurl = curl_init ();
        if (stripos ( $url, "https://" ) !== FALSE) {
            curl_setopt ( $oCurl, CURLOPT_SSL_VERIFYPEER, FALSE );
            curl_setopt ( $oCurl, CURLOPT_SSL_VERIFYHOST, FALSE );
        }
        curl_setopt ( $oCurl, CURLOPT_URL, $url );
        curl_setopt ( $oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec ( $oCurl );
        $aStatus = curl_getinfo ( $oCurl );
        curl_close ( $oCurl );
        if (intval ( $aStatus ["http_code"] ) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * 检测验证码
     * @param  integer $id 验证码ID
     * @return boolean 检测结果
     */
    function check_verify($code, $vid = 1) {
        $verify = new Verify();
        return $verify->check($code, $vid);
    }


    function getpwd(){

        if(IS_POST){
            $tel=I('tel');
            $password=I('password');

            $company=M('company');
            $admin_user=M('admin_user');

            $data['password']=user_md5($password);
            $hasnum=$company->data($data)->where('tel='.$tel)->save() && $admin_user->data($data)->where('mobile='.$tel)->save();
            if($hasnum){
                $return['status'] = true;
                $return['info'] = '密码设置成功！';
                $return['url'] = U('Public/login');
                $this->ajaxReturn($return, 'JSON');
            }else{
                $return['status'] = false;
                $return['info'] = '密码设置失败！';
                $this->ajaxReturn($return, 'JSON');
            }
        }
        $this->display();
    }
}