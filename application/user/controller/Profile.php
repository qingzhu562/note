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

class Profile extends User{

	//修改资料
	public function index(){
		$user = model('User');
		if (IS_POST) {
			$result = $user->change();
			if ($result !== false) {
				return $this->success("更新成功！", "");
			}else{
				return $this->error($user->getError(), '');
			}
		}else{
			$group['基础资料'] = $user->useredit;

			$info = $user->getInfo(session('user_auth.uid'));
			$data = array(
				'fieldGroup' => $group,
				'info'       => $info
			);
			$this->assign($data);
			return $this->fetch('public/edit');
		}
	}

	//修改密码
	public function editpw(){
		$user = model('User');
		if (IS_POST) {
			$result = $user->editpw();
			if ($result !== false) {
				return $this->success("更新成功！", "");
			}else{
				return $this->error($user->getError(), '');
			}
		}else{
			return $this->fetch();
		}
	}

	//修改头像
	public function avatar(){
		return $this->fetch();
	}
}
