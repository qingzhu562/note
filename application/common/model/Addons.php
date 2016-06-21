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

    protected function setStatusAttr(){
        return 1;
    }

    /**
     * 获取插件列表
     * @param string $addon_dir
     */
    public function getList($addon_dir = ''){
        if(!$addon_dir){
            $addon_dir = SENT_ADDON_PATH;
        }
        $dirs = array_map('basename',glob($addon_dir.'*', GLOB_ONLYDIR));
        if($dirs === FALSE || !file_exists($addon_dir)){
            $this->error = '插件目录不可读或者不存在';
            return FALSE;
        }
		$addons			=	array();
		$where['name']	=	array('in',$dirs);
		$list			=	db('Addons')->where($where)->field(true)->select();
		foreach($list as $addon){
			$addon['uninstall']		=	0;
			$addons[$addon['name']]	=	$addon;
		}
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
        int_to_string($addons, array('status'=>array(-1=>'损坏', 0=>'禁用', 1=>'启用', null=>'未安装')));
        $addons = list_sort_by($addons,'uninstall','desc');
        return $addons;
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
            $result = $this->save($data);
            if ($result) {
                model('Hooks')->addHooks($data['name']);
                return true;
            }else{
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