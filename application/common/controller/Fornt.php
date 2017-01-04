<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\common\controller;

class Fornt extends Base {

	public function _initialize() {
		parent::_initialize();

		//判读是否为关闭网站
		if (\think\Config::get('web_site_close')) {
			header("Content-type:text/html;charset=utf-8");
			echo $this->fetch('common@default/public/close');exit();
		}

		//设置SEO
		$this->setSeo();

		$this->setHoverNav();

		//主题设置
		$this->setThemes();
	}

	//当前栏目导航
	protected function setHoverNav() {
		//dump($_SERVER['PHP_SELF']);
	}

	protected function setThemes() {
		//网站主题设置
		$themes['mobile'] = config('mobile_themes') ? config('mobile_themes') : 'mobile';
		$themes['pc']     = config('pc_themes') ? config('pc_themes') : 'default';
		$view_path        = ($this->isMobile() && config('open_mobile_site') == '1') ? 'template/' . $themes['mobile'] . '/' : 'template/' . $themes['pc'] . '/';
		$module = $this->request->module();
		if (!in_array($module, array('index', 'install'))) {
			$view_path_pre = $module . '/';
		} else {
			$view_path_pre = '';
		}
		$this->view->config('view_path', $view_path . $view_path_pre)
			->config('tpl_replace_string',array(
				'__IMG__' => BASE_PATH . '/' . $view_path . 'static/images',
				'__JS__'  => BASE_PATH . '/' . $view_path . 'static/js',
				'__CSS__' => BASE_PATH . '/' . $view_path . 'static/css',
			));
	}
}
