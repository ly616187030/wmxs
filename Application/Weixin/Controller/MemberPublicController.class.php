<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-11-16
 * Time: 15:18
 */

namespace Weixin\Controller;
use Admin\Controller\AdminController;

class MemberPublicController extends AdminController
{

    public function index(){

        $MemberPublic = M('MemberPublic');
        $data = $MemberPublic->order('id desc')->select();

        $this->assign('info', $data);
        $this->display('Weixin/MemberPublic/index');
    }

    public function setSession(){

        $id = I('id');
        $title = I('title');
        session('wid',$id);
//        dump(session('wid'));exit;
        $this->redirect('Weixin/MemberPublic/index');
    }

    public function step_0()
    {

        if (IS_POST) {
            $public_name = I('public_name');
            $public_id = I('public_id');
            $wechat = I('wechat');
            $type = I('type');
            $uid = UID;

            if ($public_name == '') {
                $return['info'] = '公众号名称不能为空';
                $this->ajaxReturn($return, 'JSON');
            } elseif ($public_id == '') {
                $return['info'] = '原始ID不能为空';
                $this->ajaxReturn($return, 'JSON');
            } elseif ($wechat == '') {
                $return['info'] = '微信号不能为空';
                $this->ajaxReturn($return, 'JSON');
            } elseif ($type == '') {
                $return['info'] = '公众号类型不能为空';
                $this->ajaxReturn($return, 'JSON');
            }
            $data['public_name'] = $public_name;
            $data['public_id'] = $public_id;
            $data['token'] = $public_id;
            $data['wechat'] = $wechat;
            $data['type'] = $type;
            $data['uid'] = $uid;

            $MemberPublic = M('MemberPublic');
            if ($MemberPublic->create($data)) {
                $id = $MemberPublic->add();
                if ($id > 0) {
                    $return['status'] = true;
                    $return['url'] = U('MemberPublic/step_1?id=' . $id);
                    $return['info'] = '添加成功，正在跳转';
                    $this->ajaxReturn($return, 'JSON');
                } else {
                    $return['status'] = false;
                    $return['info'] = $MemberPublic->getError();
                    $this->ajaxReturn($return, 'JSON');
                }
            } else {
                $this->error('数据不存在！');
            }
        }
        $this->display('Weixin/MemberPublic/step_0');
    }

    public function step_1()
    {
        $id = I('id');
        $this->assign('id', $id);

        $this->display('Weixin/MemberPublic/step_1');
    }

    public function step_2()
    {
        $id = I('id');
        $this->assign('id', $id);
        if (IS_POST) {

            $appid = I('appid');
            $secret = I('secret');
            $encodingaeskey = I('encodingaeskey');

            if ($appid == '') {
                $return['info'] = '应用ID不能为空';
                $this->ajaxReturn($return, 'JSON');
            } elseif ($secret == '') {
                $return['info'] = '应用密钥不能为空';
                $this->ajaxReturn($return, 'JSON');
            }

            $data['appid'] = $appid;
            $data['secret'] = $secret;
            $data['encodingaeskey'] = $encodingaeskey;

            $MemberPublic = M('member_public');
            $re = $MemberPublic->where('id=' . $id)->save($data);
            if ($re) {
                $return['status'] = true;
                $return['url'] = U('MemberPublic/index?id=' . $id);
                $return['info'] = '添加成功，正在跳转';
                $this->ajaxReturn($return, 'JSON');
            } else {
                $return['status'] = false;
                $return['info'] = $MemberPublic->getError();
                $this->ajaxReturn($return, 'JSON');
            }
        } else {
            $this->display('Weixin/MemberPublic/step_2');
        }
    }

    //编辑step_0
    public function step_edit_0(){

        $id=I('id');
        $this->assign('id', $id);
        if(IS_POST){
        $public_name = I('public_name');
        $public_id = I('public_id');
        $wechat = I('wechat');
        $type = I('type');
        $uid = UID;

        $data['public_name'] = $public_name;
        $data['public_id'] = $public_id;
        $data['token'] = $public_id;
        $data['wechat'] = $wechat;
        $data['type'] = $type;
        $data['uid'] = $uid;

        $MemberPublic = M('MemberPublic');
        if($MemberPublic->where('id='.$id)->save($data)!==false){
            $return['status'] = true;
            $return['url'] = U('MemberPublic/step_edit_1?id=' . $id);
            $return['info'] = '添加成功，正在跳转';
            $this->ajaxReturn($return, 'JSON');
        }else{
            $return['status'] = false;
            $return['info'] = $MemberPublic->getError();
            $this->ajaxReturn($return, 'JSON');
        }
    }
        $MemberPublic = M('member_public');
        $data=$MemberPublic->where('id='.$id)->find();
        $this->assign('info',$data);
        $this->display('Weixin/MemberPublic/step_edit_0');
    }

