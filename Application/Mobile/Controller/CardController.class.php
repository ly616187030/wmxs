<?php
namespace Mobile\Controller;
class CardController extends MobileController{
    public $weObj;
    public function _initialize() {
        import('Vendor.WechatPhpSdk.Wx');
        $options = array(
            'appid'=>'wxc45fb3b35bb2d07d', //��д�߼����ù��ܵ�app id, ����΢�ſ���ģʽ��̨��ѯ
            'appsecret'=>'9475ab6e4f6ea13b7271c39d2c0900b4' //��д�߼����ù��ܵ���Կ
        );
        $this->weObj = new \wechat($options); //����ʵ������
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