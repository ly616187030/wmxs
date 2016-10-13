<?php
namespace Mobile\Controller;
class CardController extends MobileController{
    public $weObj;
    public function _initialize() {
        import('Vendor.WechatPhpSdk.Wx');
        $options = array(
            'appid'=>'wxc45fb3b35bb2d07d', //填写高级调用功能的app id, 请在微信开发模式后台查询
            'appsecret'=>'9475ab6e4f6ea13b7271c39d2c0900b4' //填写高级调用功能的密钥
        );
        $this->weObj = new \wechat($options); //创建实例对象
    }
    public function index(){
           $this->weObj->valid();
           $type = $this->weObj->getRevEvent();
          switch($type) {
           		case Wechat::EVENT_SUBSCRIBE:
                    $this->weObj->text("help info")->reply();
                   			break;
                   		case Wechat::EVENT_UNSUBSCRIBE:
                   			break;
                   		default:
                            $this->weObj->text("help info")->reply();
                   }
    }
}