    //编辑step_1
    public function step_edit_1(){
        $id=I('id');

        $this->assign('id',$id);
        $this->display('Weixin/MemberPublic/step_edit_1');
    }

    //编辑step_2
    public function step_edit_2(){

        $id=I('id');
        $this->assign('id', $id);
        if(IS_POST){
            $appid = I('appid');
            $secret = I('secret');
            $encodingaeskey = I('encodingaeskey');
            $uid = UID;

            $data['appid'] = $appid;
            $data['secret'] = $secret;
            $data['encodingaeskey'] = $encodingaeskey;
            $data['uid'] = $uid;

            $MemberPublic = M('MemberPublic');
            if($MemberPublic->where('id='.$id)->save($data)!==false){
                $return['status'] = true;
                $return['url'] = U('MemberPublic/index?id=' . $id);
                $return['info'] = '添加成功，正在跳转';
                $this->ajaxReturn($return, 'JSON');
            }else{
                $return['status'] = false;
                $return['info'] = $MemberPublic->getError();
                $this->ajaxReturn($return, 'JSON');
            }
        }
        $MemberPublic = M('member_public');
        $data=$MemberPublic->where('id='.$id)->find();
        $this->assign('info',$data);
        $this->display('Weixin/MemberPublic/step_edit_2');
    }


    //删除公众号信息内容
    public function del()
    {

        $m = M('MemberPublic');
        $id = I('id');
        //判断id是否是数组类型
        if (is_array($id)) {
            //执行批量删除操作
            $where = 'id in (' . implode(',', $id) . ')';
        } else {
            //执行单挑删除操作
            $where = 'id =' . $id;
        }

        session('wid',null);
        session('wtitle',null);

        $list = $m->where($where)->delete();
        if ($list > 0) {
            $this->success('删除成功');
        } else {

            $this->error('删除失败');
        }
    }

    //修改公众号信息
    public function edit()
    {

        $m = M('MemberPublic');
        $id = I('id');
        $where = 'id =' . $id;
        $list = $m->where($where)->save();
        if ($list > 0) {
            $this->success('修改成功');
        } else {

            $this->error('修改失败');
        }
        $this->display('Weixin/MemberPublic/step_0');
    }

    //自定义菜单内容显示页
    public function menu()
    {
        $wid=session('wid');
        $this->assign('wid', $wid);
        if($wid){
            $MemberPublic = M('member_public');
            $data2 = $MemberPublic->where('id=' . $wid)->find();

            $CustomMenu = M('CustomMenu');
            $where['pid']=0;
            $where['token']=$data2['token'];
            $pData = $CustomMenu->where( $where )->order('id asc')->select();
            foreach($pData as $k=>$v){
                $cData = $CustomMenu->where("pid=".$pData[$k]['id'])->order('id asc')->select();
                $data[]=$pData[$k];
                foreach($cData as $kk=>$vb){
                    $cData[$kk]['title'] = "|——".$cData[$kk]['title'];
                    $data[]=$cData[$kk];
                }
            }
            $this->assign('info', $data);
        }

        $this->display('Weixin/MemberPublic/menu');
    }


