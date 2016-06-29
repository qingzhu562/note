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
class Document extends \think\model\Merge{

	protected $table = "sent_document";
	protected $fk = 'doc_id';

	// 定义需要自动写入时间戳格式的字段
	protected $autoTimeField = array('create_time','update_time','deadline');

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
		if (is_numeric($name)) {
			$name = db('model')->where(array('id'=>$name))->value('name');
		}
		self::$relationModel = array('document_' . $name);
		return $this;
	}

	public function change(){
		/* 获取数据对象 */
		$data = input('post.');

		if ($data !== false) {
			/* 添加或新增基础内容 */
			if(empty($data['id'])){ //新增数据
				unset($data['id']);
				$id = $this->save($data); //添加基础内容

				if(!$id){
					$this->error = '添加基础内容出错！';
					return false;
				}
				$data['id'] = $id;
			} else { //更新数据
				$status = $this->save($data, array('id'=>$data['id'])); //更新基础内容
				if(false === $status){
					$this->error = '更新基础内容出错！';
					return false;
				}
			}
			return $data['id'];
		}else{
			return false;
		}
	}

	public function del($map){
		return $this->db()->where($map)->delete();
	}

	public function detail($id){
		$data = $this->get($id);

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