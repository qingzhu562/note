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

class Model extends Admin {

	public function _initialize() {
		parent::_initialize();
		$this->getContentMenu();
		$this->model = model('Model');
	}

	/**
	 * 模型管理首页
	 * @author huajie <banhuajie@163.com>
	 */
	public function index() {
		$map = array('status' => array('gt', -1));

		$order = "id desc";
		$list  = $this->model->where($map)->order($order)->paginate(10);

		$data = array(
			'list' => $list,
			'page' => $list->render(),
		);

		// 记录当前列表页的cookie
		Cookie('__forward__', $_SERVER['REQUEST_URI']);

		$this->assign($data);
		$this->setMeta('模型管理');
		return $this->fetch();
	}

	/**
	 * 新增页面初始化
	 * @author huajie <banhuajie@163.com>
	 */
	public function add(\think\Request $request) {
		if (IS_POST) {
			$result = $this->model->validate('Model.add')->save($request->post());
			if (false !== $result) {
				$this->success('创建成功！', url('admin/model/index'));
			}else{
				return $this->error($this->model->getError());
			}
		}else{
			$this->setMeta('新增模型');
			return $this->fetch();
		}
	}

	/**
	 * 编辑页面初始化
	 * @author molong <molong@tensent.cn>
	 */
	public function edit(\think\Request $request) {
		if (IS_POST) {
			$result = $this->model->validate('Model.edit')->save($request->post(), array('id'=>$request->post('id')));
			if (false !== $result) {
				$this->success('更新成功！', url('admin/model/index'));
			}else{
				return $this->error($this->model->getError());
			}
		}else{
			$info = $this->model->where('id', $request->param('id'))->find();

			//获取字段列表
			if ($info['attribute_list']) {
				$fields = model('Attribute')->where('id', 'IN', $info['attribute_list'])->where('is_show', 1)->select();
				// 梳理属性的可见性
				foreach ($fields as $key => $field) {
					$field['group']           = -1;
					$field['sort']            = 0;
					$fields_tem[$field['id']] = $field->toArray();    //数据对象转换为数组
				}

				// 获取模型排序字段
				$field_sort = json_decode($info['attribute_sort'], true);//dump($field_sort);exit();
				if (!empty($field_sort)) {
					foreach ($field_sort as $group => $ids) {
						foreach ($ids as $key => $value) {
							if (!empty($fields_tem[$value])) {
								$fields_tem[$value]['group'] = $group;
								$fields_tem[$value]['sort']  = $key;
							}
						}
					}
				}
				
				if (isset($fields_tem) && $fields_tem) {
					// 模型字段列表排序
					$fields = list_sort_by($fields_tem, "sort");
				}
			}else{
				$fields = array();
			}
			$data = array(
				'info'   => $info,
				'fields' => $fields
			);
			$this->assign($data);
			$this->setMeta('编辑模型');
			return $this->fetch();
		}
	}

	/**
	 * 删除一条数据
	 * @author huajie <banhuajie@163.com>
	 */
	public function del() {
		$result = $this->model->del();
		if ($result) {
			return $this->success('删除模型成功！');
		} else {
			return $this->error($this->mdoel->getError());
		}
	}

	/**
	 * 更新一条数据
	 * @author huajie <banhuajie@163.com>
	 */
	public function update() {
		$res = \think\Loader::model('Model')->change();
		if ($res['status']) {
			return $this->success($res['info'], url('index'));
		} else {
			return $this->error($res['info']);
		}
	}

	/**
	 * 更新数据
	 * @author colin <colin@tensent.cn>
	 */
	public function status(\think\Request $request) {
		$map['id'] = $request->param('id');

		$data['status'] = $request->param('status');

		if (null == $map['id'] || null == $data['status']) {
			return $this->error('参数不正确！');
		}

		$model = $this->model->where($map)->find();
		if ($model['table_status'] != 1 && $data['status'] == 1) {
			return $this->error('数据表未创建或更新');
		}
		if ($model['list_grid'] == '' && $data['status'] == 1) {
			return $this->error('模型列表未定义');
		}
		$result = $this->model->where($map)->update($data);
		if (false !== $result) {
			return $this->success('状态设置成功！');
		}else{
			return $this->error($this->model->getError());
		}
	}
}