    //判断有没有设置公众号和公众号设置是否正确
    public function is_exit(){

        $wid=session('wid');
            if($wid){

                $MemberPublic = M('member_public');
                $data2 = $MemberPublic->where('id=' . $wid)->find();
                $WeChatMenu = new WeChatMenu("weixin", $data2['appid'], $data2['secret']);
                $AccessToken = $WeChatMenu->getAccessToken();

                if($AccessToken!==null){
                    session('token',$data2['token']);
                    $return['status'] = true;
                    $this->ajaxReturn($return,'JSON');
                }else{
                    $return['status'] = false;
                    $return['info'] = '请设置正确的appid和secret!';
                    $this->ajaxReturn($return,'JSON');
                }
            }else{
                $return['status'] = false;
                $return['info'] = '请设置公众号信息!';
                $this->ajaxReturn($return,'JSON');
            }
    }
    //添加自定义菜单项
    public function addMenu()
    {
                if (IS_POST) {
                    $sort = I('sort');
                    $pid = I('pid');
                    $title = I('title');
                    $type = I('type');
                    $keyword = I('keyword');
                    $url = I('url');
                    $wid=session('wid');
                    $uid = UID;
                    $MemberPublic = M('member_public');
                    $info = $MemberPublic->where('id=' . $wid)->find();

                    if ($sort == '') {
                        $return['info'] = '排序号不能为空';
                        $this->ajaxReturn($return, 'JSON');
                    } elseif ($pid == '') {
                        $return['info'] = '一级菜单不能为空';
                        $this->ajaxReturn($return, 'JSON');
                    } elseif ($title == '') {
                        $return['info'] = '菜单名不能为空';
                        $this->ajaxReturn($return, 'JSON');
                    } elseif ($type == '') {
                        $return['info'] = '类型不能为空';
                        $this->ajaxReturn($return, 'JSON');
                    }

                    $data['sort'] = $sort;
                    $data['pid'] = $pid;
                    $data['title'] = $title;
                    $data['type'] = $type;
                    $data['keyword'] = $keyword;
                    $data['url'] = $url;
                    $data['uid'] = $uid;
                    $data['token'] = $info['token'];

                    $CustomMenu = M('CustomMenu');
                    if ($CustomMenu->create($data)) {
                        $addid = $CustomMenu->add();
                        if ($addid > 0) {
                            $return['status'] = true;
                            $return['url'] = U('MemberPublic/menu');
                            $return['info'] = '添加成功，正在跳转';
                            $this->ajaxReturn($return, 'JSON');
                        } else {
                            $return['status'] = false;
                            $return['info'] = $CustomMenu->getError();
                            $this->ajaxReturn($return, 'JSON');
                        }
                    } else {
                        $this->error('数据不存在！');
                    }
                } else {

                    $wid=session('wid');
                    $MemberPublic = M('member_public');
                    $info = $MemberPublic->where('id=' . $wid)->find();

                    $where['pid']=0;
                    $where['token']=$info['token'];
                    $CustomMenu = M('CustomMenu');
                    $data=$CustomMenu->where( $where )->select();

                    $this->assign('info',$data);
                    $this->display('Weixin/MemberPublic/addmenu');
                }
    }


    /**
        生成手机微信自定义菜单
     */
    public function send_menu(){

        //取得当前公众号id从数据库取出appid和secret
        $wid=session('wid');
        $this->assign('wid',$wid);
        if($wid){
            $MemberPublic = M('member_public');
            $info = $MemberPublic->where('id=' . $wid)->find();

            //实例化WeChatMenu类，得到$access_token
            $WeChatMenu = new WeChatMenu("weixin", $info['appid'], $info['secret']);
            $access_token=$WeChatMenu->getAccessToken();

            if($access_token==null){
                $return['status'] = false;
                $return['info'] = '请设置正确的appid和secret!';
                $this->ajaxReturn($return,'JSON');
            }else {
                //$this->ajaxReturn($access_token);
                //通过token字段取得当前公众号的自定义菜单
                $CustomMenu = M('CustomMenu');
                $where['pid'] = 0;
                $where['token'] = $info['token'];
                $pData = $CustomMenu->where($where)->order('id asc')->select();

                //定义一个菜单
                $menu = new MenuDefine();
                $menu->menuStart();  //菜单开始

                //遍历菜单得到端口需要的数据格式
                foreach ($pData as $k => $v) {
                    $menu->addMenu($pData[$k]['title'], $pData[$k]['keyword'], $pData[$k]['type'], $pData[$k]['url']);
                    $cData = $CustomMenu->where("pid=" . $pData[$k]['id'])->order('id asc')->select();
                    foreach ($cData as $kk => $vv) {
                        $menu->addMenuItem($cData[$kk]['title'], $cData[$kk]['keyword'], $cData[$kk]['type'], $cData[$kk]['url']);
                    }
                }
                $menu->menuEnd();

                //生成自定义菜单
                $WeChatMenu->createMenu($menu->str, $access_token);


                $this->ajaxReturn($WeChatMenu->createMenu($menu->str, $access_token));
                //判断是否生成成功
                if ($WeChatMenu->createMenu($menu->str, $access_token) == true) {//生成自定义菜单成功
                    $return['status'] = true;
                    $return['url'] = U('MemberPublic/menu');
                    $return['info'] = '添加自定义菜单成功！';
                    $this->ajaxReturn($return, 'JSON');
                } else {//生成自定义菜单失败
                    $return['status'] = false;
                    $return['info'] = '添加自定义菜单失败！';
                    $this->ajaxReturn($return, 'JSON');
                }
            }
        }else{
            $return['status'] = false;
            $return['info'] = '请先配置公众号信息！';
            $this->ajaxReturn($return, 'JSON');
        }
        $this->display('Weixin/MemberPublic/menu');
    }


