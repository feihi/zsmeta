<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2022 http://www.sandbean.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: afei <feoyo@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;
use cmf\controller\AdminBaseController;
use think\facade\Db;

class MapController extends AdminBaseController
{
    /**
     * 地图渲染
     * @return [type] [description]
     */
    public function index ()
    {
        $cityId 	= $this->request->get('city_id', 184); //默认郑州
        $type   	= $this->request->get('type', 1);
        $longitude 	= (float)$this->request->get('longitude', 0);
        $latitude  	= (float)$this->request->get('latitude', 0);
        $lngLatPrefix = $this->request->get('lng_lat_prefix', '');//父页面经纬度字段前缀
        $cityName 	= Db::name('region')->where(['id' => $cityId])->value('name');
        $url 		="http://api.map.baidu.com/geocoder/v2/?callback=renderOption&output=json&address=".$cityName."&ak=KwZhXg0g9C5zZpDIZX895Xfn";
        
        if ($longitude > 0 && $latitude > 0) {

            $rs = \coordpositiontransform\CoordPositionTransform::gcj02_To_Bd09($latitude,$longitude);
            $longitude = $rs['lon'];
            $latitude  = $rs['lat'];
        } else {

            $data = curl_file_get_contents($url);
            $data = str_replace('renderOption&&renderOption(', '', $data);
            $data = str_replace(')', '', $data);
            $data = json_decode($data,true);
            if (!empty($data) && $data['status'] == 0) {
                $longitude = $data['result']['location']['lng'];
                $latitude  = $data['result']['location']['lat'];
            }
        }

        $this->assign('type', $type);
        $this->assign('longitude', $longitude);
        $this->assign('latitude', $latitude);
        $this->assign('lng_lat_prefix', $lngLatPrefix);

    	return $this->fetch();
    }
}