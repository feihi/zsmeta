<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2019 http://www.sandbean.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: afei <feoyo@qq.com>
// +----------------------------------------------------------------------

if (!function_exists('_get_value'))
{
    /**
     * 获取数组参数取值 @author wpf
     */
    function _get_value($data, $key = '', $default = null, $filter = '')
    {
        return request()->input($data, $key, $default, $filter);
    }
}

if (!function_exists('_get_val'))
{
    /**
     * 获取数据及赋予默认值
     * 
     * @return [type] [description]
     */
    function _get_val ($data, $key, $default = '', $format = '')
    {
        if (isset($data[$key]) && $data[$key]) {
            $return = $data[$key];
        } else {
            $return = $default;
        }

        if (!empty($format) && function_exists($format)) {
            $return = $format($return);
        }
        return $return;
    }
}

if (!function_exists('user_name_cut'))
{
    /**
     * 只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）
     * @param string $username 姓名
     * @param string $repeatStr 替换的字符
     * @param string $encode 字符编码
     * @return string 格式化后的姓名
     */
    function user_name_cut($username, $repeatStr = '*', $encode = 'utf-8')
    {
        if (empty($username)) {
            return '***';
        }
        $length   = mb_strlen($username,$encode);
        $firstStr = mb_substr($username, 0, 1, $encode);
        $lastStr  = mb_substr($username, -1, 1, $encode);
        return $length == 2 ? $firstStr . str_repeat($repeatStr, $length - 1) : $firstStr . str_repeat($repeatStr, $length - 2) . $lastStr;
    }
}

if (!function_exists('get_recent_year_months')) {
    /**
     * 获取最近一年的月份
     * 
     * @return [type] [description]
     */
    function get_recent_year_months (string $format='Y-m') : array
    {
        $z = date('Y-m');
        $a = date('Y-m', strtotime('-11 months'));
        $begin = new DateTime($a);
        $end   = new DateTime($z);
        $end   = $end->modify('+1 month');
        
        $interval  = new DateInterval('P1M');
        $daterange = new DatePeriod($begin, $interval, $end);
        $months = array();
        
        foreach ($daterange as $key => $date) {
            array_push($months, $date->format($format));
        }

        return $months;
    }
}
