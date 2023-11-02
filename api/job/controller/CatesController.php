<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace api\job\controller;

use api\job\service\JobCategoryService;
use cmf\controller\RestBaseController;
use api\portal\model\JobCategoryModel;

class CatesController extends RestBaseController
{
    /**
     * 获取分类列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $params          = $this->request->get();
        $categoryService = new JobCategoryService();

        $params['order'] = '+list_order,-id';
        $data            = $categoryService->categories($params)->toArray();
        
        $response = ['list' => $data];
        $this->success('请求成功!', $response);
    }

    /**
     * 显示指定的分类
     * @param $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function read($id)
    {
        $categoryModel = new JobCategoryModel();
        $data          = $categoryModel
            ->where('delete_time', 0)
            ->where('status', 1)
            ->where('id', $id)
            ->find();
        if ($data) {
            $this->success('请求成功！', $data);
        } else {
            $this->error('该分类不存在！');
        }

    }
}