    /**
        删除公众号的自定义菜单
     */
    public function delMenu(){

        //取得当前公众号id从数据库取出appid和secret，得到$access_token
        $wid=session('wid');
        $this->assign('wid',$wid);
        if($wid) {
            $MemberPublic = M('member_public');
            $info = $MemberPublic->where('id=' . $wid)->find();
            $WeChatMenu = new WeChatMenu("weixin", $info['appid'], $info['secret']);
            $access_token = $WeChatMenu->getAccessToken();

            //微信里删除自定义菜单
            $WeChatMenu->deleteMenu($access_token);
            if ($WeChatMenu->deleteMenu($access_token) == true) {//删除成功
                $return['status'] = true;
                $m = M('CustomMenu');
                $where['token'] = $info['token'];
                $list = $m->where($where)->delete();
                if ($list > 0) {
                    $return['url'] = U('MemberPublic/menu');
                    $return['info'] = '删除自定义菜单成功！';
                    $this->ajaxReturn($return, 'JSON');
                }else{
                    $return['status'] = false;
                    $return['info'] = '删除自定义菜单失败！';
                    $this->ajaxReturn($return, 'JSON');
                }
            } else {//删除失败
                $WeChatMenu->deleteMenu($access_token);
            }
        }else{
            $return['status'] = false;
            $return['info'] = '请先配置公众号信息！';
            $this->ajaxReturn($return, 'JSON');
        }
        $this->display('Weixin/MemberPublic/menu');
    }

