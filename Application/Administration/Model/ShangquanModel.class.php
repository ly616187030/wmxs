<?phpnamespace Administration\Model;use Think\Model;class ShangquanModel extends Model{    protected $_validate = array(        array('sq_name','','商圈名称已经存在！',0,'unique',1),//验证商圈名称是否重复        array('sq_name','require','请输入商圈名称',1,'regex',3),//验证商圈名称是否为空    );}