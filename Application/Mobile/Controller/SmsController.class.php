<?php
/**
 * Created by PhpStorm.
 * Auther: <zhangpf41@163.com>
 * Date: 2015/6/4
 * Time: 16:42
 */
namespace Mobile\Controller;
use Think\Controller;
class SmsController extends Controller{



    public function sendSms(){
        $phone=I('get.phone');
        $uid="gjz1010";
        $password="rundasoft";
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
}