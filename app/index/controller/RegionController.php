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
namespace app\index\controller;
use cmf\controller\BaseController;
use think\facade\Db;

class RegionController extends BaseController
{
    /**
     * 获取区域
     * @return [type] [description]
     */
    public function index()
    {
        $word = $this->request->param('word') ? $this->request->param('word') : '';
        $pid  = $this->request->param('pid') ? $this->request->param('pid') : 0;
        $type = $this->request->param('type') ? $this->request->param('type') : 0;

        $map   = [];
        $map[] = ['level', '<', 5];
        if($type) {
            $map[] = ['is_opening', '=', 1];
        }

        if ($word) {
            $map[] = ['name','like','%'.$word.'%'];           
        }

        if ($pid) {
            $map[] = ['parent_id', '=', $pid];
        } else {
            if (!$word) {
                $map[] = ['parent_id', '=', 0];
            }
        }
        
        $result = Db::name('region')->where($map)->field('id,name,code,current_name')->select();
        $this->result($result, 1, 'ok', 'json');
    }
}
// EOF