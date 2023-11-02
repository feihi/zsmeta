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
namespace app\job\controller;

use app\job\model\JobTagModel;
use app\job\model\JobTagPostModel;
use cmf\controller\AdminBaseController;

/**
 * Class AdminTagController 标签管理控制器
 * @package app\job\controller
 */
class AdminJobTagController extends AdminBaseController
{
    /**
     * 岗位标签管理
     * @adminMenu(
     *     'name'   => '岗位标签',
     *     'parent' => 'job/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '岗位标签',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $content = hook_one('job_admin_tag_index_view');

        if (!empty($content)) {
            return $content;
        }

        $jobTagModel = new JobTagModel();
        $tags        = $jobTagModel->paginate();

        $this->assign("arrStatus", $jobTagModel::$STATUS);
        $this->assign("tags", $tags);
        $this->assign('page', $tags->render());
        return $this->fetch();
    }

    /**
     * 添加岗位标签
     * @adminMenu(
     *     'name'   => '添加岗位标签',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加岗位标签',
     *     'param'  => ''
     * )
     */
    public function add()
    {
        $jobTagModel = new JobTagModel();
        $this->assign("arrStatus", $jobTagModel::$STATUS);
        return $this->fetch();
    }

    /**
     * 添加岗位标签提交
     * @adminMenu(
     *     'name'   => '添加岗位标签提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加岗位标签提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {

        $arrData = $this->request->param();

        $jobTagModel = new JobTagModel();
        $jobTagModel->save($arrData);

        $this->success(lang("SAVE_SUCCESS"));

    }

    /**
     * 更新岗位标签状态
     * @adminMenu(
     *     'name'   => '更新标签状态',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '更新标签状态',
     *     'param'  => ''
     * )
     */
    public function upStatus()
    {
        $intId     = $this->request->param("id");
        $intStatus = $this->request->param("status");
        $intStatus = $intStatus ? 1 : 0;
        if (empty($intId)) {
            $this->error(lang("NO_ID"));
        }

        $jobTagModel = new JobTagModel();
        $jobTagModel->where("id", $intId)->update(["status" => $intStatus]);

        $this->success(lang("SAVE_SUCCESS"));

    }

    /**
     * 删除岗位标签
     * @adminMenu(
     *     'name'   => '删除岗位标签',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除岗位标签',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $intId = $this->request->param("id", 0, 'intval');

        if (empty($intId)) {
            $this->error(lang("NO_ID"));
        }

        JobTagModel::where('id', $intId)->delete();
        JobTagPostModel::where('tag_id', $intId)->delete();
        $this->success(lang("DELETE_SUCCESS"));
    }
}
