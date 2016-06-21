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

class User extends Admin{
	
	/**
	 * 用户管理首页
	 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
	 */
	public function index() {
		$nickname = input('nickname');
		$map['status'] = array('egt', 0);
		if (is_numeric($nickname)) {
			$map['uid|nickname'] = array(intval($nickname), array('like', '%' . $nickname . '%'), '_multi' => true);
		} else {
			$map['nickname'] = array('like', '%' . (string)$nickname . '%');
		}

		$order = "uid desc";
		$list = model('User')->where($map)->order($order)->paginate(15);

		$data = array(
			'list'  => $list,
			'page'  => $list->render(),
		);
		$this->assign($data);
		$this->setMeta('用户信息');
		return $this->fetch();
	}

	/**
	 * create
	 * @author colin <colin@tensent.cn>
	 */
	public function add(){
		$model = \think\Loader::model('User');
		if(IS_POST){
			$username = input('post.username', '', 'trim');
			$email = input('post.email', '', 'trim');
			$password = input('post.password', '', 'trim');
			$repassword = input('post.repassword', '', 'trim');
			//创建注册用户
			$uid = $model->register($username, $password, $repassword, false);

			if(0 < $uid){
				$userinfo = array('nickname' => $username, 'email' => $email, 'status' => 1,'reg_time'=>time(),'last_login_time'=>time(),'last_login_ip'=>get_client_ip(1));
				/*保存信息*/
				if(!db('Member')->where(array('uid'=>$uid))->update($userinfo)){
					return $this->error('用户添加失败！');
				} else {
					return $this->success('用户添加成功！',url('index'));
				}
			}else{
				return $this->error($this->showRegError($uid));
			}
		}else{
			$data = array(
				'keyList' => $model->addfield
			);
			$this->assign($data);
			$this->setMeta("添加用户");
			return $this->fetch('public/edit');
		}
	}
	
	/**
	 * 修改昵称初始化
	 * @author huajie <banhuajie@163.com>
	 */
	public function edit() {
		$model = model('User');
		if(IS_POST){
			$data = $this->request->post();
			if(!$data){
				return $this->error($this->showRegError($model->getError()));
			}

			//为空
			if(empty($data['password'])){
				unset($data['password']);
				unset($data['salt']);
				$model->save($data);
			}else{
				$data['salt'] = rand_string();
				$data['password'] = md5($password.$data['salt']);
				//不为空
				$model->save($data,array('uid'=>$data['uid']));
			}
			
			if ($reuslt) {
				return $this->success('修改成功！',url('index'));
			}else{
				return $this->error('修改失败！', '');
			}
		}else{
			$info = $this->getUserinfo();

			$data = array(
				'info'  => $info,
				'keyList' => $model->editfield
			);
			$this->assign($data);
			$this->setMeta("编辑用户");
			return $this->fetch('public/edit');
		}
	}

	/**
	 * del
	 * @author colin <colin@tensent.cn>
	 */
	public function del(){
		$ids = input('post.ids');
		//多条删除和单条删除
		empty($ids) ? $ids = input('get.id') : $ids = $ids;
		$uid = array('IN',is_array($ids) ? implode(',',$ids) : $ids);
		//获取用户信息
		$find = $this->getUserinfo($uid);
		model('User')->where(array('uid'=>$uid))->delete();
		return $this->success('删除用户成功！');
	}


	public function auth(){
		$access = model('AuthGroupAccess');
		$group = model('AuthGroup');
		if (IS_POST) {
			$uid = input('uid','','trim,intval');
			$access->where(array('uid'=>$uid))->delete();
			$group_type = config('user_group_type');
			foreach ($group_type as $key => $value) {
				$group_id = input($key,'','trim,intval');
				if ($group_id) {
					$add = array(
						'uid' => $uid,
						'group_id' => $group_id,
					);
					$access->save($add);
				}
			}
			return $this->success("设置成功！");
		}else{
			$uid = input('id','','trim,intval');
			$row = $group::select();
			$auth = $access::where(array('uid'=>$uid))->select();

			$auth_list = array();
			foreach ($auth as $key => $value) {
				$auth_list[] = $value['group_id'];
			}
			foreach ($row as $key => $value) {
				$list[$value['module']][] = $value;
			}
			$data = array(
				'uid'   => $uid,
				'auth_list' => $auth_list,
				'list' => $list
			);
			$this->assign($data);
			$this->setMeta("用户分组");
			return $this->fetch();
		}
	}

