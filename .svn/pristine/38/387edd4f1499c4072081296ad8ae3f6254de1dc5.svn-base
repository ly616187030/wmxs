<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-12-09
 * Time: 11:43
 */

namespace Weixin\Controller;
use Admin\Controller\AdminController;

class CustomReplyController extends AdminController
{

    //图文自定义回复
    public function CustomReply_lists(){
            $wid=session('wid');
            if($wid) {
                $MemberPublic = M('member_public');
                $data2 = $MemberPublic->where('id=' . $wid)->find();
                $map['wm_custom_reply_news.token'] = $data2['token'];
                $map['wm_custom_reply_news.uid'] = UID;
                $CustomReplyNews = M('CustomReplyNews');
                $info = $CustomReplyNews
                    ->join('LEFT JOIN wm_weisite_category ON wm_weisite_category.id = wm_custom_reply_news.cate_id')
                    ->field('wm_weisite_category.title as category,wm_custom_reply_news.*')
                    ->where($map)
                    ->order('id asc')
                    ->select();
                $this->assign('info', $info);
            }
            $this->display('Weixin/CustomReply/CustomReply_lists');
        }

    //图文自定义回复添加
    public function CustomReply_lists_add()
    {
        $id=I('id');
        if($id==0){
            if(IS_POST){
                $keyword=I('post.keyword');
                $keyword_type=I('post.keyword_type');
                $title=I('post.title');
                $intro=I('post.intro');
                $cate_id=I('post.cate_id');
                $cover=I('post.cover');
                $sort=I('post.sort');
                $jump_url=I('post.jump_url');
                $content = I('post.content');
                $token=session('token');

                if ($keyword == '') {
                    $return['info'] = '关键词必须填写';
                    $this->ajaxReturn($return, 'JSON');
                } elseif ($title == '') {
                    $return['info'] = '标题必须填写';
                    $this->ajaxReturn($return, 'JSON');
                }

                $data['keyword']=$keyword;
                $data['keyword_type']=$keyword_type;
                $data['title']=$title;
                $data['intro']=$intro;
                $data['cate_id']=$cate_id;
                $data['cover']=$cover;
                $data['sort']=$sort;
                $data['jump_url']=$jump_url;
                $data['uid']=UID;
                $data['token']=$token;
                $data['cTime']=time();
                $data['content']=$content;

                $CustomReplyNews=M('CustomReplyNews');
                if($CustomReplyNews->create($data)){
                    $addid=$CustomReplyNews->add();
                    if($addid>0){
                        $return['status'] = true;
                        $return['url'] = U('CustomReply/CustomReply_lists');
                        $return['info'] = '添加成功，正在跳转';
                        $this->ajaxReturn($return, 'JSON');
                    }else{
                        $return['status'] = false;
                        $return['info'] = $CustomReplyNews->getError();
                        $this->ajaxReturn($return, 'JSON');
                    }
                }else{
                    $this->error('数据不存在！');
                }
            }else{
                $WeisiteCategory=M('WeisiteCategory');
                $map['token']=session('token');
                $map['uid']=UID;
                $data=$WeisiteCategory->where($map)->select();
                $this->assign('data',$data);
            }

        }else{
            if(IS_POST){
                $id=I('id');
                $keyword=I('post.keyword');
                $keyword_type=I('post.keyword_type');
                $title=I('post.title');
                $intro=I('post.intro');
                $cate_id=I('post.cate_id');
                $cover=I('post.cover');
                $sort=I('post.sort');
                $jump_url=I('post.jump_url');
                $content = I('post.content');
                $token=session('token');

                $data['id']=$id;
                $data['keyword']=$keyword;
                $data['keyword_type']=$keyword_type;
                $data['title']=$title;
                $data['intro']=$intro;
                $data['cate_id']=$cate_id;
                $data['cover']=$cover;
                $data['sort']=$sort;
                $data['jump_url']=$jump_url;
                $data['uid']=UID;
                $data['token']=$token;
                $data['cTime']=time();
                $data['content']=$content;

                $CustomReplyNews=M('CustomReplyNews');
                if($CustomReplyNews->where('id='.$id)->save($data)!==false) {
                        $return['status'] = true;
                        $return['url'] = U('CustomReply/CustomReply_lists');
                        $return['info'] = '编辑成功，正在跳转';
                        $this->ajaxReturn($return, 'JSON');
                }else{
                        $return['status'] = false;
                        $return['info'] = $CustomReplyNews->getError();
                        $this->ajaxReturn($return, 'JSON');
                    }
            }else{
                $map['id']=$id;
                $map['uid']=UID;
                $CustomReplyNews=M('CustomReplyNews');
                $WeisiteCategory=M('WeisiteCategory');
                $infos=$CustomReplyNews->where($map)->find();
                $map1['token']=session('token');
                $map1['uid']=UID;
                $data=$WeisiteCategory->where($map1)->select();
                $this->assign('data',$data);
                $this->assign('infos',$infos);
            }
        }
        $this->display('Weixin/CustomReply/CustomReply_lists_add');
    }

