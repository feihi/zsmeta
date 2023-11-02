<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2022 http://www.sandbean.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: afei <feoyo@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;
use cmf\controller\AdminBaseController;
use app\admin\model\RegionModel;

class RegionController extends AdminBaseController
{
	
	/**
     * 区域管理
     * @adminMenu(
     *     'name'   => '区域管理',
     *     'parent' => 'admin/Setting/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '区域管理',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
	public function index()
	{
		$regionModel = new RegionModel();
		$parentId    = $this->request->param('pid', 0, 'intval');
		$parent_info = $regionModel->field('id,name,level')->where('id', '=', $parentId)->find();
		if(empty($parent_info['name'])){ $parent_info['name'] = '中国';}
		// halt($parent_info);
		
		$area_type   = array(0=>"省级区域",1=>"市级区域",2=>'区/县区域',3=>'乡镇/街道区域',4=>'村/社区区域');
		$region_type = empty($parent_info['level'])? 0 : $parent_info['level'];
		
		$region_type_name = $area_type[$region_type];

		$area_list = $this->area_list($parentId);
		
		$this->assign("parent_id",$parentId);
		$this->assign("region_type",($region_type+1));
		$this->assign("region_type_name",$region_type_name);
		$this->assign("parent_name",$parent_info['name']);
		$this->assign("area_list",$area_list);
		
		return $this->fetch();
	}
	
	/**
     * 添加区域
     * @adminMenu(
     *     'name'   => '添加区域',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加区域',
     *     'param'  => ''
     * )
     */
	public function add()
	{
		$parent_id      = intval($_POST['post']['parent_id']);
		$region_name    = trim($_POST['post']['region_name']);
		$region_type    = intval($_POST['post']['region_type']);
		if(empty($region_name)){
			$this->error('请填写地区名称');
		}
		$check_name = $this->region_obj->where('region_name = "'.$region_name.'" and parent_id = '.$parent_id)->find();
		if($check_name){
			$this->error('该地区已经存在。');
		}else{
			
			$result = $this->region_obj->add($_POST['post']);
			if ($result) {
					$this->success("添加成功！");
			} else {
					$this->error("添加失败！");
			}
			
		}	
	}
	
	//删除维修方法
	function delete(){
		
		if(isset($_GET['id'])){
			$id = intval($_GET['id']);
			$this->region_obj->where(" region_id = ".$id)->delete();
			$this->success("数据删除成功！");
			
		}
		
		if(isset($_POST['ids'])){
			$ids=join(",",$_POST['ids']);
			if ($this->region_obj->where("region_id in (".$ids.")")->delete()) {
				$this->success("数据删除成功！");
			} else {
				$this->error("数据删除失败！");
			}
		}
		
	}
	
	//更新地区名称
	function ajax_region_update(){
		
		$result = array('error'=>0,'info'=>'');
		$region_id = intval($_POST['region_id']);
		if(empty($_POST['region_name']) || empty($region_id)){
			
			$result['error'] = 1;
			$result['info'] = '名称不能为空！';
		
		}else{
		
			$check_name = $this->region_obj->where('region_name = "'.$_POST['region_name'].'" and region_id != '.$region_id)->find();
			if($check_name){
				$result['error'] = 1;
				$result['info'] = '该名称已经存在！';
			}else{
				$check = $this->region_obj->where('region_id = '.$region_id)->setField('region_name',$_POST['region_name']);
			}
		}
		$data = json_encode($result);
		echo $data;
	}
	/**
	 * 获取地区列表的函数。
	 *
	 * @access  public
	 * @param   int     $region_id  上级地区id
	 * @return  void
	 */
	function area_list($region_id=0)
	{
		$area_arr = array();
		$regionModel = new RegionModel();
		$row = $regionModel->where(" parent_id = ".$region_id)->order('id')->select();	
		return $row;
	}
	
}