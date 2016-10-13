<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-12-10
 * Time: 15:58
 */

namespace Weixin\Controller;
use Admin\Controller\AdminController;

class WebsiteController extends AdminController{

    //分类管理首页
    public function websiteCategory(){

        //判断是否设置了公众号
        $wid=session('wid');
        if($wid) {
            $MemberPublic = M('member_public');
            $data2 = $MemberPublic->where('id=' . $wid)->find();
            $map['token'] = $data2['token']; //获得公众号的token，作为查找相关数据列表内数据的条件
            $map['uid'] = UID;  //获得当前登录用户id，作为查找相关数据列表内数据的条件
            $WeisiteCategory = M('WeisiteCategory');
            $info = $WeisiteCategory->where($map)->order('id asc')->select();
            $this->assign('info', $info);
        }
        $this->display('Weixin/Website/website_category');
    }

    public function websiteCategoryAdd(){

        $id=I('id');
        $WeisiteCategory = M('WeisiteCategory');

        if(IS_POST) {
            $rules = array(
                array('title', 'require', '分类标题必须填写'),
            );
            if ($WeisiteCategory->validate($rules)->create()) {
                $WeisiteCategory->uid=UID;
                $WeisiteCategory->token=session('token');
                if ($id==0) {
                    if ($WeisiteCategory->add()) {
                        $this->success('添加成功', U('websiteCategory'));
                    } else {
                        $this->error('添加失败');
                    }
                } else {
                    if ($WeisiteCategory->save()!==false) {
                        $this->success('更新成功', U('websiteCategory'));
                    } else {
                        $this->error('更新失败');
                    }
                }
            } else {
                $this->error($WeisiteCategory->getError());
            }
        }

        if($id!==0){
            $info = $WeisiteCategory->where('id = '.$id)->find();
            $this->assign('info',$info);
            $this->assign('id',$id);
        }

        $this->display('Weixin/Website/website_category_add');
    }

    public function websiteSlideshow(){

        $wid=session('wid');
        if($wid) {
            $MemberPublic = M('member_public');
            $data2 = $MemberPublic->where('id=' . $wid)->find();
            $map['token'] = $data2['token'];
            $map['uid'] = UID;
            $WeisiteSlideshow = M('WeisiteSlideshow');
            $info = $WeisiteSlideshow->where($map)->order('id asc')->select();
            $this->assign('info', $info);
        }
        $this->display('Weixin/Website/website_slideshow');
    }

    public function websiteSlideshowAdd(){

        $id=I('id');
        $WebsiteSlideshow = M('WeisiteSlideshow');

        if(IS_POST) {
            $rules = array(
                array('img', 'require', '幻灯片图片必须添加'),
            );
            if ($WebsiteSlideshow->validate($rules)->create()) {
                $WebsiteSlideshow->uid=UID;
                $WebsiteSlideshow->token=session('token');
                if ($id==0) {
                    if ($WebsiteSlideshow->add()) {
                        $this->success('添加成功', U('websiteSlideshow'));
                    } else {
                        $this->error('添加失败');
                    }
                } else {
                    if ($WebsiteSlideshow->save()!==false) {
                        $this->success('更新成功', U('websiteSlideshow'));
                    } else {
                        $this->error('更新失败');
                    }
                }
            } else {
                $this->error($WebsiteSlideshow->getError());
            }
        }

        if($id!==0){
            $info = $WebsiteSlideshow->where('id = '.$id)->find();
            $this->assign('info',$info);
            $this->assign('id',$id);
        }

        $this->display('Weixin/Website/website_slideshow_add');
    }

    public function websiteFooter(){

        $wid=session('wid');
        if($wid) {
            $MemberPublic = M('member_public');
            $data2 = $MemberPublic->where('id=' . $wid)->find();
            $map['token'] = $data2['token'];
            $map['uid'] = UID;
            $map['pid']=0;
            $WeisiteFooter = M('WeisiteFooter');
            $info = $WeisiteFooter->where($map)->order('id asc')->select();
            foreach($info as $k=>$v){
                $cData = $WeisiteFooter->where("pid=".$info[$k]['id'])->order('id asc')->select();
                $data[]=$info[$k];
                foreach($cData as $kk=>$vb){
                    $cData[$kk]['title'] = "|——".$cData[$kk]['title'];
                    $data[]=$cData[$kk];
                }
            }
            $this->assign('info', $data);
        }
        $this->display('Weixin/Website/website_footer');
    }

