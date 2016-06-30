<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\index\controller;
use app\common\controller\Fornt;

class Book extends Fornt{

	public function _initialize(){
		parent::_initialize();
		$this->book = model('Book');
	}

	public function index(){
		$map = $this->search();
		$order = 'id desc';
		$list = $this->book->where($map)->order($order)->paginate(10);

		$data = array(
			'list'   => $list,
			'page'   => $list->render()
		);
		$this->assign($data);
		return $this->fetch();
	}

	//图书详情
	public function detail($id = 0){
		$map['id'] = $id;

		$info = $this->book->where($map)->find();

		$data = array(
			'info'   => $info,
		);
		$this->assign($data);
		return $this->fetch();
	}

	//图书搜索条件组织
	protected function search(){
		$map = array();
		$data = $this->request->post();
		$data['is_show_height'] = 0;
		if (isset($data['book_name']) && $data['book_name']) {
			$map['book_name'] = array("LIKE", "%" . $data['book_name'] . "%");
		}
		if (isset($data['book_no']) && $data['book_no']) {
			$map['book_no'] = array("LIKE", "%" . $data['book_no'] . "%");
			$data['is_show_height'] = 1;
		}
		if (isset($data['stand_class']) && $data['stand_class']) {
			$map['stand_class'] = $data['stand_class'];
			$data['is_show_height'] = 1;
		}
		if (isset($data['create_time'][0]) && isset($data['create_time'][1]) && $data['create_time'][0] && $data['create_time'][1]) {
			$map['create_time'] = array('BETWEEN',$data['create_time']);
			$data['is_show_height'] = 1;
		}

		$this->assign($data);
		return $map;
	}
}