	/**
	 * 获取某个用户的信息
	 * @var uid 针对状态和删除启用
	 * @var pass 是查询password
	 * @var errormasg 错误提示
	 * @author colin <colin@tensent.cn>
	 */
	private function getUserinfo($uid = null,$pass = null,$errormsg = null){
		$user = model('User');
		$uid = $uid ? $uid : input('get.id');
		//如果无UID则修改当前用户
		$uid = $uid ? $uid : session('user_auth.uid');
		$map['uid'] = $uid;
		if($pass != null ){
			unset($map);
			$map['password'] = $pass;
		}
		$list = $user::where($map)->field('uid,username,nickname,sex,email,qq,score,signature,status,salt')->find();
		if(!$list){
			return $this->error($errormsg ? $errormsg : '不存在此用户！');
		}
		return $list;
	}
	
	/**
	 * 修改昵称提交
	 * @author huajie <banhuajie@163.com>
	 */
	public function submitNickname() {
		
		//获取参数
		$nickname = input('post.nickname');
		$password = input('post.password');
		if (empty($nickname)) {
			return $this->error('请输入昵称');
		}
		if (empty($password)) {
			return $this->error('请输入密码');
		}
		
		//密码验证
		$User = new UserApi();
		$uid = $User->login(UID, $password, 4);
		if ($uid == -2) {
			return $this->error('密码不正确');
		}
		
		$Member = model('User');
		$data = $Member->create(array('nickname' => $nickname));
		if (!$data) {
			return $this->error($Member->getError());
		}
		
		$res = $Member->where(array('uid' => $uid))->save($data);
		
		if ($res) {
			$user = session('user_auth');
			$user['username'] = $data['nickname'];
			session('user_auth', $user);
			session('user_auth_sign', data_auth_sign($user));
			return $this->success('修改昵称成功！');
		} 
		else {
			return $this->error('修改昵称失败！');
		}
	}
	
	/**
	 * 修改密码初始化
	 * @author huajie <banhuajie@163.com>
	 */
	public function editpwd() {
		if (IS_POST) {
			$user = \think\Loader::model('User');
			//获取参数
			$password = input('post.old');
			if(empty($password)){
				return $this->error('请输入原密码','');
			}
			$data['password'] = input('post.password');
			if (empty($data['password'])) {
				return $this->error('请输入新密码','');
			}
			$repassword = input('post.repassword');
			if (empty($repassword)) {
				return $this->error('请输入确认密码','');
			}
			
			if ($data['password'] !== $repassword) {
				return $this->error('您输入的新密码与确认密码不一致','');
			}
			$res = $user->updateUserFields(UID, $password, $data);
			if ($res) {
				return $this->success('修改密码成功！');
			}else {
				return $this->error($user->getError());
			}
		}else{
			$this->setMeta('修改密码');
			return $this->fetch();
		}
	}
		
	/**
	 * 会员状态修改
	 * @author 朱亚杰 <zhuyajie@topthink.net>
	 */
	public function changeStatus($method = null) {
		$id = array_unique((array)input('id', 0));
		if (in_array(config('user_administrator'), $id)) {
			return $this->error("不允许对超级管理员执行该操作!");
		}
		$id = is_array($id) ? implode(',', $id) : $id;
		if (empty($id)) {
			return $this->error('请选择要操作的数据!');
		}
		$map['uid'] = array('in', $id);
		switch (strtolower($method)) {
			case 'forbiduser':
				$this->forbid('Member', $map);
				break;

			case 'resumeuser':
				$this->resume('Member', $map);
				break;

			case 'deleteuser':
				$this->delete('Member', $map);
				break;

			default:
				return $this->error('参数非法');
		}
	}
	
	/**
	 * 获取用户注册错误信息
	 * @param  integer $code 错误编码
	 * @return string        错误信息
	 */
	private function showRegError($code = 0) {
		switch ($code) {
			case -1:
				$error = '用户名长度必须在16个字符以内！';
				break;

			case -2:
				$error = '用户名被禁止注册！';
				break;

			case -3:
				$error = '用户名被占用！';
				break;

			case -4:
				$error = '密码长度必须在6-30个字符之间！';
				break;

			case -5:
				$error = '邮箱格式不正确！';
				break;

			case -6:
				$error = '邮箱长度必须在1-32个字符之间！';
				break;

			case -7:
				$error = '邮箱被禁止注册！';
				break;

			case -8:
				$error = '邮箱被占用！';
				break;

			case -9:
				$error = '手机格式不正确！';
				break;

			case -10:
				$error = '手机被禁止注册！';
				break;

			case -11:
				$error = '手机号被占用！';
				break;

			default:
				$error = '未知错误';
		}
		return $error;
	}
}