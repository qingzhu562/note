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

class Pay extends User{

	public function index($type = 'alipay', $pay_data){
		$pay = new \com\Pay($type);
		$pay_data['bank'] = isset($pay_data['bank']) ? $pay_data['bank'] : '';
		$pay_data['callback'] = url('user/pay/returnback',array('apitype'=>input('apitype', 'alipay', 'trim')));
		$pay_data['url'] = url('user/pay/returnback',array('apitype'=>input('apitype', 'alipay', 'trim')));
		$pay_data['param'] = isset($pay_data['param']) ? $pay_data['param'] : '';

		$input = new \com\pay\Input();
		$input->setBody($pay_data['body'])
			->setUid(session('user_auth.uid'))
			->setFee($pay_data['fee']) //支付金额
			->setOrderNo($pay_data['order_no'])//订单号
			->setTitle($pay_data['title'])//设置商品名称
			->setBank($pay_data['bank'])//设置银行
			->setCallback($pay_data['callback'])/*** 设置支付完成后的后续操作接口 */
			->setUrl($pay_data['url']) /* 设置支付完成后的跳转地址*/
			->setParam($pay_data['param']);
		$data = $pay->pay($input);
		return $data;
	}

	//返回
	public function returnback(){
		$apitype = input('get.apitype');
		$method = input('get.method');
		if (!empty($_POST)) {
			$result = input('post.');
		}elseif (!empty($_GET)) {
			$result = input('get.');;
			unset($result['method']);
			unset($result['apitype']);
		} else {
			exit('Access Denied');
		}
		$pay = new \com\Pay($apitype);
		$res = $pay->verifyNotify($result);
		$status = ($result['trade_status'] == 'TRADE_FINISHED' || $result['trade_status'] == 'TRADE_SUCCESS') ? true : false;
		if ($res && $status) {
			db('Order')->where(array('order_no'=>$result['out_trade_no']))->update(array('pay_status'=>1));
			return $this->success("支付成功！", url('user/order/index'));
		}else{
			return $this->error("支付失败！");
		}
	}

	//异步获取
	public function notify(){
		$order_no = input('post.order_no');
		$pay_status = db('Order')->where(array('order_no'=>$order_no))->value('pay_status');
		if ($pay_status) {
			return $this->success("支付成功！", url('user/order/index'));
		}else{
			return $this->error("支付失败！");
		}
	}
}