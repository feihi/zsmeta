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

if (!function_exists('curl_file_get_contents')) {
    /**
     * curl读取远程文件
     * @param string $url
     * @param int $timeout
     * @param boolean $ssl
     */
    function curl_file_get_contents($url, $timeout = 5000, $ssl = false)
    {
        if (function_exists("curl_init")) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            if ($ssl)
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            if (isset($_SERVER['HTTP_USER_AGENT']))
                curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $r = curl_exec($ch);
            curl_close($ch);
        } else {
            $r = @file_get_contents($url);
        }

        return $r;
    }
}

if (!function_exists('hide_mobile_part')) {
    /**
     * 隐藏部分手机号
     * 
     * @param $url
     * @param array $header
     * @return mixed
     */
    function hide_mobile_part($phone)
    {
        return substr_replace($phone, '****', 3, 4);
    }
}

if (!function_exists('curl_post_contents')) {
    /**
     * curl POST文件
     * @param type $url
     * @param type $postField
     * @param type $timeout
     * @return type
     */
    function curl_post_contents($url, $postField, $timeout = 30)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postField));
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if (isset($_SERVER['HTTP_USER_AGENT']))
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }
}

if (!function_exists('zuobiao_baidu_to_gcj')) {
    /**
     * 百度坐标 转换为 国测局坐标
     * @param string $longitude 百度的 经度
     * @param string $latitude  百度的 纬度
     * @return boole/array
     */
    function zuobiao_baidu_to_gcj($longitude, $latitude)
    {
        if (!empty($longitude) && empty(!$longitude)) {
            $request_url = "http://api.map.baidu.com/geoconv/v1/?coords=" . $longitude . ","
                . $latitude . "&from=5&to=3&ak=KwZhXg0g9C5zZpDIZX895Xfn";
            $json_res = curl_file_get_contents($request_url);
            $res_data = json_decode($json_res, true);
            if ($res_data['status'] == 0) {
                $gcj_longitude = $res_data['result'][0]['x'];
                $gcj_latitude = $res_data['result'][0]['y'];
                return ['longitude' => $gcj_longitude, 'latitude' => $gcj_latitude];
            }
        }
        return false;
    }
}

if (!function_exists('http_async')) {
    /**
     * 异步请求方法
     * @param string $url 请求的http地址
     * @param string $data  请求的参数 post
     * @return json
     */
    function http_async($url, $data)
    {
        //$url = '';//接受curl请求的地址
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type:application/json; charset=utf-8"));
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);   // 注意，毫秒超时一定要设置这个 
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1500);  // 超时时间200毫秒         
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //post方式数据为json格式
        curl_setopt($ch, CURLOPT_TIMEOUT, 1); //设置超时时间为1s

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

if (!function_exists('get_age_by_id')) {
    /**
     * 根据身份证计算年龄，性别
     * @access    public
     * @param     int       $id    
     */
    function get_age_by_id ($id) : array
    {
        $id = trim($id);    
        if(empty($id)) return false; 
        // 获取出生年月日(xxxx-xx-xx)
        $birthday = date('Y-m-d', strtotime(substr($id,6,8)));
        
        $year = date('Y'); $month = date('m'); $day = date('d');

        if (substr($month,0,1)==0) $month = substr($month,1);
        if (substr($day,0,1)==0) $day = substr($day,1);

        $arr = explode('-', $birthday);
        $age = $year - $arr[0];

        if($month < $arr[1]) {
            $age = $age-1;
        } elseif ($month == $arr[1] && $day < $arr[2]) {
            $age = $age-1;
        }

        $data['nianling'] = $age; 
        
        // 获取性别
        $sexint = (int)substr($id, 16, 1);
        $data['xingbie'] = $sexint%2===0 ? 2 : 1;
        return $data; 
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

// EOF