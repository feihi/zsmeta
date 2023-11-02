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

class JobCategoryModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'job_category';

    //类型转换
    protected $type = [
        'more' => 'array',
    ];


    //模型关联方法
    protected $relationFilter = ['posts'];


    /**
     * more 自动转化
     * @param $value
     * @return array
     */
    public function getMoreAttr($value)
    {
        $more = json_decode($value, true);
        if (!empty($more['thumbnail'])) {
            $more['thumbnail'] = cmf_get_image_url($more['thumbnail']);
        }

        if (!empty($more['photos'])) {
            foreach ($more['photos'] as $key => $value) {
                $more['photos'][$key]['url'] = cmf_get_image_url($value['url']);
            }
        }
        return $more;
    }

    /**
     * 关联岗位表
     * @return \think\model\relation\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany('JobPostModel', 'job_category_post', 'post_id', 'category_id');
    }

    /**
     * 关联岗位分类和岗位表
     * @return \think\model\relation\HasMany
     */
    public function PostIds()
    {
        return $this->hasMany('JobCategoryPostModel', 'category_id', 'id');
    }

    /**
     * 此类岗位id数组
     * @param string $category_id 分类di
     * @return array|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function categoryPostIds($category_id)
    {
        $ids      = [];
        $post_ids = self::relation('PostIds')->field(true)->where('id', $category_id)->find();
        foreach ($post_ids['PostIds'] as $key => $id) {
            $ids[] = $id['post_id'];
        }
        $post_ids['PostIds'] = $ids;
        return $post_ids;
    }
}
