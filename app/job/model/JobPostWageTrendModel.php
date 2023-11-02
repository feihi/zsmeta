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
use think\db\Query;

class JobPostWageTrendModel extends Model
{
    CONST WAGE = 1;
    CONST RETURN_FEE = 2;
    CONST GENERAL_WAGE = 3;
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'job_post_wage_trend';

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

    public function getList (array $filter) : array
    {
        $field = 'a.*';

        $data = $this->alias('a')->field($field)
            ->where('a.delete_time', 0)
            ->where('a.post_id', $filter['post_id'])
            ->where(function (Query $query) use ($filter) {

                $startMonth = empty($filter['start_month']) ? '' : strtotime($filter['start_month']);
                $endMonth   = empty($filter['end_month']) ? '' : strtotime($filter['end_month']);
                if (!empty($startMonth)) {
                    $query->where('a.month', '>=', $startMonth);
                }
                if (!empty($endMonth)) {
                    $query->where('a.month', '<=', $endMonth);
                }

                $type = empty($filter['type']) ? '' : $filter['type'];
                if (!empty($type)) {
                    $query->where('a.type', '=', $type);
                }
            })
            ->order('a.month', 'ASC')
            ->limit(12)
            ->select()
            ->toArray();

        return $data;
    }
}
