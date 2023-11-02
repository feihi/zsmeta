<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------

namespace api\job\model;

use think\Model;

class JobTagModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'job_tag';

    /**
     * 关联 岗位表
     * @return \think\model\relation\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany('JobPostModel','job_tag_post','post_id','tag_id')->alias('post');
    }
}
