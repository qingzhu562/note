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

class Seo extends Admin{

	protected $model;
	protected $keyList;

	public function _initialize(){
		parent::_initialize();
		$app=array(''=>'-所有模块-','index'=>'前台模块','user'=>'用户中心');
		$this->keyList = array(
			array('name'=>'id','title'=>'标识','type'=>'hidden'),
			array('name'=>'title','title'=>'规则名称','type'=>'text','option'=>'','help'=>'规则名称，方便记忆'),
			array('name'=>'app','title'=>'模块名','type'=>'select','option'=>$app,'help'=>'不选表示所有模块'),
			array('name'=>'controller','title'=>'控制器','type'=>'text','option'=>'','help'=>'不填表示所有控制器'),
			array('name'=>'act','title'=>'方法','type'=>'text','option'=>'','help'=>'不填表示所有方法'),
			array('name'=>'seo_title','title'=>'SEO标题','type'=>'text','option'=>'','help'=>'不填表示使用默认'),
			array('name'=>'seo_keywords','title'=>'SEO关键字','type'=>'text','option'=>'','help'=>'不填表示使用默认'),
			array('name'=>'seo_description','title'=>'SEO描述','type'=>'text','option'=>'','help'=>'不填表示使用默认'),
			array('name'=>'status', 'title'=>'状态', 'type'=>'select','option'=>array('0'=>'禁用','1'=>'启用'),'help'=>''),
			array('name'=>'sort','title'=>'排序','type'=>'text','option'=>'','help'=>'')
		);
	}

	public function index($page = 1, $r = 20){
		//读取规则列表
		$map = array('status' => array('EGT', 0));

		$list = model('SeoRule')->where($map)->order('sort asc')->paginate(10);

		$data = array(
			'list'  => $list,
			'page'  => $list->render(),
		);
		$this->assign($data);
		$this->setMeta("规则列表");
		return $this->fetch();
	}

	public function add(){
		if (IS_POST) {
			$data = $this->request->post();
			if ($data) {
				$result = model('SeoRule')->save($data);
				if ($result) {
					return $this->success("添加成功！");
				}else{
					return $this->error("添加失败！");
				}
			}else{
				return $this->error($this->model->getError());
			}
		}else{
			$data = array(
				'keyList' => $this->keyList
			);
			$this->assign($data);
			$this->setMeta("添加规则");
			return $this->fetch('public/edit');
		}
	}

	public function edit($id = null){
		if (IS_POST) {
			$data = $this->request->post();
			if ($data) {
				$result = model('SeoRule')->save($data,array('id'=>$data['id']));
				if (false !== $result) {
					return $this->success("修改成功！");
				}else{
					return $this->error("修改失败！");
				}
			}else{
				return $this->error($this->model->getError());
			}
		}else{
			$id = input('id','','trim,intval');
			$info = db('SeoRule')->where(array('id'=>$id))->find();
			$data = array(
				'info'  => $info,
				'keyList' => $this->keyList
			);
			$this->assign($data);
			$this->setMeta("编辑规则");
			return $this->fetch('public/edit');
		}
	}

	public function del(){
		$id = input('id','','trim,intval');
		if (!$id) {
			return $this->error("非法操作！");
		}
		$result = db('SeoRule')->where(array('id'=>$id))->delete();
		if ($result) {
			return $this->success("删除成功！");
		}else{
			return $this->error("删除失败！");
		}
	}
}