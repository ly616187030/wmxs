<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\CommonController;
use Common\Util\Tree;
use Think;
/**
 * 后台公共控制器
 * 为什么要继承AdminController？
 * 因为AdminController的初始化函数中读取了顶部导航栏和左侧的菜单，
 * 如果不继承的话，只能复制AdminController中的代码来读取导航栏和左侧的菜单。
 * 这样做会导致一个问题就是当AdminController被官方修改后不会同步更新，从而导致错误。
 * 所以综合考虑还是继承比较好。
 * @author jry <598821125@qq.com>
 */
class AdminController extends CommonController {
    /**
     * 初始化方法
     * @author jry <598821125@qq.com>
     */
    protected function _initialize() {
        // 登录检测
        if (!$uuid=is_login()) { //还没登录跳转到登录页面
            $this->redirect('Admin/Public/login');
        }

        define('UID',$uuid);
        define('FID',session('user_auth.founder_id'));

        define('IS_ROOT', is_admin());
        if(!IS_ROOT && C('ADMIN_ALLOW_IP')){
            // 检查IP地址访问
            if(!in_array(get_client_ip(),explode(',',C('ADMIN_ALLOW_IP')))){
                $this->error('403:禁止访问');
            }
        }



        //dump(strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME));exit;
        if(!IS_ROOT){
            $access =   $this->accessControl();
            if ( false === $access ) {
                $this->error('403:禁止访问');
            }elseif(null === $access ){
                //检测访问权限
                $rule  = strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
                if ( !$this->checkRule($rule,array('in','1,2')) ){
                    $this->error('未授权访问!');
                }else{
                    // 检测分类及内容有关的各项动态权限
                    $dynamic    =   $this->checkDynamic();
                    if( false === $dynamic ){
                        $this->error('未授权访问!');
                    }

                }

            }

        }



        $this->assign('_menu_list',$this->getMenus());
        $this->assign('_user_auth', session('user_auth'));       // 用户登录信息
    }


    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final public function getMenus($controller=CONTROLLER_NAME){
        //$menus  =   session('ADMIN_MENU_LIST.'.$controller);
        if(empty($menus)){
            // 获取主菜单
            $mdb = D('Admin/Menu');
            if(is_admin()){
                $map['status'] = 1;
                $menus = $mdb->where($map)->order('listorder ASC')->select();
            }else{
                $m = M('role_user')
                    ->alias('a')
                    ->join("__ROLE__ AS g on a.role_id=g.id")
                    ->join("__AUTH_ACCESS__ AS r ON a.role_id=r.role_id")
                    ->where("a.user_id=".UID." and g.status='1'")
                    ->field('r.role_id,r.rule_name AS name')->select();
                foreach ($m as $rule) {
                    $authList[] = strtolower($rule['name']);
                }
                if(!empty($authList)){
                    $map['status'] = 1;
                    $map['url'] = array('in',$authList);
                    $menus = $mdb->where($map)->order('listorder ASC')->select();
                }

            }

            $tree = new Tree();
            $menus = $tree->list_to_tree($menus,'id','parentid');
            //session('ADMIN_MENU_LIST.'.$controller,$menus);
        }

        return $menus;

    }

    /**

     * 检测是否是需要动态判断的权限
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则表示权限不明
     *

     * @author 朱亚杰  <xcoolcc@gmail.com>

     */

    protected function checkDynamic(){}
    /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     *
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回**true**, 允许任何管理员访问,无需执行节点权限检测
     *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */

    final protected function accessControl(){
        $allow = C('ALLOW_VISIT');
        $deny  = C('DENY_VISIT');
        $check = strtolower(CONTROLLER_NAME.'/'.ACTION_NAME);
        if ( !empty($deny)  && in_array_case($check,$deny) ) {
            return false;//非超管禁止访问deny中的方法
        }
        if ( !empty($allow) && in_array_case($check,$allow) ) {
            return true;
        }
        return null;//需要检测节点权限
    }

    /**
     * 权限检测
     * @param string  $rule    检测的规则
     * @param string  $mode    check模式
     * @return boolean
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */

    final protected function checkRule($rule, $type=AuthRuleModel::RULE_URL, $mode='url'){
        static $Auth    =   null;
        if (!$Auth) {
            $Auth       =   new \Think\Auth();
        }
        if(!$Auth->check($rule,UID,$type,$mode)){
            return false;
        }
        return true;
    }

