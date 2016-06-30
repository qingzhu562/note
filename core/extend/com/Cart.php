<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace com;

class Cart{

	private $goods = 'book';
	private $items = array();

	public function __construct(){
		$this->items = session('sent_cart') ? session('sent_cart') : array();
	}

	/**
	 * 添加购物车
	 * @param integer $id  商品ID标识
	 * @param integer $num  添加的数量
	 */
	public function addItem($id,$num=1) {
		if ($this->hasItem($id)) {
			$this->incNum($id,$num);
			return;
		}
		$this->items[$id] = $num;
		session('sent_cart', $this->items);
	}

	/**
	 * 判断是否有某个商品
	 * @param  integer   $id  商品ID标识
	 * @return boolean
	 */
	protected function hasItem($id) {
		return isset($this->items[$id]);
	}

	/**
	 * 删除商品
	 * @param  integer   $id  商品ID标识
	 */
	public function delItem($id) {
		unset($this->items[$id]);
		session('sent_cart', $this->items);
	}

	public function modNum($id,$num=1) {
		if (!$this->hasItem($id)) {
			return false;
		}
		$this->items[$id] = $num;
		session('sent_cart', $this->items);
	}

	public function incNum($id,$num=1) {
		if ($this->hasItem($id)) {
			$this->items[$id] += $num;
		}
		session('sent_cart', $this->items);
	}

	public function decNum($id,$num=1) {
		if ($this->hasItem($id)) {
			$this->items[$id] -= $num;
		}
		if ($this->items[$id] <1) {
			$this->delItem($id);
		}
		session('sent_cart', $this->items);
	}

	public function getCount() {
		return count($this->items);
	}

	public function getNum(){
		if ($this->getCount() == 0) {
			return 0;
		}

		$sum = 0;
		foreach ($this->items as $item) {
			$sum += $item;
		}
		return $sum;
	}

	public function all(){
		$list = array();
		foreach ($this->items as $key => $value) {
			$goods = \think\Db::name($this->goods)->where(array('id'=>$key))->find();
			$list[] = array(
				'id'          => $goods['id'],
				'name'        => $goods['book_name'],
				'price'       => $goods['price'],
				'cover_id'    => $goods['cover_id'],
				'price_count' => $value*$goods['price'],
				'num'         => $value
			);
		}
		return $list;
	}

	public function clear() {
		$this->items = array();
		session('sent_cart', $this->items);
	}
}