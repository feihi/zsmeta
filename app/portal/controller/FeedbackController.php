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
use app\portal\model\FeedbackModel;

class FeedbackController extends HomeBaseController
{
    /**
     * 标签
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $id             = $this->request->param('id');

        $feedbackModel = new FeedbackModel();

        if(is_numeric($id)){
            $tag = $feedbackModel->where('id', $id)->where('status', 1)->find();
        }else{
            $tag = $feedbackModel->where('name', $id)->where('status', 1)->find();
        }


        if (empty($tag)) {
            abort(404, '标签不存在!');
        }

        $this->assign('tag', $tag);

        return $this->fetch('/tag');
    }

    /**
     * 标签
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $arrData = $this->request->param();
        
        $arrData['create_time'] = time();
        $arrData['delete_time'] = 0;

        // halt($arrData);
        $feedbackModel = new FeedbackModel();
        $feedbackModel->save($arrData);

        $this->success(lang("SAVE_SUCCESS"));
    }
}
