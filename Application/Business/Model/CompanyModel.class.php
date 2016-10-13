<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-04-13
 * Time: 18:02
 */
namespace Business\Model;
use Think\Model;
class CompanyModel extends Model{

    protected $_validate = array(

        array('c_name','require','公司名称不能为空',1,'regex',2),
        array('province','require','省不能为空',1,'regex',2),
        array('city','require','市不能为空',1,'regex',2),
        array('county','require','区、县不能为空',1,'regex',2),
        array('username','require','联系人不能为空',1,'regex',2),
        array('post_code','number','邮编格式不正确',2,'regex',2),
        array('tel','require','手机不能为空',1,'regex',2),
        array('tel','number','手机必须是数字',2,'regex',2),
        array('tel','','手机号已经存在！',1,'unique',2),
        array('qq','number','QQ必须是数字',2,'regex',2),
        array('email','email','邮箱格式不正确',2,'regex',2),
    );
}