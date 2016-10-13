<?php
namespace Admin\Controller;
class SmsController extends AdminController
{
    public function index(){
        $list = D('Sms')->where('founder_id = '.FID)->find();
        $url = "http://service.winic.org:8003/service.asmx/GetUserInfo?uid={$list['uid']}&pwd={$list['pwd']}";
        $xml = simplexml_load_string(http_get($url),'SimpleXMLElement', LIBXML_NOCDATA);
        $rres = (string)$xml;
        $xmlstr =explode('/', $rres);
        $bal = round($xmlstr[2],2);
        D('Sms')->where('founder_id = '.FID)->save(array('balance'=>$bal));
        $detail = M('SmsRecharge')->where('founder_id = '.FID)->select();
        if($list) $list['remain'] = $bal;
        $this->assign('detail',$detail);
        $this->assign('list',$list);
        $this->display();
    }
    public function add(){
        $sms = D('Sms');
        $info = $sms->where('founder_id = '.FID)->find();
        if($info) $this->error('您已经有账号了，且只能创建有一个账号',U('index'));
            if($sms->create()){

                $res = http_build_query($_POST);
                $url = 'http://service.winic.org:8003/service.asmx/RegineUser?';
                $url.=$res;
                $xmlstr = http_get($url);
                $xml = simplexml_load_string($xmlstr,'SimpleXMLElement', LIBXML_NOCDATA);
                $rres = (string)$xml;
                if($rres == '000'){
                    $sms->founder_id = FID;
                    if($sms->add()){
                        $this->success('添加成功');
                    }else{
                        $this->error('添加失败');
                    }
                }elseif($rres == '-01'){
                    $this->error('上级账号不存在');
                }elseif($rres == '-02'){
                    $this->error('该账号已经存在');
                }
            }else{
                $this->error($sms->getError());
            }
    }

    /**
     * 短信支付
     */
    public function recharge(){
        $fee = I('fee');
        $accountid = I('smsid');
        $smsuid = I('smsuid');
        if(empty($accountid)) $this->error('请先注册短信账号');
        if(!preg_match("/^[1-9]{1}\d{0,4}$/",$fee)){
            $this->error('只能输入整数金额，且不超过5位数');
        }
        $total = $fee;
        $data['sms_account_id'] = $accountid;
        $data['fee'] = $total;
        $data['r_sn'] = createOrdersn();
        $data['founder_id'] = FID;
        $data['ctime'] = time();
        $data['uid'] = $smsuid;
        $re = M('SmsRecharge');
        if($re->add($data)){
            $this->success('',"http://{$_SERVER['SERVER_NAME']}/WxPay/pay/native.php?ordsn={$data['r_sn']}&total={$total}&smsuid={$smsuid}");
        }else{
            $this->error('充值失败');
        }
        

    }

    /**
     * 充值到子账号
     * @param $smsuid
     * @param $fee
     */

    public function realRecharge($smsuid,$fee,$ordsn){
        $url = "http://service.winic.org:8009/sys_port/money/opermoney.asp?username=rundasoft&password=13947573286&sub_username={$smsuid}&product=sms&money={$fee}";
        $xmlstr = http_get($url);
        //$xml = simplexml_load_string($xmlstr,'SimpleXMLElement', LIBXML_NOCDATA);
        //$rres = (string)$xml;
        //$rres = '000/472.2904/.055/0/.3/0/.2/0/.3|.07/.1/0/.3/0/.2/0/.3';
        if(preg_match('/^000\//',$xmlstr)){
            M('SmsRecharge')->where('r_sn = '.$ordsn)->setField('is_recharge',1);
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('fail');

        }
    }
}