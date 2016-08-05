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
* 设置模型
*/
class Content extends Base{

	//protected $name = "page";

	protected $auto = array("update_time");
	protected $insert = array("create_time");

	protected $type = array(
		'id'  => 'integer',
		'cover_id'  => 'integer',
	);

	protected function setUidAttr(){
		return session('user_auth.uid');
	}

	protected function setCreateTimeAttr($value){
		return $value ? strtotime($value) : time();
	}

	protected function setUpdateTimeAttr($value){
		return $value ? strtotime($value) : time();
	}

	public function extend($name){
		$this->name = $name;
		return $this;
	}

	public function detail($id){
		$map['id'] = $id;
		$this->data = $this->db()->where($map)->find();
		return $this->data;
	}

	public function change(){
		$data = input('post.');
		if ($data['id']) {
			$result = $this->save($data,array('id'=>$data['id']));
		}else{
			$result = $this->save($data);
		}
		return $result;
	}

	public function del($map){
		return $this->where($map)->delete();
	}
}