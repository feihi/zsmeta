<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: wuxian <feoyo@qq.com>
// +----------------------------------------------------------------------

namespace app\job\model;

use think\Model;

class JobApplyModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'job_apply';

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    /**
     * 关联 post表
     * @return \think\model\relation\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo('JobPostModel', 'post_id');
    }
}