    /**
     * 设置一条或者多条数据的状态
     * @param $script 严格模式要求处理的纪录的uid等于当前登陆用户UID
     * @author jry <598821125@qq.com>
     */
    public function setStatus($model = CONTROLLER_NAME, $script = false) {
        $ids    = I('request.ids');
        $status = I('request.status');
        if (empty($ids)) {
            $this->error('请选择要操作的数据');
        }
        $model_primary_key = D($model)->getPk();
        $map[$model_primary_key] = array('in',$ids);
        if ($script) {
            $map['uid'] = array('eq', is_login());
        }
        switch ($status) {
            case 'forbid' :  // 禁用条目
                $data = array('status' => 0);
                $this->editRow(
                    $model,
                    $data,
                    $map,
                    array('success'=>'禁用成功','error'=>'禁用失败','action'=>'disable_action','ids'=>is_array($ids)?implode(',',$ids):$ids)
                );
                break;
            case 'resume' :  // 启用条目
                //判断当前绑定手机是否被其他账户使用
                /*$mobile=D('User')->where("status = 1")->field('mobile')->select();
                static $mobileall=array();
                foreach($mobile as $k=>$v){
                    $mobileall=array_merge($mobileall,$v);
                }
                $map['mobile']=array('not in',$mobileall);*/
                $data = array('status' => 1);
                $map  = array_merge(array('status' => 0), $map);
                $this->editRow(
                    $model,
                    $data,
                    $map,
                    array('success'=>'启用成功','error'=>'启用失败','action'=>'enable_action','ids'=>is_array($ids)?implode(',',$ids):$ids)
                );
                break;
            case 'hide' :  // 隐藏条目
                $data = array('status' => 2);
                $map  = array_merge(array('status' => 1), $map);
                $this->editRow(
                    $model,
                    $data,
                    $map,
                    array('success'=>'隐藏成功','error'=>'隐藏失败')
                );
                break;
            case 'show' :  // 显示条目
                $data = array('status' => 1);
                $map  = array_merge(array('status' => 2), $map);
                $this->editRow(
                   $model,
                   $data,
                   $map,
                   array('success'=>'显示成功','error'=>'显示失败')
                );
                break;
            case 'recycle' :  // 移动至回收站
                $data['status'] = -1;
                $this->editRow(
                    $model,
                    $data,
                    $map,
                    array('success'=>'成功移至回收站','error'=>'删除失败','action'=>'recycle_action','ids'=>is_array($ids)?implode(',',$ids):$ids)
                );
                break;
            case 'restore' :  // 从回收站还原
                $data = array('status' => 1);
                $map  = array_merge(array('status' => -1), $map);
                $this->editRow(
                    $model,
                    $data,
                    $map,
                    array('success'=>'恢复成功','error'=>'恢复失败','action'=>'reset_action','ids'=>is_array($ids)?implode(',',$ids):$ids)
                );
                break;
            case 'delete'  :  // 删除条目
                $result = D($model)->where($map)->delete();
                if ($result) {
                    action_log('delete_action',$model,is_array($ids)?implode(',',$ids):$ids,UID);
                    $this->success('删除成功，不可恢复！');
                } else {
                    $this->error('删除失败');
                }
                break;
            default :
                $this->error('参数错误');
                break;
        }
    }

    /**
     * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
     * @param string $model 模型名称,供M函数使用的参数
     * @param array  $data  修改的数据
     * @param array  $map   查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息
     *                       array(
     *                           'success' => '',
     *                           'error'   => '',
     *                           'url'     => '',   // url为跳转页面
     *                           'ajax'    => false //是否ajax(数字则为倒数计时)
     *                       )
     * @author jry <598821125@qq.com>
     */
    final protected function editRow($model, $data, $map, $msg) {
        $id = array_unique((array)I('id',0));
        $id = is_array($id) ? implode(',',$id) : $id;
        //如存在id字段，则加入该条件
        $fields = D($model)->getDbFields();
        if (in_array('id', $fields) && !empty($id)) {
            $where = array_merge(
                array('id' => array('in', $id )),
                (array)$where
            );
        }
        $msg = array_merge(
            array(
                'success' => '操作成功！',
                'error'   => '操作失败！',
                'url'     => ' ',
                'ajax'    => IS_AJAX
            ),
            (array)$msg
        );
        $result = D($model)->where($map)->save($data);
        if ($result != false) {
            action_log($msg['action'],D($model)->getTableName(),$msg['ids'],UID);
            $this->success($msg['success'], $msg['url'], $msg['ajax']);
        } else {
            $this->error($msg['error'], $msg['url'], $msg['ajax']);
        }
    }

