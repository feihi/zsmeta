<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\job\service;

use app\job\model\JobApplyModel;
use think\db\Query;

class ApplyService
{
    /**
     * 报名查询
     * @param      $filter
     * @param bool $isPage
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function adminApplyList(array $filter, bool $isPage = false) : object
    {
        $field = 'a.*';

        $applyModel = new JobApplyModel();
        $applyQuery = $applyModel->alias('a');

        $category = empty($filter['category']) ? 0 : intval($filter['category']);
        if (!empty($category)) {
            $applyQuery = $applyQuery->join('job_category_post c', 'c.post_id = a.post_id');
            $field = 'a.*,c.category_id';
        }

        $data = $applyQuery->field($field)
            ->where('a.delete_time', 0)
            ->where(function (Query $query) use ($filter) {

                $category = empty($filter['category']) ? 0 : intval($filter['category']);
                if (!empty($category)) {
                    $query->where('c.category_id', $category);
                }

                $startTime = empty($filter['start_time']) ? 0 : strtotime($filter['start_time']);
                $endTime   = empty($filter['end_time']) ? 0 : strtotime($filter['end_time']);
                if (!empty($startTime)) {
                    $query->where('a.create_time', '>=', $startTime);
                }
                if (!empty($endTime)) {
                    $query->where('a.create_time', '<=', $endTime);
                }

                $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
                if (!empty($keyword)) {
                    $query->where('a.post_title|a.realname|a.mobile|a.idcard', 'like', "%$keyword%");
                }

                $status = isset($filter['status']) ? $filter['status'] : -1;
                if ($status > -1) {
                    $query->where('a.status', $status);
                }
                
            })
            ->order('a.id', 'DESC')
            ->paginate(10);

        return $data;

    }

}
