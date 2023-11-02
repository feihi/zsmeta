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
namespace api\index\controller;

use cmf\controller\RestBaseController;

class IndexController extends RestBaseController
{

    /**
     * 获取岗位列表
     */
    public function index()
    {
        $data['menu'] =  [
            ['name' => '外地企业', 'param' => ['cate' => 1]],
            ['name' => '河南企业', 'param' => ['cate' => 2]],
            ['name' => '日结工资', 'param' => ['cate' => 3]],
            ['name' => '小时工', 'param' => ['cate' => 4]],
        ];

        $data['lunbo'] = [];
        $this->success('请求成功!', $data);
    }

    /**
     * 获取热门标签列表
     */
    public function hotTags()
    {
        $params                         = $this->request->get();
        $params['where']['recommended'] = 1;
        $data                           = $this->tagModel->getDatas($params);

        if (empty($this->apiVersion) || $this->apiVersion == '1.0.0') {
            $response = $data;
        } else {
            $response = ['list' => $data,];
        }
        $this->success('请求成功!', $response);
    }

    /**
     * 获取热门岗位列表
     * @param int $id
     */
    public function hotPosts($id)
    {
        $params    = $this->request->param();
        $postModel = new JobPostModel();

        if (!empty($params['relation'])) {
            $allowedRelations = $postModel->allowedRelations($params['relation']);
            if (!empty($allowedRelations)) {
                if (count($articles) > 0) {
                    $articles->load($allowedRelations);
                    $articles->append($allowedRelations);
                }
            }
        }


        $this->success('请求成功!', ['articles' => $articles]);
    }
}
