<?php
// +----------------------------------------------------------------------
// | d_comment [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 DaliyCode All rights reserved.
// +----------------------------------------------------------------------
// | Author: DaliyCode <3471677985@qq.com> <author_url:dalicode.com>
// +----------------------------------------------------------------------
namespace plugins\d_comment\controller;

use app\admin\model\PluginModel;
use app\portal\model\PortalPostModel;
use app\user\model\CommentModel;
use cmf\controller\PluginBaseController;
use think\facade\Db;

class IndexController extends PluginBaseController
{
    public function _initialize()
    {
        $where = ['status' => 1, 'name' => $this->getPlugin()->info['name']];
        $vo    = PluginModel::where($where)->cache(60, true)->find();
        if (!$vo) {
            $this->error('评论插件未启用！');
        }
    }

    public function add()
    {
        $config = $this->getPlugin()->getConfig();
        if (!$config || $config['comment_type'] == 2) {
            $this->error('评论已关闭！');
        }

        $userId = cmf_get_current_user_id();
        if ($userId) {
            $i = (int) $config['comment_interval'] >= 0 ? (int) $config['comment_interval'] : 5;
            if (session('com') && $i && (time() - session('com')) < $i) {
                $this->error('请' . $i . '秒后再评论！');
            }

            $params             = $this->request->param();
            $data['user_id']    = $userId;
            $data['object_id']  = $params['object_id'];
            $data['table_name'] = $params['table_name'];
            $data['to_user_id'] = $params['to_user_id'];
            $data['content']    = $params['content'];
            $data['parent_id']  = $params['parent_id'];
            $data['more']       = $params['object_title'];
            $data['create_time'] = time();
            $config['comment_check'] == 1 && $data['status'] = 0;
            
            $result = Db::name('comment')->insert($data);

            if (false === $result) {

                $this->error('评论失败');
            }

            session('com', time());
            PortalPostModel::where('id=' . $data['object_id'])->inc('comment_count')->update();
            $this->success("评论成功");

        } else {
            $this->error('请登录！');
        }
    }
}