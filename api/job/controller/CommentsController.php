<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------
namespace api\job\controller;

use cmf\controller\RestUserBaseController;
use api\job\model\JobPostModel;
use api\job\model\JobCommentModel;
use think\facade\Db;

class CommentsController extends RestUserBaseController
{
	/**
     * 显示资源列表
     */
    public function index()
    {
        $pageSize = config('job.page_size'); // 默认分页数量
        $limit = $this->request->param('limit', $pageSize, 'intval');
        $page  = $this->request->param('page', 1, 'intval');
        $page  = $page > 0 ? $page : 1;
        $limit = $limit > 0 ? $limit : $pageSize;

        $param['page']  = $page;
        $param['limit'] = $limit;

        $userId  = $this->getUserId();
        $param['user_id'] = $userId;
        
        $objectId  = $this->request->param('post_id', 0, 'intval');
        $postModel = new JobPostModel();
        $postInfo  = $postModel->where('id', $objectId)->find();
        
        if (empty($postInfo)) {
            $this->error('岗位不存在！');
        }

        $where['c.delete_time'] = 0;
        $where['c.status']      = 1;
        $where['c.table_name']  = 'job_post';
        $where['c.object_id']   = $objectId;
        
        $field = 'c.id,c.parent_id,c.user_id,c.to_user_id,c.object_id,c.content,c.create_time,c.more,u.user_nickname as username,u.avatar as user_avatar,ut.user_nickname as to_username,ut.avatar as to_user_avatar';
        $commentModel  = new JobCommentModel();
        $data = $commentModel->alias('c')
            ->join('user u', 'c.user_id = u.id', 'left')
            ->join('user ut', 'c.to_user_id = ut.id', 'left')
            ->field($field)
            ->where($where)
            ->where('parent_id', 0)
            ->order('c.create_time DESC')
            ->limit($limit)
            ->page($page)
            ->select();

        $count = $commentModel->alias('c')
            ->join('user u', 'c.user_id = u.id', 'left')
            ->join('user ut', 'c.to_user_id = ut.id', 'left')
            ->field('id')
            ->where($where)
            ->where('parent_id', 0)
            ->order('c.create_time DESC')
            ->count();

        $totalPage = ceil($count / $limit);

        foreach ($data as $k => &$vo) {
            $replies = $commentModel->alias('c')
                ->join('user u', 'c.user_id = u.id', 'left')
                ->join('user ut', 'c.to_user_id = ut.id', 'left')
                ->field($field)
                ->where($where)
                ->where('parent_id', $vo['id'])
                ->order('c.create_time DESC')
                ->select()->toArray();
            $vo['replies'] = $replies;
        }

        $response = [
            'list' => $data,
            'page' => $page,
            'total_page' => $totalPage,
        ];

        $this->success('请求成功!', $response);
    }

    public function save ()
    {
        $userId = $this->getUserId();

        $i = 5;
        if (session('com') && $i && (time() - session('com')) < $i) {
            $this->error('请' . $i . '秒后再评论！');
        }
            
        $data   = $this->request->param();
        $result = $this->validate($data, 'Comment');
        if ($result !== true) {
            $this->error($result);
        }

        $postId = $this->request->param('post_id', 0, 'intval');
        $postModel = new JobPostModel();
        $postInfo  = $postModel->where('id', $postId)->field('id,user_id,post_title,post_excerpt,more')->find();

        if (empty($postInfo)) {
            $this->error('岗位不存在！');
        }

        $parentComment = ['id' => 0, 'user_id' => $postInfo['user_id']];
        $commentModel  = new JobCommentModel();
        // 可对留言进行评论，暂时开放，上线后不开放
        $parentId      = $this->request->param('parent_id', 0, 'intval');
        if ($parentId > 0) {
            $parentComment = $commentModel->where('id', $parentId)->field('id,user_id')->find();
            if (empty($parentComment)) {
                $this->error('被评论的留言不存在！');
            }
        }

        Db::startTrans();
        try {

            $commentModel->insert([
                'user_id'     => $userId,
                'object_id'   => $postId,
                'table_name'  => 'job_post',
                'to_user_id'  => $parentComment['user_id'],
                'content'     => $data['content'],
                'parent_id'   => $parentComment['id'],
                'more'        => $postInfo['post_title'],
                'status'      => 1,
                'create_time' => time()
            ]);

            $postModel->where('id', $postId)->inc('comment_count')->update();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            dump($e->getMessage());
            $this->error('留言失败！');
        }

        session('com', time());
        $this->success("留言成功！");
    }

    /**
     * 删除留言
     * @param $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        if (empty($id)) {
            $this->error('无效的留言id');
        }

        $userId       = $this->getUserId();
        $commentModel = new JobCommentModel();
        $commentInfo  = $commentModel->where('id', $id)->find();

        if (!empty($commentInfo)) {
            
            $objectId    = $commentInfo['object_id'];
            $objectTable = $commentInfo['table_name'];

            $result = $commentModel
                ->where('id' , $id)
                ->where('user_id', $userId)
                ->update(['delete_time' => time(), 'status' => 2]);
        
            if ($result) {
                @Db::name($objectTable)->where('id=' . (int)$objectId)->dec('comment_count');
                
                $this->success('删除成功！');
            } else {
                $this->error('删除失败！');
            }
        } else {
            $this->error('留言不存在');
        }
    }
}