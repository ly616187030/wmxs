<?php

/* * 
 * 菜单
 */
namespace Admin\Model;
use Think\Model;
class MenuModel extends Model {

    protected $tableName = 'menu';
    //自动验证
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('name', 'require', '菜单名称不能为空！',self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('app', 'require', '应用不能为空！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('model', 'require', '模块名称不能为空！', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('action', 'require', '方法名称不能为空！',self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('app,model,action', '', '同样的记录已经存在！', 1, 'unique', self:: MODEL_BOTH),
        array('parentid', 'checkParentid', '菜单只支持四级！', 1, 'callback', 1),
    );
    //自动完成
    protected $_auto = array(
            //array(填充字段,填充内容,填充条件,附加规则)
    );


    public function authMenus($arrname=array()){
        $map['url'] = array('in',$arrname);
        $list = $this->where($map)->select();
        return $list;
    }
    //验证菜单是否超出三级
    public function checkParentid($parentid) {
        $find = $this->where(array("id" => $parentid))->getField("parentid");
        if ($find) {
            $find2 = $this->where(array("id" => $find))->getField("parentid");
            if ($find2) {
                $find3 = $this->where(array("id" => $find2))->getField("parentid");
                if ($find3) {
                    return false;
                }
            }
        }
        return true;
    }

    //验证action是否重复添加
    public function checkAction($data) {
        //检查是否重复添加
        $pk =   $this->getPk();
        if(!empty($data[$pk]) && is_string($pk)) { // 完善编辑的时候验证唯一
            $data[$pk] = array('neq',$data[$pk]);
        }
        if($this->where($data)->find()) return false;
        return true;
    }
    //验证action是否重复添加
    public function checkActionUpdate($data) {
    	//检查是否重复添加
    	$id=$data['id'];
    	unset($data['id']);
    	$find = $this->field('id')->where($data)->find();
    	if (isset($find['id']) && $find['id']!=$id) {
    		return false;
    	}
    	return true;
    }
    

    /**
     * 按父ID查找菜单子项
     * @param integer $parentid   父菜单ID  
     * @param integer $with_self  是否包括他自己
     */
    public function admin_menu($parentid, $with_self = false) {
        //父节点ID
        $parentid = (int) $parentid;
        $result = $this->where(array('parentid' => $parentid, 'status' => 1))->order(array("listorder" => "ASC"))->select();
        if ($with_self) {
            $result2[] = $this->where(array('id' => $parentid))->find();
            $result = array_merge($result2, $result);
        }
        //权限检查
        if (sp_get_current_admin_id() == 1) {
            //如果是超级管理员 直接通过
            return $result;
        } 
        
         $array = array();
        foreach ($result as $v) {
        	
            //方法
            $action = $v['action'];
            
            //public开头的通过
            if (preg_match('/^public_/', $action)) {
                $array[] = $v;
            } else {
            	
                if (preg_match('/^ajax_([a-z]+)_/', $action, $_match)){
                	
                	$action = $_match[1];
                }
                   
                $rule_name=strtolower($v['app']."/".$v['model']."/".$action);
                
                if ( sp_auth_check(sp_get_current_admin_id(),$rule_name)){
                	$array[] = $v;
                }
                   
            }
        } 
        
        return $array;
    }

    /**
     * 获取菜单 头部菜单导航
     * @param $parentid 菜单id
     */
    public function submenu($parentid = '', $big_menu = false) {
        $array = $this->admin_menu($parentid, 1);
        $numbers = count($array);
        if ($numbers == 1 && !$big_menu) {
            return '';
        }
        return $array;
    }

    /**
     * 菜单树状结构集合
     */
    public function menu_json() {
        $data = $this->get_tree(0);
        return $data;
    }

    //取得树形结构的菜单
    public function get_tree($myid, $parent = "", $Level = 1) {
        $data = $this->admin_menu($myid);
        $Level++;
        if (is_array($data)) {
            $ret = NULL;
            foreach ($data as $a) {
                $id = $a['id'];
                $name = ucwords($a['app']);
                $model = ucwords($a['model']);
                $action = $a['action'];
                //附带参数
              	$fu = "";
                if ($a['data']) {
                    $fu = "?" . htmlspecialchars_decode($a['data']);
                }
                $array = array(
                    "icon" => $a['icon'],
                    "id" => $id . $name,
                    "name" => $a['name'],
                    "parent" => $parent,
                    "url" => U("{$name}/{$model}/{$action}{$fu}", array("menuid" => $id)),
                    'lang'=> strtoupper($name.'_'.$model.'_'.$action)
                ); 
                
                
                
                $ret[$id . $name] = $array;
                $child = $this->get_tree($a['id'], $id, $Level);
                //由于后台管理界面只支持三层，超出的不层级的不显示
                if ($child && $Level <= 3) {
                    $ret[$id . $name]['items'] = $child;
                }
               
            }
            return $ret;
        }
       
        return false;
    }

    /**
     * 更新缓存
     * @param type $data
     * @return type
     */
    public function menu_cache($data = null) {
        if (empty($data)) {
            //$data = $this->select();
            $data =$this->getMenus();
            F("Menu".substr(md5(UID),10), $data);
        } else {
            F("Menu".substr(md5(UID),10), $data);
        }
        return $data;
    }

    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    public function getMenus($controller=CONTROLLER_NAME){
        //$menus  =   session('ADMIN_MENU_LIST.'.$controller);
        if(empty($menus)){
            // 获取主菜单
            $mdb = D('Admin/Menu');
            if(is_admin()){
                $menus = $mdb
                    ->alias('m')
                    ->join('__AUTH_RULE__ AS r ON r.name=m.url')
                    ->field('m.*,r.id AS rule_id')
                    ->order('m.listorder ASC')->select();
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
                    $map['url'] = array('in',$authList);
                    $menus = $mdb->where($map)->order('listorder ASC')->select();
                }

            }

            //$tree = new Tree();
            //$menus = $tree->list_to_tree($menus,'id','parentid');
            //session('ADMIN_MENU_LIST.'.$controller,$menus);
        }

        return $menus;

    }
    /**
     * 后台有更新/编辑则删除缓存
     * @param type $data
     */
    public function _before_write(&$data) {
        parent::_before_write($data);
        F("Menu", NULL);
    }

    //删除操作时删除缓存
    public function _after_delete($data, $options) {
        parent::_after_delete($data, $options);
        $this->_before_write($data);
    }
    
    public function menu($parentid, $with_self = false){
    	//父节点ID
    	$parentid = (int) $parentid;
    	$result = $this->where(array('parentid' => $parentid))->select();
    	if ($with_self) {
    		$result2[] = $this->where(array('id' => $parentid))->find();
    		$result = array_merge($result2, $result);
    	}
    	return $result;
    }
    /**
     * 得到某父级菜单所有子菜单，包括自己
     * @param number $parentid 
     */
    public function get_menu_tree($parentid=0){
    	$menus=$this->where(array("parentid"=>$parentid))->order(array("listorder"=>"ASC"))->select();
    	
    	if($menus){
    		foreach ($menus as $key=>$menu){
    			$children=$this->get_menu_tree($menu['id']);
    			if(!empty($children)){
    				$menus[$key]['children']=$children;
    			}
    			unset($menus[$key]['id']);
    			unset($menus[$key]['parentid']);
    		}
    		return $menus;
    	}else{
    		return $menus;
    	}
    	
    }

}