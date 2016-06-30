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

class Order extends User{

	public function _initialize(){
		parent::_initialize();
		$this->order = model('Order');
		$this->cart = new \com\Cart();
	}

	//会员中心订单首页
	public function index(){
		$map['uid'] = session('user_auth.uid');
		$map['status'] = array('gt',0);

		$rows = array();
		$list = $this->order->where($map)->order('id desc')->paginate(10);
		foreach ($list as $key => $value) {
			$value['product'] = $value->product;
			$rows[] = $value;
		}

		$data = array(
			'list'   => $rows,
			'page'   => $list->render()
		);
		$this->assign($data);
		return $this->fetch();
	}

	//订单结算列表
	public function lists(){
		$id = input('id', '', 'trim');
		$order_id = input('order_id', '', 'trim');
		
		//立即购买，把物品放入购物车
		if ($id) {
			$this->cart->addItem($id, 1);
		}
		
		if (!$order_id) {
			$price_count = '0.00';
			$list = $this->cart->all();
			foreach ($list as $key => $value) {
				$price_count += $value['price_count'];
			}

			$data = array(
				'list'   => $list,
				'price_count' => $price_count
			);

			//生成订单
			$map['id'] = $this->order->createOrder($data);
		}else{
			$map['id'] = $order_id;
		}
		$order = $this->order->where($map)->find();

		$data = array(
			'list' => $order->product,
			'order_id' => $map['id'],
			'price_count' => $order['price_count']
		);
		//订单生成后情况购物车
		$this->cart->clear();
		$this->assign($data);
		return $this->fetch();
	}

	public function confirm(){
		$order_id = input('order_id', '', 'trim');
		$payment = input('payment', '', 'trim');
		$map['id'] = $order_id;
		$this->order->where($map)->update(array('pay_type'=>$payment));
		$info = $this->order->where($map)->find();

		$data = array(
			'info'    => $info
		);
		$this->assign($data);
		return $this->fetch();
	}

	/**
	 * 订单支付
	 * @param  int $order_id
	 * @return html
	 */
	public function pay($order_id){
		$info = $this->order->where(array('id'=>$order_id))->find();
		if ($info['pay_status']) {
			return $this->success("您已支付！", url('user/order/index'));
		}
		$pay = array(
			'body'     => '测试支付',
			'fee'      => $info['price_count'],
			//'fee'      => 0.01,
			'order_no' => $info['order_no'],
			'title'    => config('web_site_title') . ' - 订单付款',
			'param'    => array('order_id'=>$info['id'])
		);
		$data = array('type'  => $info['pay_type'], 'pay_data'=>$pay);
		$data = action('user/pay/index',$data);
		$data['order_no'] = $info['order_no'];
		$this->assign($data);
		return $this->fetch();
	}

	public function cancel($order_id){
		$result = $this->order->where(array('id'=>$order_id))->setField('status',0);
		if ($result) {
			return $this->success("已取消！");
		}else{
			return $this->error("取消失败！");
		}
	}
}