<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
* 用户模型
*/
class SeoRule extends Base{

	protected function getAppAttr($value){
		return $value ? $value : '*';
	}

	protected function getControllerAttr($value){
		return $value ? $value : '*';
	}

	protected function getActionAttr($value){
		return (isset($value) && $value) ? $value : '*';
	}

	protected function getRuleNameAttr($value, $data){
		return $data['app'].'/'.$data['controller'].'/'.$data['action'];
	}

	public function getMetaOfCurrentPage($seo){
		$request = \think\Request::instance();
		$result = $this->getMeta($request->module(), $request->controller(), $request->action(), $seo);
		return $result;
	}

	private function getMeta($module, $controller, $action, $seo){
		//查询缓存，如果已有，则直接返回
		$cacheKey = "sent_seo_meta_{$module}_{$controller}_{$action}";
		$cache = cache($cacheKey);
		if($cache !== false) {
			return $cache;
		}

		//获取相关的规则
		$rules = $this->getRelatedRules($module, $controller, $action);

		//按照排序计算最终结果
		$title = '';
		$keywords = '';
		$description = '';

		$need_seo = 1;
		foreach ($rules as $e) {
			//如果存在完全匹配的seo配置，则不用程序设置的seo资料
			if ($e['app'] && $e['controller'] && $e['action']) {
				$need_seo = 0;
			}
			if (!$title && $e['seo_title']) {
				$title = $e['seo_title'];
			}
			if (!$keywords && $e['seo_keywords']) {
				$keywords = $e['seo_keywords'];
			}
			if (!$description && $e['seo_description']) {
				$description = $e['seo_description'];
			}
		}
		if ($need_seo) { //默认让全站的seo规则优先级小于$this->setTitle等方式设置的规则。
			if ($seo['title']) {
				$title = $seo['title'];
			}
			if ($seo['keywords']) {
				$keywords = $seo['keywords'];
			}
			if ($seo['description']) {
				$description = $seo['description'];
			}
		}
		//生成结果
		$result = array('title' => $title, 'keywords' => $keywords, 'description' => $description);

		//写入缓存
		cache($cacheKey, $result);

		//返回结果
		return $result;
    }

	private function getRelatedRules($module, $controller, $action){
		//查询与当前页面相关的SEO规则
		$map = array();
		$map = "(app='' or app='$module') and (controller='' or controller='$controller') and (action='' or action='$action') and status=1";

		$rules = $this->db()->where($map)->order('sort asc')->select();

		//返回规则列表
		return $rules;
	}
}