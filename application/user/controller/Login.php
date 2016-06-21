<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\user\controller;
use app\common\controller\Fornt;

class Login extends Fornt{

	public function index($username = '', $password = '', $verify = ''){
		if (IS_POST) {
			if (!$username || !$password) {
				return $this->error('用户名或者密码不能为空！','');
			}
			//验证码验证
			$this->checkVerify($verify);
			$user = model('User');
			$uid = $user->login($username,$password);
			if ($uid > 0) {
				$url = session('http_referer') ? session('http_referer') : url('user/index/index');
				return $this->success('登录成功！', $url);
			}else{
				switch($uid) {
					case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
					case -2: $error = '密码错误！'; break;
					default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
				}
				return $this->error($error,'');
			}
		}else{
			session('http_referer', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
			if (is_login()) {
				return $this->redirect('user/index/index');
			}else{
				return $this->fetch();
			}
		}
	}

	public function logout(){
		model('User')->logout();
		return $this->redirect('index/index/index');
	}

	public function register(){
		if (IS_POST) {
			$user = model('User');
			$username = input('username', '', 'trim');
			$password = input('password', '', 'trim');
			$repassword = input('repassword', '', 'trim');
			$verify = input('verify', '', 'trim');
			
			//验证码验证
			$this->checkVerify($verify);

			if ($username == '' || $password == '' || $repassword == '') {
				return $this->error("请填写完整注册信息！", '');
			}
			$result = $user->register($username, $password, $repassword);
			if ($result) {
				return $this->success('注册成功！', url('user/index/index'));
			}else{
				return $this->error($user->getError(), '');
			}
		}else{
			return $this->fetch();
		}
	}
}
