<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeController extends Controller {

	/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/index');
	}


    protected function _initialize(){

		// 系统开关
		if (!C('TOGGLE_WEB_SITE')) {
			$this->error('站点已经关闭，请稍后访问~');
		}
		// 获取当前用户ID
		define('UID',is_login());
    }

	/* 用户登录检测 */
	protected function login(){
		/* 用户登录检测 */
		is_login() || $this->error('您还没有登录，请先登录！', U('User/Home/User/login'));
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

}
