<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wuxian <feoyo@qq.com>
// +----------------------------------------------------------------------

namespace api\job\model;

use think\Model;
use think\db\Query;

class JobCommentModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'comment';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    /**
     *  关联 user表
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('api\job\model\UserModel', 'user_id');
    }

    /**
     * 关联 user表
     * @return \think\model\relation\BelongsTo
     */
    public function toUser()
    {
        return $this->belongsTo('api\job\model\UserModel', 'to_user_id')->field('user_nickname,avatar');
    }

    /**
     * 关联 post表
     * @return \think\model\relation\BelongsToMany
     */
    public function post()
    {
        return $this->belongsTo('api\job\model\JobPostModel', 'post_id');
    }

    /**
     * 关联 post表
     * @return \think\model\relation\BelongsTo
     */
    public function commentPost()
    {
        return $this->belongsTo('api\job\model\JobPostModel', 'post_id');
    }

    /**
     * 获取报名列表
     * @param  [type] $filter [description]
     * @return [type]         [description]
     */
    public function getUserComments ($filter)
    {
    	$data = $this->with(['user','toUser'])
    			->where(function (Query $query) use ($filter) {
                if (!empty($filter['id'])) {
                    $query->where('id', $filter['id']);
                }
                if (!empty($filter['user_id'])) {
                    $query->where('user_id', $filter['user_id']);
                }
            })
            ->where('delete_time', 0)
            ->select();

        return $data;
    }

    /**
     * 获取报名详情
     * @param  [type] $filter [description]
     * @return [type]         [description]
     */
    public function getUserComment ($filter)
    {
        $data = $this->with(['user','toUser'])
                ->where(function (Query $query) use ($filter) {
                if (!empty($filter['id'])) {
                    $query->where('id', $filter['id']);
                }
                if (!empty($filter['user_id'])) {
                    $query->where('user_id', $filter['user_id']);
                }
            })
            ->where('delete_time', 0)
            ->find();

        return $data;
    }
}
