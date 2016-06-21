<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
 * 分类模型
 */
class Order extends Base{

	protected $auto = array('update_time');
	protected $insert = array('create_time', 'uid', 'status'=>1, 'order_no');

	protected function setOrderNoAttr($value){
		return 'SN'.date('YmdHis',time()).rand('1000','9999');
	}

	protected function setUidAttr($value){
		return session('user_auth.uid');
	}

	protected function getPayTypeTextAttr($value, $data){
		$type = array('wechat'=>'微信支付', 'alipay'=>'支付宝支付');
		return $type[$data['pay_type']];
	}
	protected function getPayStatusTextAttr($value, $data){
		$type = array(
			0   => '未付款',
			1   => '已付款',
		);
		return $type[$data['pay_status']];
	}

	public function product(){
		return $this->hasMany('OrderProduct', 'order_no', 'order_no');
	}

	//生成订单
	public function createOrder($data){
		$order_id = $this->save($data);
		foreach ($data['list'] as $key => $value) {
			$value['order_no'] = $this->data['order_no'];
			$value['product_id'] = $value['id'];
			unset($value['id']);
			$value['create_time'] = time();
			$value['update_time'] = time();
			$goods[] = $value;
		}
		db('OrderProduct')->insertAll($goods);
		return $order_id;
	}
}