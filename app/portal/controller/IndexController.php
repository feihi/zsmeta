<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use app\portal\model\PortalCategoryModel;
use app\portal\model\PortalPostModel;
use think\db\Query;

class IndexController extends HomeBaseController
{
    public function index()
    {
        
        $categories = PortalCategoryModel::order("list_order ASC")
            ->where('delete_time', 0)
            ->where('parent_id', 1)
            ->select()->toArray();

        foreach ($categories as $k => &$category) {
            $categories[$k]['first_article_id'] = PortalPostModel::alias('post')
                ->field('post.id')
                ->join('portal_category_post relation', 'post.id = relation.post_id')
                ->where('relation.category_id', '=', $category['id'])
                ->value('post.id');
        }
        
        $platSet = cmf_get_option('plat_set');
        $this->assign("plat_set", $platSet);
        $this->assign("categories", $categories);
        // halt($categories);
        return $this->fetch(':index');
    }

    public function contact()
    {
        return $this->fetch(':contact');
    }

    public function embed()
    {
        return $this->fetch(':embed');
    }

    public function baas()
    {
        return $this->fetch(':baas');
    }

    public function fund_services()
    {
        return $this->fetch(':fund-services');
    }

    public function private_concierge_services()
    {
        return $this->fetch(':fund-services');
    }
}