    public function websiteFooterAdd(){

        $id=I('id');
        $WeisiteFooter = M('WeisiteFooter');

        if(IS_POST) {
            $rules = array(
                array('title', 'require', '菜单名必须填写'),
            );
            if ($WeisiteFooter->validate($rules)->create()) {
                $WeisiteFooter->uid=UID;
                $WeisiteFooter->token=session('token');
                if ($id==0) {
                    if ($WeisiteFooter->add()) {
                        $this->success('添加成功', U('websiteFooter'));
                    } else {
                        $this->error('添加失败');
                    }
                } else {
                    if ($WeisiteFooter->save()!==false) {
                        $this->success('更新成功', U('websiteFooter'));
                    } else {
                        $this->error('更新失败');
                    }
                }
            } else {
                $this->error($WeisiteFooter->getError());
            }
        }

        if($id!==0){
            $info = $WeisiteFooter->where('id = '.$id)->find();
            $map['uid']=UID;
            $map['token']=session('token');
            $map['pid']=0;
            $infos=$WeisiteFooter->where($map)->select();
            $this->assign('info',$info);
            $this->assign('infos',$infos);
            $this->assign('id',$id);
        }

        if($id==0){
            $map['uid']=UID;
            $map['token']=session('token');
            $map['pid']=0;
            $data = $WeisiteFooter->where($map)->select();
            $this->assign('data',$data);
        }

        $this->display('Weixin/Website/website_footer_add');
    }

    public function websiteReplyNews(){

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
        $this->display('Weixin/Website/website_reply_news');
    }


    public function websiteReplyNewsAdd(){

        $id=I('id');
        $CustomReplyNews = M('CustomReplyNews');

        if(IS_POST) {
            $rules = array(
                array('keyword', 'require', '关键字必须填写'),
                array('title', 'require', '标题名称必须填写')
            );
            if ($CustomReplyNews->validate($rules)->create()) {
                $CustomReplyNews->uid=UID;
                $CustomReplyNews->token=session('token');
                if ($id==0) {
                    if ($CustomReplyNews->add()) {
                        $this->success('添加成功', U('websiteReplyNews'));
                    } else {
                        $this->error('添加失败');
                    }
                } else {
                    if ($CustomReplyNews->save()!==false) {
                        $this->success('更新成功', U('websiteReplyNews'));
                    } else {
                        $this->error('更新失败');
                    }
                }
            } else {
                $this->error($CustomReplyNews->getError());
            }
        }

        $WeisiteCategory = M('WeisiteCategory');
        $map['uid']=UID;
        $map['token']=session('token');
        $data=$WeisiteCategory->where($map)->select();
        $this->assign('data',$data);

        if($id!==0){
            $info = $CustomReplyNews->where('id = '.$id)->find();
            $this->assign('info',$info);
            $this->assign('id',$id);
        }

//        if($id==0){
//            $map['uid']=UID;
//            $map['token']=session('token');
//            $map['pid']=0;
//            $data = $CustomReplyNews->where($map)->select();
//            $this->assign('data',$data);
//        }


        $this->display('Weixin/Website/website_reply_news_add');
    }

    public function websiteModolIndex(){


        $this->display('Weixin/Website/website_modol_index');
    }
    public function websiteModollist(){


        $this->display('Weixin/Website/website_modol_list');
    }
    public function websiteModoldetail(){


        $this->display('Weixin/Website/website_modol_detail');
    }
    public function websiteModolfooter(){


        $this->display('Weixin/Website/website_modol_footer');
    }

    //删除列表中数据记录
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


    //将选中的模板及模板类型存入关系表中
    public function saveModel(){

        $template=I('post.template');
        $type=I('post.type');

        $data['template']=$template;
        $data['type']=$type;
        $data['uid']=UID;
        $data['token']=session('token');

        $WeisiteModelRelation = M('weisite_model_relation');
        $map['type']=$data['type'];
        $map['uid']=UID;
        $map['token']=session('token');
        $WeisiteModelRelation->where($map)->delete();
        if($WeisiteModelRelation->create($data)){
            if ($WeisiteModelRelation->add()) {
                $this->success('添加成功', U('websiteReplyNews'));
            } else {
                $this->error('添加失败');
            }
        }else{
            $this->error($WeisiteModelRelation->getError());
        }

        if($type==1){
            $this->assign('name',$template);
            $this->display('Weixin/Website/website_modol_index');
        }elseif($type==2){
            $this->assign('name',$template);
            $this->display('Weixin/Website/website_modol_list');
        }elseif($type==3){
            $this->assign('name',$template);
            $this->display('Weixin/Website/website_modol_detail');
        }elseif($type==4){
            $this->assign('name',$template);
            $this->display('Weixin/Website/website_modol_footer');
        }

    }


