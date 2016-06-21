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
 * 分类模型
 */
class Book extends Content{

	protected $auto = array('date_last_modify', 'user_last_modify');
	protected $type = array(
		'id'   => 'integer',
		'cover_id'   => 'integer',
		'sort'   => 'integer'
	);

	protected function setStatusAttr($value){
		return 1;
	}

	protected function setCreateTimeAttr($value){
		return $value ? strtotime($value) : time();
	}

	protected function setDateLastModifyAttr($value){
		return $value ? strtotime($value) : time();
	}

	protected function setUserLastModifyAttr($value){
		return session('user_auth.uid');
	}

	protected function getCreateTimeAttr($value){
		return date('Y-m-d H:i:s', $value);
	}

	protected function getDateLastModifyAttr($value){
		return date('Y-m-d H:i:s', $value);
	}

	protected function getStandClassTextAttr($value, $data){
		$text = array(
			'1'  => 'BK ', '2'  => 'CN', '3'  => 'QT', '4'  => 'DB',
		);
		return $text[$data['stand_class']];
	}
}