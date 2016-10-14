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
class Document extends \think\Model{

	protected $fk = 'doc_id';
	protected $pk = 'id';

	// 定义需要自动写入时间戳格式的字段
	protected $autoWriteTimestamp = array('create_time','update_time','deadline');

	protected $auto = array('doc_id', 'title', 'description', 'update_time','deadline');
	protected $insert = array('uid', 'attach'=>0, 'view'=>0, 'comment'=>0, 'extend'=>0, 'create_time', 'status');

	protected $type = array(
		'cover_id'  => 'integer',
		'link_id'  => 'integer',
		'level'  => 'integer',
		'comment'  => 'integer',
		'view'  => 'integer',
	);

	protected function setUidAttr(){
		return session('user_auth.uid');
	}

	protected function setDocIdAttr(){
		return input('id','','intval,trim');
	}

	protected function setDeadlineAttr($value){
		return $value ? strtotime($value) : time();
	}

	protected function setCreateTimeAttr($value){
		return $value ? strtotime($value) : time();
	}

	/**
	 * 获取数据状态
	 * @return integer 数据状态
	 * @author huajie <banhuajie@163.com>
	 */
	protected function setStatusAttr($value){
		$cate = input('post.category_id');
		$check = db('Category')->getFieldById($cate, 'check');
		if($check){
			$status = 2;
		}else{
			$status = 1;
		}
		return $status;
	}

	protected function getTagsAttr($value){
		if ($value) {
			return explode(',', $value);
		}
	}

	public function extend($name){
		$name = strtoupper($name);
		$this->join('__DOCUMENT_' . $name . '__', $this->fk . '=' . $this->pk, 'LEFT');
		return $this;
	}

	public function change(){
		/* 获取数据对象 */
		$data = \think\Request::instance()->post();

		if ($data !== false) {
			//增加增加复选框 shu'zu数组保存 
			foreach($data as $key=>$val){
				if(is_array($val)){
					$data[$key] = implode(',', $val);
				}
			}
			/* 添加或新增基础内容 */
			if(empty($data['id'])){ //新增数据
				unset($data['id']);
				$id = $this->validate('document.edit')->insert($data); //添加基础内容

				if(!$id){
					return false;
				}
				$data['id'] = $id;
			} else { //更新数据
				$status = $this->validate('document.edit')->fetchSql(true)->update($data, array('id'=>$data['id'])); //更新基础内容
				dump($status);exit();
				if(false === $status){
					return false;
				}
			}
			return $data['id'];
		}else{
			return false;
		}
	}

	public function del($map){
		return $this->where($map)->delete();
	}

	public function detail($id){
		$data = $this->get($id);
		$map = array('model_id'=>$data['model_id'], 'type'=>array('in', 'checkbox'));
		$model_type = db('attribute')->where($map)->column('name');
		foreach($model_type as $val){
			$data->setAttr($val, explode(',', $data[$val]));
		}

		return $data;
	}

	public function recom($id, $field = '*', $limit = 10, $order = 'id desc'){
		$tag = $this->where(array('id'=>$id))->value('tags');
		$map = '';
		if ($tag) {
			$tags = explode(',', $tag);
			foreach ($tags as $item) {
				$where[] = 'tags LIKE "%' . $item . '%"';
			}
			$map = implode(' OR ', $where);
		}
		$list = $this->where($map)->field($field)->limit($limit)->order($order)->select();
		if (empty($list)) {
			return $list;
		}else{
			return $this->field($field)->limit($limit)->order($order)->select();
		}
	}
}