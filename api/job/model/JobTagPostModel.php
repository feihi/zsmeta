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

class JobTagPostModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'job_tag_post';

    /**
     * 获取指定id相关的岗位id数组
     * @param $post_id  岗位id
     * @return array    相关的岗位id
     */
    function getRelationPostIds($post_id)
    {
        $tagIds  = $this->where('post_id', $post_id)
            ->column('tag_id');
        $postIds = $this->whereIn('tag_id', $tagIds)
            ->column('post_id');
        return array_unique($postIds);
    }
}
