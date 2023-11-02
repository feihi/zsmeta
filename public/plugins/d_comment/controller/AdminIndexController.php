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
use cmf\controller\PluginBaseController;
use think\facade\Db;

class AdminIndexController extends PluginBaseController
{

    public function _initialize()
    {
        $where = ['status' => 1, 'name' => $this->getPlugin()->info['name']];
        $vo    = PluginModel::where($where)->cache(60, true)->find();
        if (!$vo) {
            $this->error('评论插件未启用！');
        }

        $adminId = cmf_get_current_admin_id();
        if (!empty($adminId)) {
            $this->assign("admin_id", $adminId);
        } else {
            $this->error('请登录！');
        }
    }

    /**
     * 插件列表
     * @adminMenu(
     *     'name'   => '岗位留言',
     *     'parent' => 'job/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '岗位留言',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $param = $this->request->param();
        $where['c.delete_time'] = 0;
        $where['c.status'] = array(0,1);
        // $where['c.table_name'] = 'portal_post';

        $startTime = empty($param['start_time']) ? 0 : strtotime($param['start_time']);
        $endTime   = empty($param['end_time']) ? 0 : strtotime($param['end_time']);
        if (!empty($startTime) && !empty($endTime)) {
            $where['c.create_time'] = [['>= time', $startTime], ['<= time', $endTime]];
        } else {
            if (!empty($startTime)) {
                $where['c.create_time'] = ['>= time', $startTime];
            }
            if (!empty($endTime)) {
                $where['c.create_time'] = ['<= time', $endTime];
            }
        }

        $keyword = empty($param['keyword']) ? '' : $param['keyword'];
        if (!empty($keyword)) {
            $where['c.content|c.more'] = ['like', "%$keyword%"];
        }

        $status = isset($param['status']) ? $param['status'] : -1;
        if ($status > -1) {
            $where['c.status'] = (int) $status;
        }

        $username = empty($param['username']) ? '' : $param['username'];
        if (!empty($username)) {
            $where['u.user_nickname'] = trim($username);
        }
        
        $comments = Db::name('comment')->alias('c')
            ->join('user u', 'c.user_id = u.id', 'left')
            ->join('user ut', 'c.to_user_id = ut.id', 'left')
            ->field('c.*,u.user_nickname as username,ut.user_nickname as to_username')
            ->where($where)
            ->order('c.create_time DESC')
            ->paginate(10);
        
        $comments->appends($param);

        $this->assign('start_time', isset($param['start_time']) ? $param['start_time'] : '');
        $this->assign('end_time', isset($param['end_time']) ? $param['end_time'] : '');
        $this->assign('keyword', isset($param['keyword']) ? $param['keyword'] : '');
        $this->assign('username', isset($param['username']) ? $param['username'] : '');
        $this->assign('status', isset($param['status']) ? $param['status'] : '');
        $this->assign('list', $comments);
        $this->assign('page', $comments->render());

        return $this->fetch('/admin_index');
    }

    /**
     * 删除
     * @param  integer $id  [description]
     * @param  integer $oid [description]
     * @return [type]       [description]
     */
    public function del($id = 0, $oid = 0)
    {
        $param = $this->request->param();
        $id  = $param['id'];
        $oid = $param['oid'];
        
        $relateTableName = Db::name('comment')->where(['id' => (int) $id])->value('table_name');
        if (Db::name('comment')->where(['id' => (int) $id])->update(['delete_time' => time(), 'status' => 2])) {

            @Db::name($relateTableName)->where('id' ,(int)$oid)->dec('comment_count');
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

    /**
     * 审核
     * @return [type] [description]
     */
    public function pass()
    {
        $ids = $this->request->param('ids/a');
        if (is_array($ids)) {
            if (Db::name('comment')->where(['id' => ['in', $ids]])->update(['status' => 1]) !== false) {
                $this->success('审核成功');
            }
        }
        $this->error('审核失败');
    }

    /**
     * 批量删除
     * @return [type] [description]
     */
    public function delall()
    {
        $ids = $this->request->param('ids/a');
        
        if (is_array($ids)) {
            $d = Db::name('comment')->field('id,object_id,table_name')->where('id','in', $ids)->select()->toArray();
            // Db::name('comment')->where(['id' => ['in', array_map('reset', $d)]])->update(['delete_time' => time(), 'status' => 2]);
            // $r = array_count_values(array_map('end', $d));
            halt($r);
            foreach ($r as $key => $v) {
                @Db::name('comment')->where('id=' . $key)->dec('comment_count', $v);
            }
            $this->success('删除成功！');
        }
        $this->success('删除失败！');
    }

    /**
     * 回复
     * @return [type] [description]
     */
    public function reply()
    {
        $id = $this->request->param('id', 0, 'intval');
        if ($this->request->isPost()) {

            $userId = cmf_get_current_admin_id();
            if ($userId) {

                $parentId = $this->request->param('parent_id', 0, 'intval');
                $toUserId = $this->request->param('to_user_id', 0, 'intval');
                $content  = $this->request->param('content', '', 'strval');

                $pcomment = Db::name('comment')->where('id', $parentId)->find();
                $objectId  = $pcomment['object_id'];
                $tableName = $pcomment['table_name'];
                $postInfo = Db::name($tableName)->where('id', $objectId)->find();
                
                $data['user_id']    = $userId;
                $data['object_id']  = $pcomment['object_id'];
                $data['table_name'] = $pcomment['table_name'];
                $data['to_user_id'] = $toUserId;
                $data['content']    = $content;
                $data['parent_id']  = $parentId;
                $data['more']       = $postInfo['post_title'];
                $data['create_time'] = time();
                $data['status']      = 1;
                
                $result = Db::name('comment')->insert($data);
                if (false === $result) {
                    $this->error('回复失败');
                }

                $this->success("回复成功");

            } else {
                $this->error('请登录！');
            }
        } else {
            $comments = Db::name('comment')->alias('c')
                ->join('user u', 'c.user_id = u.id', 'left')
                ->join('user ut', 'c.to_user_id = ut.id', 'left')
                ->field('c.*,u.user_nickname as username,ut.user_nickname as to_username')
                ->where('c.delete_time=0')
                ->where("c.id={$id} or c.parent_id={$id}")
                ->order('c.create_time DESC')
                ->select();
            // halt($comments);
            if (empty($comments)) {
                $this->error('留言信息错误');
            }

            $objectId  = $comments[0]['object_id'];
            $tableName = $comments[0]['table_name'];
            $postInfo  = Db::name($tableName)->where('id', $objectId)->find();

            $config = $this->getPlugin()->getConfig();

            $this->assign('comments', $comments);
            $this->assign('post', $postInfo);
            $this->assign($config);
            return $this->fetch('/reply');
        }
    }
}
