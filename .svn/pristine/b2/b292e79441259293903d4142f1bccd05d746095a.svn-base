<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/3
 * Time: 13:14
 */

namespace Mobile\Controller;
    use Think\Controller;


    class HongbaoController extends Controller {
        public function index(){
            if(is_login()){
                $hongbao = M('bao');
                $time = time();
                $where['phone'] = '';
                $where['bao_status'] = 0;
                //$where['end_time'] = array('gt',$time);
                $cx_hongbao = $hongbao->where($where)->select();
                $this->assign('bao',$cx_hongbao);
                $this->display();
            }else{
                $this->display('users/login');
            }

        }
        public function _initialize()
        {
            $jssdk = new JSSDK("wx8692c5e7bb713d60", "16c88e7edf75a13163a0c12a56c5b117");
            $signPackage = $jssdk->GetSignPackage();
            $this->assign('signPackage',$signPackage);
        }
    }


    class JSSDK {
        private $appId;
        private $appSecret;
        public function __construct($appId, $appSecret) {
            $this->appId = $appId;
            $this->appSecret = $appSecret;
        }
        public function getSignPackage() {
            $jsapiTicket = $this->getJsApiTicket();
            $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $timestamp = time();
            $nonceStr = $this->createNonceStr();
            // 这里参数的顺序要按照 key 值 ASCII 码升序排序
            $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
            $signature = sha1($string);
            $signPackage = array(
                "appId"     => $this->appId,
                "nonceStr"  => $nonceStr,
                "timestamp" => $timestamp,
                "url"       => $url,
                "signature" => $signature,
                "rawString" => $string
            );
            return $signPackage;
        }
        private function createNonceStr($length = 16) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $str = "";
            for ($i = 0; $i < $length; $i++) {
                $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            }
            return $str;
        }
        private function getJsApiTicket() {
            // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
            $data = json_decode(file_get_contents("jsapi_ticket.json"));
            if (empty($data) || $data->expire_time < time()) {
                $accessToken = $this->getAccessToken();
                $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
                $res = json_decode($this->httpGet($url),true);
                $ticket = $res['ticket'];
                if ($ticket) {
                    $data->expire_time = time() + 7000;
                    $data->jsapi_ticket = $ticket;
                    $fp = fopen("jsapi_ticket.json", "a");
                    fwrite($fp, json_encode($res));
                    fclose($fp);
                }
            } else {
                $ticket = $data->jsapi_ticket;
            }
            return $ticket;
        }
        private function getAccessToken() {
            // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
            $data = json_decode(file_get_contents("access_token.json"));
            if (empty($data) ||  $data->expire_time < time()) {
                $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
                $res = json_decode($this->httpGet($url),true);
                $access_token = $res['access_token'];
                if ($access_token) {
                    $data->expire_time = time() + 7000;
                    $data->access_token = $access_token;
                    $fp = fopen("access_token.json", "a");
                    fwrite($fp, json_encode($res));
                    fclose($fp);
                }
            } else {
                $access_token = $data->access_token;
            }
            return $access_token;
        }
        private function httpGet($url) {
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

?>

