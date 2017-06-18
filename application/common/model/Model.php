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
class Model extends Base {

	protected $auto   = ['update_time'];
	protected $insert = ['name', 'create_time', 'status' => 0];
	protected $type   = array(
		'id'             => 'integer',
		'create_time'    => 'integer',
		'update_time'    => 'integer',
	);

	protected static function init(){
		self::beforeUpdate(function($event){
			$data = $event->toArray();
			$attribute_sort = json_decode($data['attribute_sort'], true);
			if (!empty($attribute_sort)) {
				foreach ($attribute_sort as $key => $value) {
					db('Attribute')->where('id', 'IN', $value)->setField('group_id', $key);
					foreach ($value as $k => $v) {
						db('Attribute')->where('id', $v)->setField('sort', $k);
					}
				}
			}
			return true;
		});
	}

	protected function setAttributeSortAttr($value){
		return $value ? json_encode($value) : '';
	}

	public function setNameAttr($value) {
		return strtolower($value);
	}

	public function getStatusTextAttr($value, $data) {
		$status = array(
			0 => '禁用',
			1 => '启用',
		);
		return $status[$data['status']];
	}

	public function del() {
		$id        = input('id', '', 'trim,intval');
		$tablename = $this->where('id', $id)->value('name');

		//删除数据表
		$db = new \com\Datatable();
		if ($db->CheckTable($tablename)) {
			//检测表是否存在
			$result = $db->del_table($tablename)->query();
			if (!$result) {
				return false;
				$this->error = "数据表删除失败！";
			}
		}
		$result = $this->where('id', $id)->delete();
		if ($result) {
			return true;
		} else {
			$this->error = "模型删除失败！";
			return false;
		}
	}

	public function attribute() {
		return $this->hasMany('Attribute');
	}

	/**
	 * 解析字段
	 * @param  [array] $model      [字段]
	 * @return [array]             [解析后的字段]
	 */
	public function preFields($model) {
		$fields     = $model->attribute;
		$groups     = parse_config_attr($model['field_group']);
		$field_sort = json_decode($model['field_sort'], true);

		//获得数组的第一条数组
		$first_key = array_keys($groups);
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

		foreach ($groups as $key => $value) {
			if ($groupfield[$key]) {
				$data[$value] = $groupfield[$key];
			}
		}
		return $data;
		return array();
	}
}