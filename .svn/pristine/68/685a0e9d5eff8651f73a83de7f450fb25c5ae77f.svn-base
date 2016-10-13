<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Dispatch\Model;
use Think\Model;
/**
 * 管理员与用户组对应关系模型
 * @author jry <598821125@qq.com>
 */
class StationModel extends Model {
    /**
     * 数据库表名
     * @author jry <598821125@qq.com>
     */
    protected $tableName = 'stations';

    /**
     * 自动验证规则
     * @author jry <598821125@qq.com>
     */
    protected $_validate = array(
        array('station_name', 'require', '站点名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('station_name', '', '站点名称已经存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('user_name', 'require', '负责人姓名不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('province', 'require', '省不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
//        array('city', 'require', '市不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
//        array('town', 'require', '区不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /**
     * 自动完成规则
     * @author jry <598821125@qq.com>
     */
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('status', '1', self::MODEL_INSERT),
    );
}
