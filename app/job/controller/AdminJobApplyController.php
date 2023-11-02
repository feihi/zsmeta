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

use app\job\model\RecycleBinModel;
use cmf\controller\AdminBaseController;
use app\job\model\JobPostModel;
use app\job\model\JobApplyModel;
use app\job\service\ApplyService;
use app\job\model\JobCategoryModel;
use think\db\Query;

class AdminJobApplyController extends AdminBaseController
{
    /**
     * 报名记录
     * @adminMenu(
     *     'name'   => '报名记录',
     *     'parent' => 'job/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '报名记录',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $content = hook_one('job_admin_post_apply_index_view');

        if (!empty($content)) {
            return $content;
        }

        $keyword    = $this->request->param('keyword');
        $categoryId = $this->request->param('category', 0, 'intval');
        $status     = $this->request->param('status', 0, 'intval');
        $param      = $this->request->param();

        $service = new ApplyService();
        $data    = $service->adminApplyList($param);

        $data->appends($param);
        
        $jobCategoryModel = new JobCategoryModel();
        $categoryTree     = $jobCategoryModel->adminCategoryTree($categoryId);

        $this->assign('record', $data->items());
        $this->assign('start_time', isset($param['start_time']) ? $param['start_time'] : '');
        $this->assign('end_time', isset($param['end_time']) ? $param['end_time'] : '');
        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');
        $this->assign('status', isset($param['status']) ? $param['status'] : '');
        $this->assign('category_tree', $categoryTree);
        $this->assign('category', $categoryId);
        $this->assign('page', $data->render());

        return $this->fetch();
    }

    /**
     * 添加报名
     * @adminMenu(
     *     'name'   => '添加报名',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加报名',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $content = hook_one('job_admin_post_apply_add_view');

        if (!empty($content)) {
            return $content;
        }

        return $this->fetch();
    }

    /**
     * 添加报名提交
     * @adminMenu(
     *     'name'   => '添加报名提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加报名提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            
            //状态只能设置默认值。未发布、未置顶、未推荐
            $data['post']['post_status'] = 0;
            $data['post']['is_top']      = 0;
            $data['post']['recommended'] = 0;

            $post    = $data['post'];
            $content = $data['content'];
            $validateData = array_merge($post, $content);

            $result = $this->validate($validateData, 'AdminJob');
            if ($result !== true) {
                $this->error($result);
            }

            $jobPostModel = new JobPostModel();

            if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {
                $data['post']['more']['photos'] = [];
                foreach ($data['photo_urls'] as $key => $url) {
                    $photoUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['photos'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
                }
            }

            if (!empty($data['file_names']) && !empty($data['file_urls'])) {
                $data['post']['more']['files'] = [];
                foreach ($data['file_urls'] as $key => $url) {
                    $fileUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['files'], ["url" => $fileUrl, "name" => $data['file_names'][$key]]);
                }
            }

            $data['post']['post_content'] = json_encode($content);

            $jobPostModel->adminAddPost($data['post'], $data['post']['categories']);

            $data['post']['id'] = $jobPostModel->id;
            $hookParam          = [
                'is_add' => true,
                'post'   => $data['post']
            ];
            hook('job_admin_after_save_post', $hookParam);

            $this->success('添加成功!', url('AdminJob/edit', ['id' => $jobPostModel->id]));
        }

    }

    /**
     * 编辑报名
     * @adminMenu(
     *     'name'   => '编辑报名',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑报名',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $content = hook_one('job_admin_post_apply_edit_view');

        if (!empty($content)) {
            return $content;
        }

        $id = $this->request->param('id', 0, 'intval');

        $jobPostModel    = new JobPostModel();
        $post            = $jobPostModel->where('id', $id)->find();
        $postCategories  = $post->categories()->alias('a')->column('a.name', 'a.id');
        $postCategoryIds = implode(',', array_keys($postCategories));

        $postContent = json_decode($post['post_content'], true);

        $this->assign('post', $post);
        $this->assign('post_content', $postContent);
        $this->assign('post_categories', $postCategories);
        $this->assign('post_category_ids', $postCategoryIds);

        return $this->fetch();
    }

    /**
     * 编辑报名提交
     * @adminMenu(
     *     'name'   => '编辑报名提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑报名提交',
     *     'param'  => ''
     * )
     * @throws \think\Exception
     */
    public function editPost()
    {

        if ($this->request->isPost()) {
            $data = $this->request->param();

            //需要抹除发布、置顶、推荐的修改。
            // unset($data['post']['post_status']);
            // unset($data['post']['is_top']);
            // unset($data['post']['recommended']);

            $post    = $data['post'];
            $content = $data['content'];
            $validateData = array_merge($post, $content);

            $result = $this->validate($validateData, 'AdminJob');
            if ($result !== true) {
                $this->error($result);
            }

            $jobPostModel = new JobPostModel();

            if (!empty($data['photo_names']) && !empty($data['photo_urls'])) {
                $data['post']['more']['photos'] = [];
                foreach ($data['photo_urls'] as $key => $url) {
                    $photoUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['photos'], ["url" => $photoUrl, "name" => $data['photo_names'][$key]]);
                }
            }

            if (!empty($data['file_names']) && !empty($data['file_urls'])) {
                $data['post']['more']['files'] = [];
                foreach ($data['file_urls'] as $key => $url) {
                    $fileUrl = cmf_asset_relative_url($url);
                    array_push($data['post']['more']['files'], ["url" => $fileUrl, "name" => $data['file_names'][$key]]);
                }
            }

            $data['post']['post_content'] = json_encode($content);

            $jobPostModel->adminEditPost($data['post'], $data['post']['categories']);

            $hookParam = [
                'is_add' => false,
                'post'   => $data['post']
            ];
            hook('job_admin_after_save_post', $hookParam);

            $this->success('保存成功!');

        }
    }

    /**
     * 处理岗位报名
     * @adminMenu(
     *     'name'   => '处理岗位报名',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '处理岗位报名',
     *     'param'  => ''
     * )
     */
    public function deal()
    {
        $intId = $this->request->param("id", 0, 'intval');

        if (empty($intId)) {
            $this->error(lang("NO_ID"));
        }
        
        JobApplyModel::where('id', $intId)->update(['status' => 1]);
        $this->success('处理成功');
    }

    /**
     * 删除岗位报名
     * @adminMenu(
     *     'name'   => '删除岗位报名',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除岗位报名',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $intId = $this->request->param("id", 0, 'intval');

        if (empty($intId)) {
            $this->error(lang("NO_ID"));
        }
        
        JobApplyModel::where('id', $intId)->update(['delete_time' => time()]);
        $this->success(lang("DELETE_SUCCESS"));
    }
}