    //自定义菜单删除
    public function menudel()
    {
        $m = M('CustomMenu');
        $id = I('menuid');
        //判断id是否是数组类型
        if (is_array($id)) {
            //执行批量删除操作
            $where = 'id in (' . implode(',', $id) . ')';
        } else {
            //执行单挑删除操作
            $where = 'id =' . $id;
        }
        $list = $m->where($where)->delete();
        if ($list > 0) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    //自定义菜单编辑
    public function menuedit(){

        $id=I('id');
        $this->assign('id',$id);
        if(IS_POST){

            $sort = I('sort');
            $pid = I('pid');
            $title = I('title');
            $type = I('type');
            $keyword = I('keyword');
            $url = I('url');
            $uid=UID;

            $data['sort'] = $sort;
            $data['pid'] = $pid;
            $data['title'] = $title;
            $data['type'] = $type;
            $data['keyword'] = $keyword;
            $data['url'] = $url;
            $data['uid'] = $uid;

            $CustomMenu = M('CustomMenu');
            $edit=$CustomMenu->where('id='.$id)->save($data);
            if ($edit) {
                    $return['status'] = true;
                    $return['url'] = U('MemberPublic/menu');
                    $return['info'] = '编辑成功，页面正在自动跳转！';
                    $this->ajaxReturn($return, 'JSON');
                } else {
                    $return['status'] = false;
                    $return['info'] = '内容没有变化，请修改具体内容！';
                    $this->ajaxReturn($return, 'JSON');
                }
        }
        $CustomMenu = M('CustomMenu');
        $data = $CustomMenu->where('id='.$id)->find();

        $wid=session('wid');
        $MemberPublic = M('member_public');
        $info = $MemberPublic->where('id=' . $wid)->find();

        $where['pid']=0;
        $where['token']=$info['token'];
        $pdata=$CustomMenu->where( $where )->select();

        $this->assign('info',$data);
        $this->assign('pdata',$pdata);
        $this->display('Weixin/MemberPublic/editmenu');
    }

    //获得关注公众号的openid
     function getOpenid(){

//        $appid = "wxc45fb3b35bb2d07d";
//        $appsecret = "9475ab6e4f6ea13b7271c39d2c0900b4";
        $wid=session('wid');
        $MemberPublic = M('member_public');
        $info = $MemberPublic->where('id=' . $wid)->find();
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$info['appid']."&secret=".$info['secret'];

//        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        $jsoninfo = json_decode($output, true);
        $access_token = $jsoninfo["access_token"];

        $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=$access_token";
        $result = $this->https_request($url);
        $jsoninfo = json_decode($result);  // 默认false，为Object，若是True，为Array

        $data = $jsoninfo -> data;
         $str = array();
        foreach($data -> openid as $x=>$x_value) {
            //echo $x_value . ",";
            //echo "<br>";
            $str[] = $x_value;
        }
        return $str;

    }

    function https_request($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {return 'ERROR '.curl_error($curl);}
        curl_close($curl);
        return $data;
    }

    //粉丝信息列表
    function fans(){

            $wid=session('wid');
            $this->assign('wid',$wid);
            if($wid){
                $MemberPublic = M('member_public');
                $data = $MemberPublic->where('id=' . $wid)->find();
                $WeChatMenu = new WeChatMenu("weixin", $data['appid'], $data['secret']);
                $access_token = $WeChatMenu->getAccessToken();
                $this->assign('access_token',$access_token);
                if($access_token!==null){
                    $Follow = M('Follow');
                    $where['token']=$data['token'];
                    $infos=$Follow->where( $where )->order('id asc')->select();
                    $this->assign('info',$infos);
                }
            }
        $this->display('Weixin/MemberPublic/fans');
    }

    //获取粉丝信息
    public function fansLists(){

        //获得当前公众号的$access_token
        $wid=session('wid');
        $this->assign('wid',$wid);
        if($wid) {

            $openid = $this->getOpenid();
            if (empty($openid)){
                $return['status'] = false;
                $return['info'] = '请配置正确的appid和secret！';
                $this->ajaxReturn($return, 'JSON');
            }else{
                $MemberPublic = M('member_public');
                $info = $MemberPublic->where('id=' . $wid)->find();
                $WeChatMenu = new WeChatMenu("weixin", $info['appid'], $info['secret']);
                $access_token = $WeChatMenu->getAccessToken();


//        遍历openid通过接口获得用户信息
            foreach ($openid as $k => $v) {

                $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $access_token . "&openid=" . $v;
                $json_info = $this->https_request($url);
                $data = json_decode($json_info, 1);
                $data['token'] = $info['token'];
                $data['uid']=UID;

                $Follow = M('Follow');
                $where['openid'] = $data['openid'];
//            dump($Follow->where( $where )->count());
                if ($Follow->where($where)->count() == 0) {
                    $Follow->where($where)->add($data);
//                if($result>0){
//
//                }else{
//
//                }
                }
            }
            $Follow = M('Follow');
            $where['token'] = $info['token'];
            $infos = $Follow->where($where)->order('id asc')->select();
            $this->assign('info', $infos);
                $return['status'] = true;
                $return['url'] = U('MemberPublic/fans');
                $return['info'] ='更新粉丝列表成功！';
                $this->ajaxReturn($return, 'JSON');
            }
        }else{
            $return['status'] = false;
            $return['info'] = '请配置公众号信息！';
            $this->ajaxReturn($return, 'JSON');
        }
        $this->display('Weixin/MemberPublic/fans');
    }

}
/**
 * 微信公共平台菜单操作类
 *
 * 用于创建微信公共平台的自定义菜单
 *
 */
class WeChatMenu{
    private $AppId = "";  //公共平台提供的AppId
    private $AppSecret = ""; //公共平台提供的AppSecret
    public $AccessToken = ""; //公共平台提供的AccessToken

    private $platform = "weixin";  //平台类型。
    public $host = "api.weixin.qq.com";  ///平台服务器.

    public $errcode = 0;  //错误代码
    public $errmsg = "";  //错误信息文本

