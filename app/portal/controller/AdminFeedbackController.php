<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:kane < chengjin005@163.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use app\portal\model\FeedbackModel;
use cmf\controller\AdminBaseController;
use think\facade\Db;

/**
 * Class AdminFeedbackController 留言管理控制器
 * @package app\portal\controller
 */
class AdminFeedbackController extends AdminBaseController
{
    /**
     * 留言管理
     * @adminMenu(
     *     'name'   => '留言',
     *     'parent' => 'portal/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '留言',
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $content = hook_one('portal_admin_feedback_index_view');

        if (!empty($content)) {
            return $content;
        }

        $feedbackModel = new FeedbackModel();
        $feedbacks     = $feedbackModel->paginate();

        $this->assign("feedbacks", $feedbacks);
        $this->assign('page', $feedbacks->render());
        return $this->fetch();
    }

    /**
     * 删除留言
     * @adminMenu(
     *     'name'   => '删除留言',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除留言',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $intId = $this->request->param("id", 0, 'intval');

        if (empty($intId)) {
            $this->error(lang("NO_ID"));
        }
        $feedbackModel = new FeedbackModel();

        $feedbackModel->where('id' , $intId)->delete();
        $this->success(lang("DELETE_SUCCESS"));
    }
}
