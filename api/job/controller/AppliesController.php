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
use api\job\logic\JobApplyModel;
use api\job\model\JobPostModel;

use think\facade\Db;

class AppliesController extends RestUserBaseController
{
    /**
     * 显示资源列表
     */
    public function index()
    {
        $param     = $this->request->param();
        $userId    = $this->getUserId();

        $param['user_id'] = $userId;
        
        $applyModel = new JobApplyModel();
        $data       = $applyModel->getUserApplies($param);

        $response = [
            'list' => $data,
            'page' => 1,
            'total_page' => 1,
        ];

        $this->success('请求成功!', $response);
    }

    /**
     * 岗位报名 保存新建的报名信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function save()
    {
        $userId = $this->getUserId();
            
        $data   = $this->request->param();
        $result = $this->validate($data, 'Apply');
        if ($result !== true) {
            $this->error($result);
        }

        $postId = $this->request->param('post_id', 0, 'intval');

        $applyModel = new JobApplyModel();
        
        $findApplyCount = $applyModel->where([
            // 'user_id' => $userId,
            'post_id' => $postId,
            'idcard'  => $data['idcard']  // 使用身份证号码验证，同一人同一岗位只能报名一次
        ])->where('delete_time', 0)->count();
        
        if (empty($findApplyCount)) {
            $postModel = new JobPostModel();
            $postinfo  = $postModel->where('id', $postId)->field('id,post_title,post_excerpt,more')->find();

            if (empty($postinfo)) {
                $this->error('岗位不存在！');
            }

            Db::startTrans();
            try {
                $postModel->where(['id' => $postId])->inc('applicant_number')->update();
                $thumbnail = empty($postinfo['more']['thumbnail']) ? '' : $postinfo['more']['thumbnail'];
                $applyModel->insert([
                    'user_id'     => $userId,
                    'post_id'     => $postId,
                    'realname'    => $data['realname'],
                    'mobile'      => $data['mobile'],
                    'idcard'      => $data['idcard'],
                    'post_title'  => $postinfo['post_title'],
                    'description' => $postinfo['post_excerpt'],
                    'create_time' => time(),
                    'update_time' => time(),
                ]);
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                // dump($e->getMessage());
                $this->error('报名失败！');
            }

            $applyCount = $postModel->where('id', $postId)->value('applicant_number');
            $this->success("报名成功！", ['applicant_number' => $applyCount]);
        } else {
            $this->error("您已报过名啦！");
        }   
    }

    /**
     * 显示指定的资源
     * @param $id
     */
    public function read($id)
    {
        if (empty($id)) {
            $this->error('无效的报名id');
        }
        $params       = $this->request->get();
        $userId       = $this->getUserId();
        $params['id'] = $id;
        $params['user_id'] = $userId;
        $applyModel   = new JobApplyModel();
        $data         = $applyModel->getUserApply($param);

        $this->success('请求成功!', $data);
    }

    /**
     * 取消报名
     * @param $id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        if (empty($id)) {
            $this->error('无效的报名id');
        }

        $userId      = $this->getUserId();
        $applyModel = new JobApplyModel();
        $result     = $applyModel->where('id' , $id)
            ->where('user_id', $userId)
            ->update(['delete_time' => time()]);
        
        if ($result) {
            $this->success('取消成功！');
        } else {
            $this->error('取消失败！');
        }
    }
}