    /**
     * 构造函数
     * @param unknown_type $platform //平台类型。
     */
    function __construct($platform, $appId = "", $appSecret = ""){
        if ($platform == "易信") $platform = "yixin";

        if ($platform == "yixin") {
            $this->platform = "yixin";
            $this->host = "api.yixin.im";
        } else {
            $this->platform = "weixin";
            $this->host = "api.weixin.qq.com";
        }

        $this->setAppId($appId, $appSecret);
    }

    /**
     * 设置AppId 和 AppSecret
     */
    public function setAppId($AppId, $AppSecret){
        $this->AppId = $AppId;
        $this->AppSecret = $AppSecret;
    }

    /**
     * 获得AccessToken
     */
//    public function getAccessToken() {
//        if (empty($this->AppId)) return;
//        if (empty($this->AppSecret)) return;
//
//        $TOKEN_URL="https://".$this->host."/cgi-bin/token?grant_type=client_credential&appid=".$this->AppId."&secret=".$this->AppSecret;
//        $json=file_get_contents($TOKEN_URL);
//        $result=json_decode($json,true);
//        $this->AccessToken =$result['access_token'];
//        return $this->AccessToken;
//    }

    public function getAccessToken(){

            // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
            $data = json_decode(file_get_contents("access_token"));
            if (empty($data) ||  $data->expire_time < time()) {
                $url = "https://" . $this->host . "/cgi-bin/token?grant_type=client_credential&appid=" . $this->AppId . "&secret=" . $this->AppSecret;
                $res = json_decode($this->httpGet($url), true);
                $access_token = $res['access_token'];
                if ($access_token) {
                    $data->expire_time = time() + 7000;
                    $data->access_token = $access_token;
                    $fp = fopen("access_token", "a");
                    fwrite($fp, json_encode($res));
                    fclose($fp);
                }
            } else {
                $access_token = $data->access_token;
            }
            return $access_token;
        }

    private function httpGet($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

    /**
     * 创建菜单
     * @return boolean
     */
    public function createMenu($menuStr,$access_token)
    {

        if (empty($this->$access_token)) $this->getAccessToken();
        if (empty($menuStr)) return false;

        $CREATE_MENU_URL = "/cgi-bin/menu/create?access_token=" .$access_token;
        $json = sendPost($this->host, $CREATE_MENU_URL, $menuStr, false);

        $result = json_decode($json, true);

        if ($result['errcode'] == 0)
            return true;
        else {
            $this->errcode = $result['errcode'];
            $this->errmsg = $result['errmsg'];
            return false;
        }
    }

    /**
     * 删除菜单
     * @return boolean
     */
    public function deleteMenu($access_token)
    {
        if (empty($this->$access_token)) $this->getAccessToken();


        $DELETE_MENU_URL = "/cgi-bin/menu/delete?access_token=" .$access_token;

        $json =$this-> httpGet("https://api.weixin.qq.com".$DELETE_MENU_URL);
        $result = json_decode($json, true);
        if ($result['errcode'] == 0)
            return true;
        else {
            $this->errcode = $result['errcode'];
            $this->errmsg = $result['errmsg'];
            return false;
        }
    }

    /**
     * 从平台读取菜单，返回菜单JSON文本
     * @return
     */
    public function getMenu()
    {
        if (empty($this->AccessToken)) $this->getAccessToken();
        if (empty($this->AccessToken)) return "";

        $GET_MENU_URL = "/cgi-bin/menu/get?access_token=" . $this->AccessToken;
        $json = file_get_contents("https://" . $this->host . $GET_MENU_URL);
        return $json;
    }
}

/**
 * 菜单定义类: 用于定义菜单，生成菜单的JSON文本
 * @author JoStudio
 *
 */
class MenuDefine
{
    private $current_menu_name = "";  //当前菜单名称
    private $menus = array();         //菜单数组
    private $menuItems = array();     //菜单项数组

    public $str = "";     //菜单的JSON文本

    /**
     * 开始定义菜单
     */
    public function menuStart()
    {
        $this->menus = array();
        $this->menuItems = array();
        $this->current_menu_type = "";
        $this->current_menu_name = "";
        $this->current_menu_key = "";
        $this->current_menu_url = "";
        $this->str = "";
    }