    /**
     * 模块配置方法
     * @author jry <598821125@qq.com>
     */
    public function module_config() {
        if (IS_POST) {
            $id     = (int)I('id');
            $config = I('config');
            $flag   = D('Admin/Module')
                    ->where("id={$id}")
                    ->setField('config', json_encode($config));
            if ($flag !== false) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        } else {
            $name = MODULE_NAME;
            $config_file = realpath(APP_PATH.$name).'/'.D('Admin/Module')->install_file();
            if (!$config_file) {
                $this->error('配置文件不存在');
            }
            $module_config = include $config_file;

            $module_info  = D('Admin/Module')->where(array('name' => $name))->find($id);
            $db_config = $module_info['config'];

            // 构造配置
            if ($db_config) {
                $db_config = json_decode($db_config, true);
                foreach ($module_config['config'] as $key => $value) {
                    if ($value['type'] != 'group') {
                        $module_config['config'][$key]['value'] = $db_config[$key];
                    } else {
                        foreach ($value['options'] as $gourp => $options) {
                            foreach ($options['options'] as $gkey => $value) {
                                $module_config['config'][$key]['options'][$gourp]['options'][$gkey]['value'] = $db_config[$gkey];
                            }
                        }
                    }
                }
            }

            // 构造表单名
            foreach ($module_config['config'] as $key => $val) {
                if ($val['type'] == 'group') {
                    foreach ($val['options'] as $key2 => $val2) {
                        foreach ($val2['options'] as $key3 => $val3) {
                            $module_config['config'][$key]['options'][$key2]['options'][$key3]['name'] = 'config['.$key3.']';
                        }
                    }
                } else {
                    $module_config['config'][$key]['name'] = 'config['.$key.']';
                }
            }

            //使用FormBuilder快速建立表单页面。
            $builder = new \Common\Builder\FormBuilder();
            $builder->setMetaTitle('设置')  //设置页面标题
                    ->setPostUrl(U('')) //设置表单提交地址
                    ->addFormItem('id', 'hidden', 'ID', 'ID')
                    ->setExtraItems($module_config['config']) //直接设置表单数据
                    ->setFormData($module_info)
                    ->display();
        }
    }

    /**
     * 初始化后台菜单
     */
    public function initMenu() {
        $Menu = F("Menu");
        if (!$Menu) {
            $Menu=D("Admin/Menu")->menu_cache();
        }
        return $Menu;
    }
        //我靠自己参照的写
    protected function lists($model,$where=array(),$order='',$base = array('status'=>array('egt',0)),$field=true){
          $options    =   array();   //空数组
//          $REQUEST = (array)I('request.');   //它是一个数组  array(6)
//        dump($REQUEST);
          if(is_string($model)){
              $model  =   M($model);   //object(Think\Model)#15 (21)
          }
          $OPT   =   new \ReflectionProperty($model,'options');   //object(ReflectionProperty)#16 (2)
          $OPT->setAccessible(true);            //null
          $pk  =   $model->getPk();   //id
          if($order === null){
               //已经定义了$order等于“”
          }else  if( $order === '' && empty($options['order']) && !empty($pk) ){

          }elseif($order){
              $options['order'] = $order;
          }
          $options['where'] = array_filter(array_merge( (array)$base,(array)$where ),function($val){
                if($val === ''|| $val === null){
                    return false;
                }else{
                    return true;
                }
          });
          if(empty($options['where'])){   //$options['where']等于空的时候进来
                 unset($options['where']);
          }
         $options      =   array_merge( (array)$OPT->getValue($model), $options );  //等于一数组array(1)

         $total        =   $model->where($options['where'])->count();   //符合$options['where']条件的记录

        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 15;

        $page = new \Think\Page($total, $listRows);   //需要修改成Think\Page文件

        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p =$page->show();

        $this->assign('_page', $p? $p: '');

        $this->assign('_total',$total);

        $options['limit'] = $page->firstRow.','.$page->listRows;

        $model->setProperty('options',$options);

        $kao =  $model->field($field)->order('id desc')->select();

        return $kao;
    }





}
