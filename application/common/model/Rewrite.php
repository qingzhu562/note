<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
 * 伪静态
 */
class Rewrite extends Base{
	
	public $keyList = array(
		array('name'=>'id','title'=>'标识','type'=>'hidden'),
		array('name'=>'title','title'=>'规则名称','type'=>'text','option'=>'','help'=>'规则名称，方便记忆'),
	);
}