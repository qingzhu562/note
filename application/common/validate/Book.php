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
class Book extends \think\Validate{

	protected $rule = array(
		'book_name' =>  'require',
		'stand_class' =>  'require',
	);

	protected $message = array(
		'book_name'         =>  '书名称必须！',
		'stand_class' =>  '图书标准类型必须！',
	);

	protected $scene = array(
		'add'   => array('book_name', 'stand_class'),
		'edit'  => array('book_name', 'stand_class')
	);

}