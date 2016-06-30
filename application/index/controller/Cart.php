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

class Cart extends Fornt{

	public function _initialize(){
		$this->cart = new \com\Cart();
	}

	//购物车
	public function index(){
		$list = $this->cart->all();

		$data = array(
			'list'   => $list,
		);
		$this->assign($data);
		return $this->fetch();
	}

	//加入购物车
	public function add($id, $num){
		if ($num > 0) {
			$this->cart->addItem($id, $num);
		}
		$info['code'] = 1;
		$info['num'] = $this->cart->getCount();
		return $info;
	}

	//加入购物车
	public function del($id){
		$this->cart->delItem($id);
		$info['code'] = 1;
		$info['data'] = array('num'=>$this->cart->getCount());
		return $info;
	}

	//购物车统计
	public function count(){
		$data = array('info'=>$this->cart->getCount());
		echo json_encode($data);
	}
}