<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\api\controller;

use think\Request;

class Index extends Base {

	public $apiAuth = true;
	// 允许访问的请求类型
	protected $restMethodList = 'get|post|';

	/**
	 * get的响应
	 * @param Request $request
	 * @return mixed
	 */
	public function getResponse(Request $request) {
		return $this->sendError(1001, 'THIS IS GET', 400);
	}

	/**
	 * post的响应
	 * @param Request $request
	 * @return mixed
	 */
	public function postResponse(Request $request) {
		return $this->sendSuccess('THIS IS POST');
	}
}
