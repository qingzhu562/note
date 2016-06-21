<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\common\controller;

class Upload {

	public function upload(){
		$upload_type = input('get.filename','images','trim');
		$config = $this->$upload_type();
		$upload = new \org\Upload($config, $config['driver']);
		$info = $upload->upload($_FILES);
		if (false !== $info) {
			$fileinfo = $this->save($config, $upload_type, $info['file']);
            $return['info'] = $fileinfo;
		}else{
            $return['status'] = 0;
            $return['info']   = $upload->getError();
		}
		
		echo json_encode($return);
	}

	/**
	 * 图片上传
	 * @var view
	 * @access public
	 */
	protected function images(){
		return config('picture_upload');
	}

	/**
	 * 文件上传
	 * @var view
	 * @access public
	 */
	protected function attachment(){
		return config('attachment_upload');
	}

	/**
	 * 百度编辑器使用
	 * @var view
	 * @access public
	 */
	public function ueditor(){
		$data = new \com\Ueditor(session('auth_user.uid'));
		echo $data->output();
	}

	public function delete(){
		$data = array(
			'status' => 1,
		);
		echo json_encode($data);exit();
	}

	/**
	 * 保存上传的信息到数据库
	 * @var view
	 * @access public
	 */
	public function save($config, $type, $file){
		$file['status'] = 1;
		if ($type == 'images') {
			$dbname = 'picture';
			$file['path'] = substr($config['rootPath'], 1).$file['savepath'].$file['savename'];	//
		}else{
			$dbname = 'file';
			$file['url'] = substr($config['rootPath'], 1).$file['savepath'].$file['savename'];
		}
		$data = db($dbname)->where(array('md5'=>$file['md5']))->find();
		if (!empty($data)) {
			return $data;
		}else{
			$id = db($dbname)->insertGetId($file);
		}
		
		if ($id) {
			$data = db($dbname)->where(array('id'=>$id))->find();
			return $data;
		}else{
			return false;
		}
	}

	/**
	 * 下载本地文件
	 * @param  array    $file     文件信息数组
	 * @param  callable $callback 下载回调函数，一般用于增加下载次数
	 * @param  string   $args     回调函数参数
	 * @return boolean            下载失败返回false
	 */
	public function downLocalFile($file, $callback = null, $args = null){
		if(is_file($file['rootpath'].$file['savepath'].$file['savename'])){
			/* 调用回调函数新增下载数 */
			is_callable($callback) && call_user_func($callback, $args);

			/* 执行下载 */ //TODO: 大文件断点续传
			header("Content-Description: File Transfer");
			header('Content-type: ' . $file['type']);
			header('Content-Length:' . $file['size']);
			if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
				header('Content-Disposition: attachment; filename="' . rawurlencode($file['name']) . '"');
			} else {
				header('Content-Disposition: attachment; filename="' . $file['name'] . '"');
			}
			readfile($file['rootpath'].$file['savepath'].$file['savename']);
			exit;
		} else {
			$this->error = '文件已被删除！';
			return false;
		}
	}
}