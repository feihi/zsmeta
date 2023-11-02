<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wuwu <15093565100@163.com>
// +----------------------------------------------------------------------
namespace api\job\controller;

use api\job\service\JobPostService;
use cmf\controller\RestBaseController;

class UserController extends RestBaseController
{
    /**
     * 会员文章列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function posts()
    {
        $userId = $this->request->param('user_id', 0, 'intval');

        if (empty($userId)) {
            $this->error('用户id不能空！');
        }

        $param             = $this->request->param();
        $param['user_id']  = $userId;
        $postService       = new JobPostService();
        $data              = $postService->jobPosts($param);
        if ($data->isEmpty()) {
            $this->error('没有数据');
        } else {
            $this->success('ok', ['list' => $data]);
        }
    }

}
