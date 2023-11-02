<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: pl125 <xskjs888@163.com>
// +----------------------------------------------------------------------
namespace api\job\service;

use api\job\model\JobApplyModel;
use think\db\Query;

class JobApplyService
{
    //模型关联方法
    protected $relationFilter = ['user'];

    /**
     * 文章列表
     * @param      $filter
     * @param bool $isPage
     * @return array|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function jobApplies($filter, $isPage = false)
    {
        $join = [];

        $field = empty($filter['field']) ? 'a.*' : explode(',', $filter['field']);
        //转为查询条件
        if (is_array($field)) {
            foreach ($field as $key => $value) {
                $field[$key] = 'a.' . $value;
            }
            $field = implode(',', $field);
        }
        $page     = empty($filter['page']) ? 1 : $filter['page'];
        $limit    = empty($filter['limit']) ? 10 : $filter['limit'];
        $order    = empty($filter['order']) ? ['-update_time'] : explode(',', $filter['order']);
        $category = empty($filter['category_id']) ? 0 : intval($filter['category_id']);
        if (!empty($category)) {
            array_push($join, ['job_category_post b', 'a.id = b.post_id']);
            $field = $field.',b.id AS post_category_id,b.list_order,b.category_id';
        }
        
        $orderArr  = order_shift($order);
        $postModel = new JobPostModel();
        
        if (!empty($page)) {
            $postModel = $postModel->page($page, $limit);
        } elseif (!empty($limit)) {
            $postModel = $postModel->limit($page,$limit);
        } else {
            $postModel = $postModel->limit(10);
        }

        $postModel = $postModel->alias('a');
        if (!empty($join)) {
            foreach ($join as $k => $v) {
                $postModel = $postModel->join($v[0], $v[1]);
            }
        }
        
        $posts = $postModel->field($field)
            ->where('a.create_time', '>=', 0)
            ->where('a.delete_time', 0)
            ->where('a.post_status', 1)
            ->where(function (Query $query) use ($filter) {
                if (!empty($filter['user_id'])) {
                    $query->where('a.user_id', $filter['user_id']);
                }
                $category = empty($filter['category_id']) ? 0 : intval($filter['category_id']);
                if (!empty($category)) {
                    $query->where('b.category_id', $category);
                }
                // 发布时间
                $startTime = empty($filter['start_time']) ? 0 : strtotime($filter['start_time']);
                $endTime   = empty($filter['end_time']) ? 0 : strtotime($filter['end_time']);
                if (!empty($startTime)) {
                    $query->where('a.published_time', '>=', $startTime);
                }
                if (!empty($endTime)) {
                    $query->where('a.published_time', '<=', $endTime);
                }

                $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
                if (!empty($keyword)) {
                    $query->where('a.post_title', 'like', "%$keyword%");
                }
                
                if (!empty($filter['recommended'])) {
                    $query->where('a.recommended', '=', 1);
                }
                // 区域-省
                if (!empty($filter['province_id'])) {
                    // halt(substr($filter['province_id'], 0, 1) == '-');
                    if ($filter['province_id'] < 0) {
                        $query->where('a.province_id', '<>', abs($filter['province_id']));
                    } else {
                        $query->where('a.province_id', '=', $filter['province_id']);
                    }
                }
                // 区域-城市
                if (!empty($filter['city_id'])) {
                    $query->where('a.city_id', '=', $filter['city_id']);
                }

                if (!empty($filter['ids'])) {
                    $ids = str_to_arr($filter['ids']);
                    $query->where('a.id', 'in', $ids);
                }
            })
            ->order($orderArr)
            ->select();
        
        return $posts;
    }

    /**
     * 岗位数量统计
     * @param      $filter
     * @param bool $isPage
     * @return array|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function jobAppliesCount($filter, $isPage = false)
    {
        $join = [];

        $category = empty($filter['category_id']) ? 0 : intval($filter['category_id']);
        if (!empty($category)) {
            array_push($join, ['job_category_post b', 'a.id = b.post_id']);
        }

        $postModel = new JobPostModel();

        if (!empty($join)) {
            foreach ($join as $k => $v) {
                $postModel = $postModel->join($v[0], $v[1]);
            }
        }
        
        $data = $postModel->alias('a')
            ->where('a.create_time', '>=', 0)
            ->where('a.delete_time', 0)
            ->where('a.post_status', 1)
            ->where(function (Query $query) use ($filter) {
                if (!empty($filter['user_id'])) {
                    $query->where('a.user_id', $filter['user_id']);
                }
                $category = empty($filter['category_id']) ? 0 : intval($filter['category_id']);
                if (!empty($category)) {
                    $query->where('b.category_id', $category);
                }
                // 发布时间
                $startTime = empty($filter['start_time']) ? 0 : strtotime($filter['start_time']);
                $endTime   = empty($filter['end_time']) ? 0 : strtotime($filter['end_time']);
                if (!empty($startTime)) {
                    $query->where('a.published_time', '>=', $startTime);
                }
                if (!empty($endTime)) {
                    $query->where('a.published_time', '<=', $endTime);
                }

                $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
                if (!empty($keyword)) {
                    $query->where('a.post_title', 'like', "%$keyword%");
                }
                
                if (!empty($filter['recommended'])) {
                    $query->where('a.recommended', '=', 1);
                }
                // 区域-省
                if (!empty($filter['province_id'])) {
                    $query->where('a.province_id', '=', $filter['province_id']);
                }
                // 区域-城市
                if (!empty($filter['city_id'])) {
                    $query->where('a.city_id', '=', $filter['city_id']);
                }

                if (!empty($filter['ids'])) {
                    $ids = str_to_arr($filter['ids']);
                    $query->where('a.id', 'in', $ids);
                }
            })->count();
        
        return $data;
    }

}
