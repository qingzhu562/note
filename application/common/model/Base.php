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
* 模型基类
*/
class Base extends \think\Model{
	
	public function scopeList($query, $map, $field = '*', $limit = 10, $order = 'id desc'){
		$query->field($field)->where($map)->limit($limit)->order($order);
	}
	
	public function scopeWhere($query, $map){
		$query->where($map);
	}
}