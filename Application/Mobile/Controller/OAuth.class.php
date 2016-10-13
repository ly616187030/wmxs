<?php
namespace Mobile\Controller;
class OAuth{
    public static function getCode($appid,$url,$scope){
        $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=".$scope."&state=STATE#wechat_redirect";
        return $url;
    }

    /**
     * 第二步：通过code换取网页授权access_token,同时获取openid
     * @param $appid string
     * @param $secret string
     * @param $code string
     * @return bool|mixed
     */
    public static function getAccessToken($appid,$secret,$code){
        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code";
        return self::http_get($url);
    }

    /**
     * 第三步：刷新access_token（如果需要）
     * @param $appid string
     * @param $ref_token string
     * @return bool|mixed
     */
    public static function refreshToken($appid,$ref_token){
        $url="https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=".$appid."&grant_type=refresh_token&refresh_token=".$ref_token;
        $res=self::http_get($url);
        return $res['access_token'];
    }

    /**
     * 第四步：拉取用户信息(需scope为 snsapi_userinfo)
     * @param $appid string
     * @param $secret string
     * @param $code string
     * @param $openid string
     * @return bool|mixed
     */
    public static function getUserInfo($appid,$secret,$code){
        $access=self::getAccessToken($appid,$secret,$code);
        $access_token=$access['access_token'];
        $openid=$access['openid'];
        if(!empty($access_token)){
            $url="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
            return self::http_get($url);
        }else{
            return 'access_token获取错误';
        }
    }
    /**
     * GET  请求
     * @param $url
     * @return bool|mixed
     */
    private static function http_get($url) {
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
            return json_decode($sContent,true);
        } else {
            return false;
        }
    }
}