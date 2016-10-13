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
class SmsModel extends Model {
    /**
     * 数据库表名
     * @author jry <598821125@qq.com>
     */
    protected $tableName = 'sms_account';

    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('uid', 'require', '账号不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('pwd', 'require', '密码不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('co_name', 'require', '企业名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('mob', 'require', '手机不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('link_man', 'require', '联系人不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('ctime', NOW_TIME, self::MODEL_INSERT),
        /*array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('sort', '0', self::MODEL_INSERT),
        array('status', '1', self::MODEL_INSERT),*/
    );

}
