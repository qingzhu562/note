<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace com;

class Pay{

	/**
	 * 支付驱动实例
	 * @var Object
	 */
	private $payer;

	/**
	 * 配置参数
	 * @var type 
	 */
	private $config;

	public function __construct($driver) {

		//获取配置
		$this->getConfig($driver);
		/* 配置 */
		$pos = strrpos($driver, '\\');
		$pos = $pos === false ? 0 : $pos + 1;
		$apitype = strtolower(substr($driver, $pos));
		$this->config['notify_url'] = url('user/pay/notify',array('apitype'=>$apitype), false, true);
		$this->config['return_url'] = url("user/pay/returnback", array('apitype' => $apitype, 'method' => 'return'), false, true);

		$this->config['pey_type']  = $driver;

		/* 设置支付驱动 */
		$class = strpos($driver, '\\') ? $driver : 'com\\pay\\driver\\' . ucfirst(strtolower($driver));
		$this->setDriver($class, $this->config);
	}

	/**
	* 支付
	* @return boolean
	*/
	public function pay(pay\Input $input){
		return $this->payer->pay($input);
	}

	/**
	* 获取配置
	* @return array
	*/
	public function getConfig($driver){
		$config = \think\Config::load(APP_PATH . 'pay.php');
		$this->config = $config[$driver];
	}

	/**
	 * 设置支付驱动
	 * @param string $class 驱动类名称
	 */
	private function setDriver($class, $config) {
		$this->payer = new $class($config);
		if (!$this->payer) {
			throw new \think\Exception("不存在支付驱动：{$class}");
		}
	}

	public function __call($method, $arguments) {
		if (method_exists($this, $method)) {
			return call_user_func_array(array(&$this, $method), $arguments);
		} elseif (!empty($this->payer) && $this->payer instanceof Pay\Pay && method_exists($this->payer, $method)) {
			return call_user_func_array(array(&$this->payer, $method), $arguments);
		}
	}

}