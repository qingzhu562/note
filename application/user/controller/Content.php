<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\user\controller;
use app\common\controller\User;

class Content extends User {

	public function _initialize() {
		parent::_initialize();
		$this->getContentMenu();
		$model_id = $this->request->param('model_id');
		$row      = db('Model')->select();
		foreach ($row as $key => $value) {
			$list[$value['id']] = $value;
		}

		if (empty($list[$model_id])) {
			return $this->error("无此模型！");
		} else {
			$this->modelInfo = $list[$model_id];
			if ($this->modelInfo['extend'] > 1) {
				$this->model = model($this->modelInfo['name']);
			} else {
				$this->model = model('Document')->extend($this->modelInfo['name']);
			}
		}

		$this->assign('model_id', $model_id);
		$this->assign('model_list', $list);
		$this->assign('model_info', $this->modelInfo);
	}

	public function index() {
		if ($this->modelInfo['list_grid'] == '') {
			return $this->error("列表定义不正确！", url('admin/model/edit', array('id' => $this->modelInfo['id'])));
		}
		$grid_list  = get_grid_list($this->modelInfo['list_grid']);
		$order      = "id desc";
		$map['uid'] = session('user_auth.uid');
		if ($this->modelInfo['extend'] == 1) {
			$map['model_id'] = $this->modelInfo['id'];
		}

		$field = array_filter($grid_list['fields']);
		$list  = $this->model->where($map)->field($field)->order($order)->paginate(15);

		$data = array(
			'grid' => $grid_list,
			'list' => $list,
			'page' => $list->render(),
		);
		$this->assign($data);
		return $this->fetch();
	}

	/**
	 * 内容添加
	 * @author molong <ycgpp@126.com>
	 */
	public function add() {
		if (IS_POST) {
			$result = $this->model->change();
			if ($result) {
				return $this->success("添加成功！", url('admin/content/index', array('model_id' => $this->modelInfo['id'])));
			} else {
				return $this->error($this->model->getError(), '');
			}
		} else {
			$info = array(
				'model_id' => $this->modelInfo['id'],
			);
			$data = array(
				'info'       => $info,
				'fieldGroup' => $this->getField($this->modelInfo),
			);
			$this->assign($data);
			return $this->fetch('public/edit');
		}
	}

	/**
	 * 内容修改
	 * @author molong <ycgpp@126.com>
	 */
	public function edit($id) {
		if (IS_POST) {
			$result = $this->model->change();
			if ($result !== false) {
				return $this->success("更新成功！", url('admin/content/index', array('model_id' => $this->modelInfo['id'])));
			} else {
				return $this->error($this->model->getError(), '');
			}
		} else {
			if (!$id) {
				return $this->error("非法操作！");
			}
			$info = $this->model->detail($id);
			if (!$info) {
				return $this->error($this->model->getError());
			}
			$info['model_id'] = $this->modelInfo['id'];
			$data             = array(
				'info'       => $info,
				'fieldGroup' => $this->getField($this->modelInfo),
			);
			$this->assign($data);
			return $this->fetch('public/edit');
		}
	}

	/**
	 * 内容删除
	 * @author molong <ycgpp@126.com>
	 */
	public function del() {
		$id  = input('get.id', '', 'trim');
		$ids = input('post.ids/a', array());
		array_push($ids, $id);
		if (empty($ids)) {
			return $this->error("非法操作！");
		}

		$map['id'] = array('IN', $ids);
		$result    = $this->model->del($map);

		if ($result) {
			return $this->success("删除成功！");
		} else {
			return $this->error("删除失败！", '', "");
		}
	}

	protected function getField() {
		$field_group = parse_config_attr($this->modelInfo['field_group']);
		$field_sort  = json_decode($this->modelInfo['field_sort'], true);

		if ($this->modelInfo['extend'] > 1) {
			$map['model_id'] = $this->modelInfo['id'];
		} else {
			$model_id[]      = $this->modelInfo['id'];
			$model_id[]      = 1;
			$map['model_id'] = array('IN', $model_id);
		}
		if (ACTION_NAME == 'add') {
			$map['is_show'] = array('in', array('1', '2'));
		} elseif (ACTION_NAME == 'edit') {
			$map['is_show'] = array('in', array('1', '3'));
		}

		//获得数组的第一条数组
		$first_key = array_keys($field_group);
		$fields    = model('Attribute')->getFieldlist($map);
		if (!empty($field_sort)) {
			foreach ($field_sort as $key => $value) {
				foreach ($value as $index) {
					if (isset($fields[$index])) {
						$groupfield[$key][] = $fields[$index];
						unset($fields[$index]);
					}
				}
			}
		}
		//未进行排序的放入第一组中
		$fields[] = array('name' => 'model_id', 'type' => 'hidden'); //加入模型ID值
		$fields[] = array('name' => 'id', 'type' => 'hidden'); //加入模型ID值
		foreach ($fields as $key => $value) {
			$groupfield[$first_key[0]][] = $value;
		}

		foreach ($field_group as $key => $value) {
			if ($groupfield[$key]) {
				$data[$value] = $groupfield[$key];
			}
		}
		return $data;
	}
}
