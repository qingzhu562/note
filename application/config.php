<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

return array(

	// 调试模式
	'app_debug' => true,

	'data_auth_key'    => 'sent',

	'base_url'             => BASE_PATH,
	'url_route_on'         => true,

	'template'             => array(
		'taglib_build_in'  => 'cx,com\Sent'
	),

	// 'dispatch_success_tmpl'  => APP_PATH . 'common/view/default/jump.html',
	// 'dispatch_error_tmpl'    => APP_PATH . 'common/view/default/jump.html',

	'attachment_upload'    => array(
		// 允许上传的文件MiMe类型
		'mimes'        => [],
		// 上传的文件大小限制 (0-不做限制)
		'maxSize'      => 0,
		// 允许上传的文件后缀
		'exts'         => [],
		// 子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
		'subName'      => ['date', 'Ymd'],
		//保存根路径
		'rootPath'     => './uploads/attachment/',
		// 保存路径
		'savePath'     => '',
		// 上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
		'saveName'     => ['uniqid', ''],
		// 文件上传驱动e,
		'driver'       => 'Local',
	),

	'editor_upload'    => array(
		// 允许上传的文件MiMe类型
		'mimes'        => [],
		// 上传的文件大小限制 (0-不做限制)
		'maxSize'      => 0,
		// 允许上传的文件后缀
		'exts'         => [],
		// 子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
		'subName'      => ['date', 'Ymd'],
		//保存根路径
		'rootPath'     => './uploads/editor/',
		// 保存路径
		'savePath'     => '',
		// 上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
		'saveName'     => ['uniqid', ''],
		// 文件上传驱动e,
		'driver'       => 'Local',
	),

	'picture_upload'    => array(
		// 允许上传的文件MiMe类型
		'mimes'        => [],
		// 上传的文件大小限制 (0-不做限制)
		'maxSize'      => 0,
		// 允许上传的文件后缀
		'exts'         => [],
		// 子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
		'subName'      => ['date', 'Ymd'],
		//保存根路径
		'rootPath'     => './uploads/picture/',
		// 保存路径
		'savePath'     => '',
		// 上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
		'saveName'     => ['uniqid', ''],
		// 文件上传驱动e,
		'driver'       => 'Local',
	),
	'session'  => array(
		'prefix'         => 'sent',
		'type'           => '',
		'auto_start'     => true,
	),

	'log'                => array(
		// 日志记录方式，支持 file socket trace sae
		'type' => 'file',
		// 日志保存目录
		'path' => LOG_PATH,
	)
);