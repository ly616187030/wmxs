<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Model;
use Think\Model;
/**
 * 管理员与用户组对应关系模型
 * @author jry <598821125@qq.com>
 */
class WeixinModel extends Model {
    /**
     * 数据库表名
     * @author jry <598821125@qq.com>
     */
    protected $tableName = 'weixin_pay';

    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('mchname', 'require', '商户名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        //array('mchid', 'require', '商户ID不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('appid', 'require', 'APPID不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('appsecret', 'require', 'APPSECRET不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        //array('app_key', 'require', 'APPKEY不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        //array('app_cert', 'require', '证书不能为空，请上传', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        //array('app_cert_key', 'require', '证书不能为空，请上传', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        //array('appid', '', 'APPID已经存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('status', '1', self::MODEL_INSERT),
        array('founder_id',FID,self::MODEL_INSERT)
    );

}
