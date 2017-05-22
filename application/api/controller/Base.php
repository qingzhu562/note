<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\api\controller;
use api\BaseRest;

class Base extends BaseRest {

	// 允许访问的请求类型
	protected $restMethodList = 'get|post|put|delete|patch|head|options';
	//业务错误码的映射表
	public $errMap = [
		0    => 'success', //没有错误
		1001 => '参数错误',
		9999 => '自定义错误', //让程序给出的自定义错误
	];

	//是否开启权限认证
	public $apiAuth = true;
}