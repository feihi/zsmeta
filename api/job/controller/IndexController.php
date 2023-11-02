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
namespace api\job\controller;

use api\job\model\JobPostModel;
use cmf\controller\RestBaseController;
use api\job\model\JobTagModel;
use api\job\model\JobApplyModel;

class IndexController extends RestBaseController
{
    protected $tagModel;

    /**
     * 获取岗位列表
     */
    public function index()
    {
        $data['menu'] =  [
            [
                'name'  => '河南企业', 
                'param' => ['province_id' => 16], 
                'page'  => '/pages/search/search'
            ],
            [
                'name' => '外地企业', 
                'param' => ['province_id' => -16],
                'page'  => '/pages/search/search'
            ],
            [
                'name' => '今日主推', 
                'param' => ['recommended' => 1],
                'page' => '/pages/search/search'
            ],
            [
                'name' => '大龄工', 
                'param' => ['category_id' => 3],
                'page' => '/pages/search/search'
            ],
            [
                'name' => '技术工种', 
                'param' => ['category_id' => 4],
                'page' => '/pages/search/search'
            ],
            [
                'name' => '门店导航', 
                'param' => [
                    'latitude' => '34.753676087525044', 
                    'longitude' => '113.64018666911731', 
                    'address' => '郑州市航空港区',
                    'title' => '十年人力' 
                ],
                'page' => '/pages/door_address/door_address'
            ],
            [
                'name' => '加盟代理', 
                'param' => [],
                'page' => '/pages/agency/agency'
            ]
        ];
        // 幻灯片
        $slides = \app\admin\service\ApiService::slides(2); // 2 : app轮播图ID
        if (!empty($slides)) {
            foreach ($slides as $k => &$vo) {
                $vo['image_url'] = cmf_get_image_url($vo['image']);
            }
        }
        
        $data['lunbo'] = $slides;

        // 首页通知
        $applyModel = new JobApplyModel();
        $noticeList = $applyModel->where('delete_time',0)->order('id desc')->limit(5)->select();
        $notices = [];
        if (!empty($noticeList)) {
            foreach ($noticeList as $k => &$vo) {
                $notices[] = [
                    'post_title' => $vo['post_title'],
                    'realname'   => user_name_cut($vo['realname']),
                    'apply_time' => date('m-d', $vo['create_time'])
                ];
            }
        }
        
        $data['notices'] = $notices;

        $this->success('请求成功!', $data);
    }

    /**
     * 获取平台信息
     * 
     * @return [type] [description]
     */
    public function platSet ()
    {
        $key  = $this->request->param('key', '');
        $data = cmf_get_option('plat_set');

        if ($key) {
            $response = htmlspecialchars_decode($data[$key.'_settings']);
        } else {
            $response = $data;
        }
        $this->success('请求成功!', $response);
    }

    /**
     * 获取热门标签列表
     */
    public function hotTags()
    {
        $params                         = $this->request->get();
        $params['where']['recommended'] = 1;
        $data                           = $this->tagModel->getDatas($params);

        if (empty($this->apiVersion) || $this->apiVersion == '1.0.0') {
            $response = $data;
        } else {
            $response = ['list' => $data,];
        }
        $this->success('请求成功!', $response);
    }

}