    /**
     * 增加一个下拉菜单
     * @param unknown_type $name
     */
    public function addMenu($name, $key,$type,$url)
    {
        $this->finishMenu();
        if($type=='view'){
            $this->current_menu_type = $type;
            $this->current_menu_name = $name;
            $this->current_menu_url = $url;
        }else{
            $this->current_menu_type = $type;
            $this->current_menu_name = $name;
            $this->current_menu_key = $key;
        }

    }

    /**
     * 结束当前下拉菜单定义
     */
    private function finishMenu()
    {
        if (!empty($this->current_menu_name)) {
            if( $this->current_menu_type=='view'){
                $menu = array('name' => $this->current_menu_name,
                    "type"=>$this->current_menu_type,
                    "url"=>$this->current_menu_url,
                    "sub_button" => $this->menuItems);
            }else{
                $menu = array('name' => $this->current_menu_name,
                    "type"=>$this->current_menu_type,
                    "key"=>$this->current_menu_key,
                    "sub_button" => $this->menuItems);
            }
            $this->menus[] = $menu;
            $this->menuItems = array();
            $this->current_menu_name = "";
        }
    }

    /**
     * 增加一个菜单项
     * @param unknown_type $name
     * @param unknown_type $key
     */
    public function addMenuItem($name, $key,$type,$url)
    {
        if($type=='view'){
        $menuItem = array(
            'type' => $type,
            'name' => $name,
            'url'=>$url
        );
        }else{
         $menuItem = array(
            'type' => $type,
            'name' => $name,
            'key'=>$key
            );
        }
        $this->menuItems[] = $menuItem;
    }


    /**
     * 结束菜单定义
     */
    public function menuEnd()
    {
        $this->finishMenu();
        $data = array('button' => $this->menus);
        $this->str = my_json_encode($data);
    }
}


/**
 *  以POST方式向提定的URL提交数据，返回结果
 */
function sendPost($host, $url, $data, $isSSL = false)
{
    $port = 80;
    $prefix = "";
    if ($isSSL) {
        $prefix = "ssl://";
        $port = 443;
    }

    $header = "POST " . $url . " HTTP/1.0\r\n";
    $header .= "Host:$host:$port\r\n";
    $header .= "User-Agent: Mozilla 4.0\r\n";
    //$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Type: raw/xml\r\n";
    $header .= "Content-Length: " . strlen($data) . "\r\n";
    $header .= "Connection: Close\r\n\r\n";
    $header .= $data;

    $result = "";
    $content_started = false;
    if ($isSSL)
        $fp = fsockopen($prefix . $host, $port, $errno, $errstr);
    else
        $fp = fsockopen($host, $port);

    if ($fp) {
        fputs($fp, $header);
        while (!feof($fp)) {
            $line = fgets($fp);
            if ($content_started == false) {
                if ($line == "\r\n") $content_started = true;
            } else {
                $result .= $line;
            }
        }
        fclose($fp);
    }
    return $result;
}


/**
 * 自定义的json_encode函数, 返回json格式的文本
 */
function my_json_encode($var)
{
    switch (gettype($var)) {
        case 'boolean':
            return $var ? 'true' : 'false'; // Lowercase necessary!
        case 'integer':
        case 'double':
            return $var;
        case 'resource':
        case 'string':
            return '"' . str_replace(array("\r", "\n", "<", ">", "&"),
                array('\r', '\n', '\x3c', '\x3e', '\x26'),
                addslashes($var)) . '"';
        case 'array':
            // Arrays in JSON can't be associative. If the array is empty or if it
            // has sequential whole number keys starting with 0, it's not associative
            // so we can go ahead and convert it as an array.
            if (empty ($var) || array_keys($var) === range(0, sizeof($var) - 1)) {
                $output = array();
                foreach ($var as $v) {
                    $output[] = my_json_encode($v);
                }
                return '[ ' . implode(', ', $output) . ' ]';
            }
        // Otherwise, fall through to convert the array as an object.
        case 'object':
            $output = array();
            foreach ($var as $k => $v) {
                $output[] = my_json_encode(strval($k)) . ': ' . my_json_encode($v);
            }
            return '{ ' . implode(', ', $output) . ' }';
        default:
            return 'null';
    }
}


?>


