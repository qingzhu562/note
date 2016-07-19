<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
 * 用户组模型类
 * Class AuthGroupModel
 * @author molong <molong@tensent.cn>
 */
class Addons extends \app\common\model\Base {

    protected $auto = array('status');
    protected $insert = array('create_time');

    protected function setStatusAttr($value){
        return 1;
    }

    protected function setIsinstallAttr($value){
        return 0;
    }

    protected function getStatusTextAttr($value, $data){
        return $data['status'] ? "启用" : "禁用";
    }

    protected function getUninstallAttr($value, $data){
        return 0;
    }

    /**
     * 更新插件列表
     * @param string $addon_dir
     */
    public function refresh($addon_dir = ''){
        if(!$addon_dir){
            $addon_dir = SENT_ADDON_PATH;
        }
        $dirs = array_map('basename',glob($addon_dir.'*', GLOB_ONLYDIR));
        if($dirs === FALSE || !file_exists($addon_dir)){
            $this->error = '插件目录不可读或者不存在';
            return FALSE;
        }
		$where['name']	=	array('in',$dirs);
		$addons			=	$this->where($where)->select();

        foreach ($dirs as $value) {
            $value = ucfirst($value);
            if(!isset($addons[$value])){
				$class = get_addon_class($value);
				if(!class_exists($class)){
                    // 实例化插件失败忽略执行
					\think\Log::record('插件'.$value.'的入口文件不存在！');
					continue;
				}
                $obj    =   new $class;
				$addons[$value]	= $obj->info;
				if($addons[$value]){
                    $addons[$value]['id'] = 0;
                    $addons[$value]['uninstall'] = 1;
                    unset($addons[$value]['status']);
				}
			}
        }
    }

    /**
     * 获取插件的后台列表
     */
    public function getAdminList(){
        $admin = array();
        $db_addons = db('Addons')->where("status=1 AND has_adminlist=1")->field('title,name')->select();
        if($db_addons){
            foreach ($db_addons as $value) {
                $admin[] = array('title'=>$value['title'],'url'=>"Addons/adminList?name={$value['name']}");
            }
        }
        return $admin;
    }

    public function install($data){
        if ($data) {
            $info = $this->where('name', $data['name'])->find();
            if (null == $info) {
                $result = $this->save($data);
                if ($result) {
                    return model('Hooks')->addHooks($data['name']);
                }else{
                    return false;
                }
            }else{
                $this->error = "已安装！";
                return false;
            }
        }else{
            return false;
        }
    }

    public function uninstall($id){
        $info = $this->get($id);
        if (!$info) {
            $this->error = "无此插件！";
            return false;
        }
        $class = get_addon_class($info['name']);
        if (class_exists($class)) {
            //插件卸载方法
            $addons  =   new $class;
            if (!method_exists($addons,'uninstall')) {
                $this->error = "插件卸载方法！";
                return false;
            }
            $result = $addons->uninstall();
            if ($result) {
                //卸载挂载点中的插件
                $result = model('Hooks')->removeHooks($info['name']);
                //删除插件表中数据
                $this->where(array('id'=>$id))->delete();
                return true;
            }else{
                $this->error = "无法卸载插件！";
                return false;
            }
        }
    }

    public function build(){

    }
}