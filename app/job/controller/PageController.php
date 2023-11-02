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
use app\job\service\PostService;

class PageController extends HomeBaseController
{
    /**
     * 页面管理
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $postService = new PostService();
        $pageId      = $this->request->param('id', 0, 'intval');
        $page        = $postService->publishedPage($pageId);

        if (empty($page)) {
            abort(404, ' 页面不存在!');
        }

        $this->assign('page', $page);

        $more = $page['more'];

        $tplName = empty($more['template']) ? 'page' : $more['template'];

        return $this->fetch("/$tplName");
    }

}
