<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\admin\controller;
use app\common\controller\Admin;

class Form extends Admin {

	//自定义表单
	public function index(){
		$list = array();

		$data = array(
			'list'   => $list,
			//'page'   => $list->render()
		);
		$this->setMeta('自定义表单');
		$this->assign($data);
		return $this->fetch();
	}

	/**
	 * 添加表单
	 */
	public function add(){
		if (IS_POST) {
			# code...
		}else{
			return $this->fetch('public/edit');
		}
	}

	/**
	 * 编辑表单
	 */
	public function edit(){
		if (IS_POST) {
			# code...
		}else{
			return $this->fetch('public/edit');
		}
	}

	/**
	 * 删除表单
	 */
	public function del(){
		$id = $this->getArrayParam('id');
		$result = false;
		if (false !== $result) {
			return $this->success('删除成功！');
		}else{
			return $this->error('删除失败！');
		}
	}
}