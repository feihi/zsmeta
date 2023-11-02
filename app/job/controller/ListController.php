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

use cmf\controller\HomeBaseController;
use app\job\model\PortalCategoryModel;

class ListController extends HomeBaseController
{
    /***
     * 文章列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $id                  = $this->request->param('id', 0, 'intval');
        $portalCategoryModel = new PortalCategoryModel();

        $category = $portalCategoryModel->where('id', $id)->where('status', 1)->find();
       
        $this->assign('category', $category);

        $listTpl = empty($category['list_tpl']) ? 'list' : $category['list_tpl'];

        return $this->fetch('/' . $listTpl);
    }

}
