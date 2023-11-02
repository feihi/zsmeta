<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wuwu <15093565100@163.com>
// +----------------------------------------------------------------------
namespace api\job\controller;

use api\job\model\PortalCategoryModel;
use api\job\service\JobPostService;
use cmf\controller\RestBaseController;

class ListsController extends RestBaseController
{
    /**
     * 搜索查询
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function search()
    {
        $pageSize = config('job.page_size'); // 默认分页数量

        $param = $this->request->param();
        $keyword = $this->request->param('keyword', '');
        $limit = $this->request->param('limit', $pageSize, 'intval');
        $page  = $this->request->param('page', 1, 'intval');
        $page  = $page > 0 ? $page : 1;
        $limit = $limit > 0 ? $limit : $pageSize;

        $param['keyword'] = $keyword;
        $param['page']    = $page;
        $param['limit']   = $limit;
        $param['order']   = '-is_top,-recommended,-post_hits,-id';
        
        $postService = new JobPostService();
        $data        = $postService->jobPosts($param);
        $count       = $postService->jobPostsCount($param);
        $totalPage   = ceil($count / $limit);

        $response = [
            'list' => $data,
            'page' => $page,
            'total_page' => $totalPage,
        ];
        
        $this->success('请求成功!', $response);
    }

    /**
     * 热门岗位列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function hot()
    {
        $pageSize = config('job.page_size'); // 默认分页数量

        $keyword = $this->request->param('keyword', '');
        $limit = $this->request->param('limit', $pageSize, 'intval');
        $page  = $this->request->param('page', 1, 'intval');
        $page  = $page > 0 ? $page : 1;
        $limit = $limit > 0 ? $limit : $pageSize;

        $param['recommended'] = true;
        $param['page']    = $page;
        $param['limit']   = $limit;
        $param['order']   = '-is_top,-recommended,-post_hits,-id';
        
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
     * 分类文章列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCategoryPostLists()
    {
        $categoryId = $this->request->param('category_id', 0, 'intval');

        $portalCategoryModel = new  PortalCategoryModel();
        $findCategory        = $portalCategoryModel->where('id', $categoryId)->find();

        //分类是否存在
        if (empty($findCategory)) {
            $this->error('分类不存在！');
        }

        $param = $this->request->param();

        $portalPostService = new PortalPostService();
        $articles          = $portalPostService->postArticles($param);
        //是否需要关联模型
        if (!$articles->isEmpty()) {
            if (!empty($param['relation'])) {
                $allowedRelations = allowed_relations(['user', 'categories'], $param['relation']);
                if (!empty($allowedRelations)) {
                    $articles->load($allowedRelations);
                    $articles->append($allowedRelations);
                }
            }
        }
        $this->success('ok', ['list' => $articles]);
    }

}