    //多图文自定义回复
    public function CustomReplyMult_lists()
        {


            $this->display('Weixin/CustomReply/CustomReplyMult_lists');
        }

    //多图文自定义回复添加
    public function CustomReplyMult_lists_add()
    {

        $this->display('Weixin/CustomReply/CustomReplyMult_lists_add');
    }

    //文本自定义回复
    public function CustomReplyText_lists()
        {
            $wid=session('wid');
            if($wid) {
                $MemberPublic = M('member_public');
                $data2 = $MemberPublic->where('id=' . $wid)->find();
                $map['token'] = $data2['token'];
                $map['uid'] = UID;
                $CustomReplyText = M('CustomReplyText');
                $info = $CustomReplyText->where($map)->order('id asc')->select();
                $this->assign('info', $info);
            }
            $this->display('Weixin/CustomReply/CustomReplyText_lists');
        }

    //文本自定义回复添加
    public function CustomReplyText_lists_add()
    {
        $id=I('id');
        if($id==0){
            if(IS_POST){
                $keyword=I('post.keyword');
                $keyword_type=I('post.keyword_type');
                $sort=I('post.sort');
                $content = I('post.content');
                $token=session('token');

                if ($keyword == '') {
                    $return['info'] = '关键词必须填写';
                    $this->ajaxReturn($return, 'JSON');
                } elseif ($content == '') {
                    $return['info'] = '回复必须填写';
                    $this->ajaxReturn($return, 'JSON');
                }

                $data['keyword']=$keyword;
                $data['keyword_type']=$keyword_type;
                $data['uid']=UID;
                $data['token']=$token;
                $data['content']=$content;
                $data['sort']=$sort;

                $CustomReplyText=M('CustomReplyText');
                if($CustomReplyText->create($data)){
                    $addid=$CustomReplyText->add();
                    if($addid>0){
                        $return['status'] = true;
                        $return['url'] = U('CustomReply/CustomReplyText_lists');
                        $return['info'] = '添加成功，正在跳转';
                        $this->ajaxReturn($return, 'JSON');
                    }else{
                        $return['status'] = false;
                        $return['info'] = $CustomReplyText->getError();
                        $this->ajaxReturn($return, 'JSON');
                    }
                }else{
                    $this->error('数据不存在！');
                }
            }else{
                $WeisiteCategory=M('WeisiteCategory');
                $map['token']=session('token');
                $map['uid']=UID;
                $data=$WeisiteCategory->where($map)->select();
                $this->assign('data',$data);
            }
        }else{
            if(IS_POST){
                $id=I('id');
                $keyword=I('post.keyword');
                $keyword_type=I('post.keyword_type');
                $sort=I('post.sort');
                $content = I('post.content');
                $token=session('token');

                $data['keyword']=$keyword;
                $data['keyword_type']=$keyword_type;
                $data['uid']=UID;
                $data['token']=$token;
                $data['content']=$content;
                $data['sort']=$sort;

                $CustomReplyText=M('CustomReplyText');
                if($CustomReplyText->where('id='.$id)->save($data)!==false) {
                    $return['status'] = true;
                    $return['url'] = U('CustomReply/CustomReplyText_lists');
                    $return['info'] = '编辑成功，正在跳转';
                    $this->ajaxReturn($return, 'JSON');
                }else{
                    $return['status'] = false;
                    $return['info'] = $CustomReplyText->getError();
                    $this->ajaxReturn($return, 'JSON');
                }
            }else{
                $map['id']=$id;
                $map['uid']=UID;
                $CustomReplyText=M('CustomReplyText');
                $infos=$CustomReplyText->where($map)->find();
                $this->assign('infos',$infos);
            }
        }
        $this->display('Weixin/CustomReply/CustomReplyText_lists_add');
    }

    //删除
    public function del(){
        $m=I('get.m');
        $id = I('id');
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

    //判断有没有设置公众号和公众号设置是否正确
    public function is_exit(){

        $wid=session('wid');
        if($wid){

            $MemberPublic = M('member_public');
            $map['id']=$wid;
            $map['uid']=UID;
            $data2 = $MemberPublic->where($map)->find();
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