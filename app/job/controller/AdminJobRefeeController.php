<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-present http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
namespace app\job\controller;

use cmf\controller\AdminBaseController;

class AdminJobRefeeController extends AdminBaseController
{

    /**
     * 返费名单
     * @adminMenu(
     *     'name'   => '返费名单',
     *     'parent' => 'job/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '岗位返费名单',
     *     'param'  => ''
     * )
     */
    public function info()
    {
        $content = hook_one('admin_job_refee_view');

        if (!empty($content)) {
            return $content;
        }

        $refeeInfo = cmf_get_option('job_refee_set');
        $this->assign("refee_info", $refeeInfo);

        return $this->fetch();
    }

    /**
     * 返费名单设置提交
     * @adminMenu(
     *     'name'   => '返费名单设置提交',
     *     'parent' => 'info',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '返费名单设置提交',
     *     'param'  => ''
     * )
     */
    public function infoPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param('refee_info/a');
            $result = $this->validate($data, 'jobRefee');
            if ($result !== true) {
                $this->error($result);
            }
            cmf_set_option('job_refee_set', $data);

            $this->success("保存成功！", '');

        }
    }

}
