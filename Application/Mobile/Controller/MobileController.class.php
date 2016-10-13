<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Mobile\Controller;
use Common\Controller\CommonController;
/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用模块名
 * @author jry <598821125@qq.com>
 */
class MobileController extends CommonController {
	/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/index');
	}
	/**
	 * 初始化方法
	 * @author jry <598821125@qq.com>
	 */
	public function _initialize() {
		// 系统开关
		if (!C('TOGGLE_WEB_SITE')) {
			$this->error('站点已经关闭，请稍后访问~');
		}
		define('UID',is_login());
		// 获取所有模块配置的用户导航
		$mod_con['status'] = 1;
		$_user_nav_main = array();
		$_user_nav_list = D('Admin/Module')->where($mod_con)->getField('user_nav', true);
		foreach ($_user_nav_list as $key => $val) {
			if ($val) {
				$val = json_decode($val, true);
				if ($val['main']) {
					$_user_nav_main = array_merge($_user_nav_main, $val['main']);
				}
			}
		}

		// 监听行为扩展
		\Think\Hook::listen('corethink_behavior');


		$this->assign('meta_keywords', C('WEB_SITE_KEYWORD'));
		$this->assign('meta_description', C('WEB_SITE_DESCRIPTION'));
		$this->assign('_new_message', cookie('_new_message')); // 获取用户未读消息数量
		$this->assign('_user_auth', cookie('user_auth')?unserialize(cookie('user_auth')):(session('user_auth')?session('user_auth'):null));     // 用户登录信息
		$this->assign('_user_nav_main', $_user_nav_main);      // 用户导航信息
	}

	/**
	 * 用户登录检测
	 * @author jry <598821125@qq.com>
	 */
	protected function is_login() {
		//用户登录检测
		$uid = is_member_login();
		if ($uid) {
			return $uid;
		} else {
			if (IS_AJAX) {
				$return['status']  = 0;
				$return['info']    = '请先登录系统';
				$return['login'] = 1;
				$this->ajaxReturn($return);
			} else {
				redirect(U('Index/getFollow', null, true, true));
			}
		}
	}

	/**
	 * 设置一条或者多条数据的状态
	 * @param $script 严格模式要求处理的纪录的uid等于当前登陆用户UID
	 * @author jry <598821125@qq.com>
	 */
	public function setStatus($model = CONTROLLER_NAME, $script = true) {
		$ids    = I('request.ids');
		$status = I('request.status');
		if (empty($ids)) {
			$this->error('请选择要操作的数据');
		}
		$model_primary_key = D($model)->getPk();
		$map[$model_primary_key] = array('in',$ids);
		if ($script) {
			$map['uid'] = array('eq', is_member_login());
		}
		switch ($status) {
			case 'forbid' :  // 禁用条目
				$data = array('status' => 0);
				$this->editRow(
					$model,
					$data,
					$map,
					array('success'=>'禁用成功','error'=>'禁用失败')
				);
				break;
			case 'resume' :  // 启用条目
				$data = array('status' => 1);
				$map  = array_merge(array('status' => 0), $map);
				$this->editRow(
					$model,
					$data,
					$map,
					array('success'=>'启用成功','error'=>'启用失败')
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
					array('success'=>'成功移至回收站','error'=>'删除失败')
				);
				break;
			case 'restore' :  // 从回收站还原
				$data = array('status' => 1);
				$map  = array_merge(array('status' => -1), $map);
				$this->editRow(
					$model,
					$data,
					$map,
					array('success'=>'恢复成功','error'=>'恢复失败')
				);
				break;
			case 'delete'  :  // 删除条目
				$result = D($model)->where($map)->delete();
				if ($result) {
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
			$this->success($msg['success'], $msg['url'], $msg['ajax']);
		} else {
			$this->error($msg['error'], $msg['url'], $msg['ajax']);
		}
	}

	/**
	 * 模板显示 调用内置的模板引擎显示方法，
	 * @access protected
	 * @param string $templateFile 指定要调用的模板文件
	 * 默认为空 由系统自动定位模板文件
	 * @param string $charset 输出编码
	 * @param string $contentType 输出类型
	 * @param string $content 输出内容
	 * @param string $prefix 模板缓存前缀
	 * @return void
	 */
	protected function display($template='',$charset='',$contentType='',$content='',$prefix='') {
		$depr     = C('TMPL_FILE_DEPR');
		$template = str_replace(':', $depr, $template);
		if (C('CURRENT_THEME')) {
			// 分析模板文件规则
			$controller_name = explode('/', CONTROLLER_NAME);
			if ('' == $template) {
				// 如果模板文件名为空 按照默认规则定位
				if (sizeof($controller_name) === 2) {
					$template = $controller_name[1] . $depr . ACTION_NAME;
				} else {
					$template = $controller_name[0] . $depr . ACTION_NAME;
				}
			} else if (false === strpos($template, $depr)) { // 没有/
				$template = CONTROLLER_NAME . $depr . $template;
				if (sizeof($controller_name) === 2) {
					$template = $controller_name[1] . $depr . $template;
				} else {
					$template = $controller_name[0] . $depr . $template;
				}
			}

			// WAP版
			if (C('IS_WAP')) {
				$file = './Theme/'.C('CURRENT_THEME').$depr.MODULE_NAME.$depr.'wap'.$depr.$template.C('TMPL_TEMPLATE_SUFFIX');
			} else {
				$file = './Theme/'.C('CURRENT_THEME').$depr.MODULE_NAME.$depr.$template.C('TMPL_TEMPLATE_SUFFIX');
			}

			if (is_file($file)) {
				$template = $file;
			}
		} else {
			// WAP版
			if (C('IS_WAP')) {
				$controller_name = explode('/', CONTROLLER_NAME);
				if (sizeof($controller_name) === 2) {
					if ('' == $template) {
						// 如果模板文件名为空 按照默认规则定位
						$template = $controller_name[0] . $depr . 'Wap' . $depr . $controller_name[1] . $depr . ACTION_NAME;
					} else if (false === strpos($template, $depr)) {
						$template = $controller_name[0] . $depr . 'Wap' . $depr . $controller_name[1] . $depr . $template;
					}
				} else {
					if ('' == $template) {
						// 如果模板文件名为空 按照默认规则定位
						$template = 'Wap' . $depr . $controller_name[0] . $depr . ACTION_NAME;
					} else if (false === strpos($template, $depr)) { // 没有/
						$template = 'Wap' . $depr . $controller_name[0] . $depr . $template;
					}
				}
			}
		}
		$this->view->display($template,$charset,$contentType,$content,$prefix);
	}
	public function channels(){
		if(IS_POST){
			$rules = array(
				array('name','require','姓名不能为空'),
				array('phone','require','联系方式不能为空！'),
				//array('phone','^[0-9]\d*$','联系方式格式不正确！'),
				array('phone','unique','联系方式已存在！'),
				array('address','require','联系地址不能为空！'),
			);
			$channels=M('channels');
			if($channels->validate($rules)->create()){
				$channels->create_time=time();
				if($channels->add()){
					$this->success('申请已提交，请耐心等待客服联系！',U('Mobile/Index/getFollow/',array('companyid'=>$_SESSION['companyid'])));
				}else{
					$this->success('申请已提交失败，请稍后重试！',U('Mobile/channels/channels'));
				};
			}else{
				$this->error($channels->getError());
			}
		}
	}
}
