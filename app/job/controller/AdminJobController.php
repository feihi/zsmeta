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

use app\job\model\JobCategoryPostModel;
use app\job\model\JobTagPostModel;
use app\job\model\RecycleBinModel;
use cmf\controller\AdminBaseController;
use app\job\model\JobPostModel;
use app\job\service\PostService;
use app\job\service\TrendService;
use app\job\model\JobCategoryModel;
use think\facade\Db;

class AdminJobController extends AdminBaseController
{
    /**
     * 岗位列表
     * @adminMenu(
     *     'name'   => '岗位列表',
     *     'parent' => 'job/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '岗位列表',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $content = hook_one('job_admin_post_index_view');

        if (!empty($content)) {
            return $content;
        }

        $param = $this->request->param();

        $categoryId = $this->request->param('category', 0, 'intval');

        $postService = new PostService();
        $data        = $postService->adminJobList($param);

        $data->appends($param);

        $jobCategoryModel = new JobCategoryModel();
        $categoryTree     = $jobCategoryModel->adminCategoryTree($categoryId);

        $this->assign('start_time', isset($param['start_time']) ? $param['start_time'] : '');
        $this->assign('end_time', isset($param['end_time']) ? $param['end_time'] : '');
        $this->assign('age_min', isset($param['age_min']) ? $param['age_min'] : '');
        $this->assign('age_max', isset($param['age_max']) ? $param['age_max'] : '');
        $this->assign('wage_min', isset($param['wage_min']) ? $param['wage_min'] : '');
        $this->assign('wage_max', isset($param['wage_max']) ? $param['wage_max'] : '');
        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');
        $this->assign('posts', $data->items());
        $this->assign('category_tree', $categoryTree);
        $this->assign('category', $categoryId);
        $this->assign('page', $data->render());
        
        return $this->fetch();
    }

    /**
     * 添加岗位
     * @adminMenu(
     *     'name'   => '添加岗位',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加岗位',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $content = hook_one('job_admin_post_add_view');

        if (!empty($content)) {
            return $content;
        }

        $platSet = cmf_get_option('plat_set');
        $this->assign("plat_set", $platSet);
        
        $this->assign("lnglot_prefix",'');
        return $this->fetch();
    }

    /**
     * 添加岗位提交
     * @adminMenu(
     *     'name'   => '添加岗位提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加岗位提交',
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
            
            $post   = $data['post'];
            $result = $this->validate($post, 'JobPost');
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

            // 地址组合
            $data['post']['country'] = '中国';
            $data['post']['province'] = Db::name('region')->where('id', $post['province_id'])->value('name');
            $data['post']['city']     = Db::name('region')->where('id', $post['city_id'])->value('name');
            $data['post']['county']   = Db::name('region')->where('id', $post['county_id'])->value('name');

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
     * 编辑岗位
     * @adminMenu(
     *     'name'   => '编辑岗位',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑岗位',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {
        $content = hook_one('job_admin_post_edit_view');

        if (!empty($content)) {
            return $content;
        }

        $id = $this->request->param('id', 0, 'intval');

        $jobPostModel    = new JobPostModel();
        $post            = $jobPostModel->where('id', $id)->find();
        $postCategories  = $post->categories()->alias('a')->column('a.name', 'a.id');
        $postCategoryIds = implode(',', array_keys($postCategories));

        $this->assign('post', $post);
        $this->assign('post_categories', $postCategories);
        $this->assign('post_category_ids', $postCategoryIds);
        $this->assign("lnglot_prefix","edit_");

        return $this->fetch();
    }

    /**
     * 编辑岗位提交
     * @adminMenu(
     *     'name'   => '编辑岗位提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑岗位提交',
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

            $post   = $data['post'];
            $result = $this->validate($post, 'JobPost');
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

            // 地址组合
            $data['post']['country'] = '中国';
            $data['post']['province'] = Db::name('region')->where('id', $post['province_id'])->value('name');
            $data['post']['city']     = Db::name('region')->where('id', $post['city_id'])->value('name');
            $data['post']['county']   = Db::name('region')->where('id', $post['county_id'])->value('name');
            
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
     * 岗位删除
     * @adminMenu(
     *     'name'   => '岗位删除',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '岗位删除',
     *     'param'  => ''
     * )
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function delete()
    {
        $param        = $this->request->param();
        $jobPostModel = new JobPostModel();

        if (isset($param['id'])) {
            $id           = $this->request->param('id', 0, 'intval');
            $result       = $jobPostModel->where('id', $id)->find();
            $data         = [
                'object_id'   => $result['id'],
                'create_time' => time(),
                'table_name'  => 'job_post',
                'name'        => $result['post_title'],
                'user_id'     => cmf_get_current_admin_id()
            ];
            $resultJob = $jobPostModel
                ->where('id', $id)
                ->update(['delete_time' => time()]);
            if ($resultJob) {
                JobCategoryPostModel::where('post_id', $id)->update(['status' => 0]);
                JobTagPostModel::where('post_id', $id)->update(['status' => 0]);

                RecycleBinModel::insert($data);
            }
            $this->success("删除成功！", '');

        }

        if (isset($param['ids'])) {
            $ids     = $this->request->param('ids/a');
            $recycle = $jobPostModel->where('id', 'in', $ids)->select();
            $result  = $jobPostModel->where('id', 'in', $ids)->update(['delete_time' => time()]);
            if ($result) {
                JobCategoryPostModel::where('post_id', 'in', $ids)->update(['status' => 0]);
                JobTagPostModel::where('post_id', 'in', $ids)->update(['status' => 0]);
                foreach ($recycle as $value) {
                    $data = [
                        'object_id'   => $value['id'],
                        'create_time' => time(),
                        'table_name'  => 'job_post',
                        'name'        => $value['post_title'],
                        'user_id'     => cmf_get_current_admin_id()
                    ];
                    RecycleBinModel::insert($data);
                }
                $this->success("删除成功！", '');
            }
        }
    }

    /**
     * 岗位发布
     * @adminMenu(
     *     'name'   => '岗位发布',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '岗位发布',
     *     'param'  => ''
     * )
     */
    public function publish()
    {
        $param        = $this->request->param();
        $jobPostModel = new JobPostModel();

        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');
            $jobPostModel->where('id', 'in', $ids)->update(['post_status' => 1, 'published_time' => time()]);
            $this->success("发布成功！", '');
        }

