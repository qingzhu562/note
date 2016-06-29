<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

if(version_compare(PHP_VERSION,'5.4.0','<'))  die('require PHP > 5.4.0 !');

// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');

/**
 * 项目定义
 * 扩展类库目录
 */
define('BASE_PATH', substr($_SERVER['SCRIPT_NAME'], 0, -10));
define('ROOT_PATH', dirname(APP_PATH) . DIRECTORY_SEPARATOR);
define('EXTEND_PATH', ROOT_PATH . 'core' . DIRECTORY_SEPARATOR . 'extend' . DIRECTORY_SEPARATOR);
define('VENDOR_PATH', ROOT_PATH . 'core' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR);

//邮件
define('ICONV_ENABLED', TRUE);

/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define ( 'RUNTIME_PATH', __DIR__ . '/data/' );

// 加载框架引导文件
require __DIR__ . '/core/start.php';