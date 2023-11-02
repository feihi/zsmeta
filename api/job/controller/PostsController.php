<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace api\job\controller;
use cmf\controller\RestBaseController;

use api\job\service\JobPostService;
use api\job\model\JobPostModel;
use api\job\model\JobApplyModel;
use app\job\service\TrendService;

use think\facade\Db;

class PostsController extends RestBaseController
{
    /**
     * 岗位列表
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $param    = $this->request->param();
        $pageSize = config('job.page_size'); // 默认分页数量

        $limit = $this->request->param('limit', $pageSize, 'intval');
        $page  = $this->request->param('page', 1, 'intval');
        $page  = $page > 0 ? $page : 1;
        $limit = $limit > 0 ? $limit : $pageSize;

        $param['page']  = $page;
        $param['limit'] = $limit;
        $param['order'] = '-is_top,-recommended,-post_hits,-id';

        $postService = new JobPostService();
        $data        = $postService->jobPosts($param);
        $count       = $postService->jobPostsCount($param);
        $totalPage   = ceil($count / $limit);

        //是否需要关联模型
        if (!$data->isEmpty()) {
            if (!empty($param['relation'])) {

                $allowedRelations = allowed_relations(['user', 'categories'], $param['relation']);

                if (!empty($allowedRelations)) {
                    $data->load('user');
                    $data->append($allowedRelations);
                }
            }
        }

        $response = [
            'list' => $data,
            'page' => $page,
            'total_page' => $totalPage,
        ];

        $this->success('请求成功!', $response);
    }

    /**
     * 获取指定的岗位
     * @param $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function read()
    {
        if (empty($this->user)) {
            $this->error(['code' => 10001, 'msg' => '登录已失效!']);
        }

        $id = $this->request->param('id', 0, 'intval');
        if (intval($id) === 0) {
            $this->error('无效的岗位id！');
        } else {
            $postModel = new JobPostModel();
            $data      = $postModel->where('id', $id)->find();

            if (empty($data)) {
                $this->error('岗位不存在！');
            } else {
                $trendService = new TrendService();
                $trendData = $trendService->getPostWageTrend($id);
                
                $echartData = [
                    'months' => get_recent_year_months('m月'),
                    'wage'   => $trendData['wage_record'],
                    'return_fee' => $trendData['refee_record'],
                    'general_wage' => $trendData['general_wage_record']
                ];
                $data['echart_data'] = $echartData;

                $postModel->where('id', $id)->inc('post_hits')->update();
                $url = cmf_url('job/Post/index', ['id' => $id, 'cid' => $data['categories'][0]['id']], true, true);
                $data['url'] = $url;
                $this->success('请求成功!', $data);
            }

        }
    }

    /**
     * 我的岗位列表（x）
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function my()
    {
        $param            = $this->request->get();
        $param['user_id'] = $this->getUserId();

        $postService = new JobPostService();
        $data        = $postService->postArticles($param);

        if (empty($this->apiVersion) || $this->apiVersion == '1.0.0') {
            $response = [$data];
        } else {
            $response = ['list' => $data];
        }

        $this->success('请求成功!', $response);
    }
    
    /**
     * 相关岗位列表/plugin/swagger/index/config.html
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function relatedPosts()
    {
        $articleId  = $this->request->param('id', 0, 'intval');
        $categoryId = Db::name('portal_category_post')->where('post_id', $articleId)->value('category_id');

        $postModel = new PortalPostModel();
        $articles  = $postModel
            ->alias('post')
            ->join('__PORTAL_CATEGORY_POST__ category_post', 'post.id=category_post.post_id')
            ->where(['post.delete_time' => 0, 'post.post_status' => 1, 'category_post.category_id' => $categoryId])
            ->orderRaw('rand()')
            ->limit(5)
            ->select();
        if ($articles->isEmpty()){
            $this->error('没有相关岗位！');
        }
        $this->success('success', ['list' => $articles]);
    }
}