    //选中的模板在网页中预览---首页和底部模板
    public function preview(){

        $map['uid']=UID;
        $map['token']=session('token');
        $WeisiteModelRelation = M('weisite_model_relation');  //模板关系表
        $data=$WeisiteModelRelation
            ->join('LEFT JOIN wm_weisite_model ON wm_weisite_model.name = wm_weisite_model_relation.template')
            ->where($map)
            ->select();

        $map1['uid']=UID;
        $map1['token']=session('token');
        $map1['is_show']=1;
        $WeisiteCategory = M('weisite_category');     //分类表
        $Category=$WeisiteCategory->where($map1)->select();
        $map2['uid']=UID;
        $map2['token']=session('token');
        $map2['is_show']=1;
        $WeisiteSlideshow = M('weisite_slideshow');   //首页幻灯片表
        $Slideshow=$WeisiteSlideshow->where($map2)->select();
        $WeisiteFooter = M('weisite_footer');       //底部表
        $condition['uid']=UID;
        $condition['token']=session('token');
        $condition['pid']=0;
        $Footer=$WeisiteFooter->where($condition)->select();

        foreach($Footer as $k=>$v){
            $Footer_child = $WeisiteFooter->where("pid=".$Footer[$k]['id'])->order('id asc')->select();
            foreach($Footer_child as $kk=>$vb){
                $Footer[$k]['child']=$Footer_child;
            }
        }

        $customReplyNews = M('custom_reply_news');   //新闻表
        $ReplyNews=$customReplyNews->where($map)->select();


        $this->assign('data',$data);
        $this->assign('category',$Category);
        $this->assign('slideshow',$Slideshow);
        $this->assign('footer',$Footer);
        $this->assign('ReplyNews',$ReplyNews);

        foreach($data as $k=>$v){
            if($v['type']==1){
                $this->display($v['path']);
            }elseif($v['type']==4){
                $this->assign('footer_path',$v['path']);
                $this->display($v['path']);
            }
        }

    }


    //选中的模板在网页中预览---图文列表模板
    public function previeList(){

        $map['uid']=UID;
        $map['token']=session('token');
        $WeisiteModelRelation = M('weisite_model_relation');  //模板关系表
        $data=$WeisiteModelRelation
            ->join('LEFT JOIN wm_weisite_model ON wm_weisite_model.name = wm_weisite_model_relation.template')
            ->where($map)
            ->select();

        $WeisiteFooter = M('weisite_footer');       //底部表
        $condition['uid']=UID;
        $condition['token']=session('token');
        $condition['pid']=0;
        $Footer=$WeisiteFooter->where($condition)->select();

        foreach($Footer as $k=>$v){
            $Footer_child = $WeisiteFooter->where("pid=".$Footer[$k]['id'])->order('id asc')->select();
            foreach($Footer_child as $kk=>$vb){
                $Footer[$k]['child']=$Footer_child;
            }
        }

        $map1['uid']=UID;
        $map1['token']=session('token');
        $map1['cate_id']=I('get.id');
        $customReplyNews = M('custom_reply_news');   //新闻表
        $ReplyNews=$customReplyNews->where($map1)->select();

        $this->assign('data',$data);
        $this->assign('footer',$Footer);
        $this->assign('ReplyNews',$ReplyNews);
//        dump($ReplyNews);exit;
        foreach($data as $k=>$v){
            if($v['type']==2){

                $this->display($v['path']);
            }elseif($v['type']==4){
                $this->assign('footer_path',$v['path']);
                $this->display($v['path']);
            }
        }
    }


    //选中的模板在网页中预览---图文内容模板
    public function detail(){

        $map['uid']=UID;
        $map['token']=session('token');
        $WeisiteModelRelation = M('weisite_model_relation');  //模板关系表
        $data=$WeisiteModelRelation
            ->join('LEFT JOIN wm_weisite_model ON wm_weisite_model.name = wm_weisite_model_relation.template')
            ->where($map)
            ->select();

        $WeisiteFooter = M('weisite_footer');       //底部表
        $condition['uid']=UID;
        $condition['token']=session('token');
        $condition['pid']=0;
        $Footer=$WeisiteFooter->where($condition)->select();

        foreach($Footer as $k=>$v){
            $Footer_child = $WeisiteFooter->where("pid=".$Footer[$k]['id'])->order('id asc')->select();
            foreach($Footer_child as $kk=>$vb){
                $Footer[$k]['child']=$Footer_child;
            }
        }

        $map1['uid']=UID;
        $map1['token']=session('token');
        $map1['id']=I('get.id');
        $customReplyNews = M('custom_reply_news');   //新闻表
        $info=$customReplyNews->where($map1)->find();

        $this->assign('data',$data);
        $this->assign('footer',$Footer);
        $this->assign('info',$info);

        foreach($data as $k=>$v){
            if($v['type']==3){
                $this->display($v['path']);
            }elseif($v['type']==4){
                $this->assign('footer_path',$v['path']);
                $this->display($v['path']);
            }
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
}