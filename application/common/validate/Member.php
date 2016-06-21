<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\common\validate;

/**
* 设置模型
*/
class Member extends \think\Validate{

	protected $rule = array(
		'username'   => 'require|unique:member|/^[a-zA-Z]\w{0,39}$/',
	);
	protected $message = array(
		'username.require'    => '用户名必须',
		'username.unique'    => '用户名已存在',
	);
	protected $scene = array();

}