        if (isset($param['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');
            $jobPostModel->where('id', 'in', $ids)->update(['post_status' => 0]);
            $this->success("取消发布成功！", '');
        }

    }

    /**
     * 岗位置顶
     * @adminMenu(
     *     'name'   => '岗位置顶',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '岗位置顶',
     *     'param'  => ''
     * )
     */
    public function top()
    {
        $param        = $this->request->param();
        $jobPostModel = new JobPostModel();

        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');

            $jobPostModel->where('id', 'in', $ids)->update(['is_top' => 1]);

            $this->success("置顶成功！", '');

        }

        if (isset($_POST['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');

            $jobPostModel->where('id', 'in', $ids)->update(['is_top' => 0]);

            $this->success("取消置顶成功！", '');
        }
    }

    /**
     * 岗位推荐
     * @adminMenu(
     *     'name'   => '岗位推荐',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '岗位推荐',
     *     'param'  => ''
     * )
     */
    public function recommend()
    {
        $param        = $this->request->param();
        $jobPostModel = new JobPostModel();

        if (isset($param['ids']) && isset($param["yes"])) {
            $ids = $this->request->param('ids/a');

            $jobPostModel->where('id', 'in', $ids)->update(['recommended' => 1]);

            $this->success("推荐成功！", '');

        }
        if (isset($param['ids']) && isset($param["no"])) {
            $ids = $this->request->param('ids/a');

            $jobPostModel->where('id', 'in', $ids)->update(['recommended' => 0]);

            $this->success("取消推荐成功！", '');

        }
    }

    /**
     * 岗位排序
     * @adminMenu(
     *     'name'   => '岗位排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '岗位排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        parent::listOrders('job_category_post');
        $this->success("排序更新成功！", '');
    }

    /**
     * 折线图
     * @adminMenu(
     *     'name'   => '折线图',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '折线图',
     *     'param'  => ''
     * )
     */
    public function echart()
    {
        if ($this->request->isPost()) {
            $postId = $this->request->param('post_id', 0, 'intval');
            $month  = $this->request->param('month/a');
            $wage   = $this->request->param('wage/a');
            $refee  = $this->request->param('refee/a');
            $general_wage = $this->request->param('general_wage/a');
            
            $data = array(
                'post_id' => $postId,
                'month' => $month,
                'wage_record' => $wage,
                'refee_record'  => $refee,
                'general_wage_record' => $general_wage,
            );
            $service = new TrendService();
            $result  = $service->setPostWageTrend($data);
           
           if ($result) {
                $this->success("保存成功！", '');
           } else {
                $this->success("保存失败！", '');
           }
        } else {
            $id = $this->request->param('id', 0, 'intval');
            $jobPostModel = new JobPostModel();
            $post         = $jobPostModel->where('id', $id)->find();

            $service = new TrendService();
            $data = $service->getPostWageTrend($id);
            
            $echartData = [
                'post' => $post,
                'months' => get_recent_year_months('Y年m月'),
                'wage_record' => $data['wage_record'],
                'refee_record' => $data['refee_record'],
                'general_wage_record' => $data['general_wage_record'],
            ];

            $this->assign('month_list', get_recent_year_months('Ym'));
            $this->assign('wage_record', $data['wage_record']);
            $this->assign('refee_record', $data['refee_record']);
            $this->assign('general_wage_record', $data['general_wage_record']);
            $this->assign('post', $post);
            $this->assign('data', json_encode($echartData));
            return $this->fetch();
        }
    }
}
