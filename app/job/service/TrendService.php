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

use app\job\model\JobPostModel;
use app\job\model\JobPostWageTrendModel;
use think\db\Query;

class TrendService
{
    /**
     * 趋势查询
     * @param      $filter
     * @param bool $isPage
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getPostWageTrend(int $postId, array $filter = []) : array
    {
        $jobPostModel = new JobPostModel();
        $post = $jobPostModel->where('id', $postId)->find();

        $wage       = $post['hourly_wage'];
        $return_fee = $post['return_fee'];
        $generaral_wage_fee = $post['monthly_wage_max'];

        $model  = new JobPostWageTrendModel();
        $filterWage = ['post_id' =>  $postId, 'type' => $model::WAGE];
        $wageData   = $model->getList($filterWage);
        
        $filterRefee = ['post_id' =>  $postId, 'type' => $model::RETURN_FEE];
        $refeeData   = $model->getList($filterRefee);

        $filterGeneralWage = ['post_id' =>  $postId, 'type' => $model::GENERAL_WAGE];
        $generalWageData   = $model->getList($filterGeneralWage);

        $wageList  = array_column($wageData, NULL, 'month');
        $refeeList = array_column($refeeData, NULL, 'month');
        $generalWageList = array_column($generalWageData, NULL, 'month');
        
        $recentYearMonths = get_recent_year_months('Ym');

        $result = array();
        foreach ($recentYearMonths as $k => $vo) {
            if (isset($wageList[$vo]) && $wageList[$vo]) {
                $result['wage_record'][]  =  $wageList[$vo]['amount'];
                $result['wage_ids'][]     =  $wageList[$vo]['id'];
            } else {
                $result['wage_record'][]  =  $wage;
                $result['wage_ids'][]     =  0.00;
            }

            if (isset($refeeList[$vo]) && $refeeList[$vo]) {
                $result['refee_record'][]  =  $refeeList[$vo]['amount'];
                $result['refee_ids'][]     =  $refeeList[$vo]['id'];
            } else {
                $result['refee_record'][]  =  $return_fee;
                $result['refee_ids'][]     =  0.00;
            }

            if (isset($generalWageList[$vo]) && $generalWageList[$vo]) {
                $result['general_wage_record'][]  =  $generalWageList[$vo]['amount'];
                $result['general_wage_ids'][]     =  $generalWageList[$vo]['id'];
            } else {
                $result['general_wage_record'][]  =  $generaral_wage_fee;
                $result['general_wage_ids'][]     =  0.00;
            }
        }
        
        return $result;
    }

    /**
     * 趋势设置
     * @param      $data
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function setPostWageTrend(array $data) : bool
    {
        $postId   = $data['post_id'];
        $wageData = $data['post_id'];
        $month    = $data['month'];
        $wageData  = $data['wage_record'];
        $refeeData = $data['refee_record'];
        $generalWageData = $data['general_wage_record'];

        $model  = new JobPostWageTrendModel();

        $list = [];
        foreach ($month as $k => $vo) {
            $list[] = ['post_id' => $postId, 'month' => $vo, 'amount' => $wageData[$k], 'type' => $model::WAGE];
            $list[] = ['post_id' => $postId, 'month' => $vo, 'amount' => $refeeData[$k], 'type' => $model::RETURN_FEE];
            $list[] = ['post_id' => $postId, 'month' => $vo, 'amount' => $generalWageData[$k], 'type' => $model::GENERAL_WAGE];
        }

        $saveData   = array();
        $saveResult = $updateResult = array();
        foreach ($list as $k => $vo) {
            
            $exist = $model->where(['post_id' => $vo['post_id'], 'month' => $vo['month'], 'type' => $vo['type']])->find();
            if (!empty($exist)) {
                $updateResult[] = $model->where('id', $exist['id'])->update(['amount' => $vo['amount']]);
            } else {
                $saveData[] = $vo;
            }
        }
        
        if (!empty($saveData)) {
            $saveResult = $model->saveAll($saveData);
        }

        $jobPostModel = new JobPostModel();
        $postUptData  = [
            'hourly_wage' => $wageData[count($wageData)-1],
            'return_fee'  => $refeeData[count($refeeData)-1]
        ];
        $jobPostModel->where('id', $postId)->update($postUptData);
        
        return $saveResult || $updateResult;
    }

}
