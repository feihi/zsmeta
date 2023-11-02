<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-present http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\RouteModel;
use app\admin\model\UserModel;
use cmf\controller\AdminBaseController;

class PlatSetController extends AdminBaseController
{

    /**
     * 平台信息
     * @adminMenu(
     *     'name'   => '平台信息',
     *     'parent' => 'admin/Setting/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 5,
     *     'icon'   => '',
     *     'remark' => '平台信息管理',
     *     'param'  => ''
     * )
     */
    public function info()
    {
        $content = hook_one('admin_plat_set_view');

        if (!empty($content)) {
            return $content;
        }

        $platSet = cmf_get_option('plat_set');
        $this->assign("plat_set", $platSet);

        return $this->fetch();
    }

    /**
     * 平台信息设置提交
     * @adminMenu(
     *     'name'   => '平台信息设置提交',
     *     'parent' => 'info',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '平台信息设置提交',
     *     'param'  => ''
     * )
     */
    public function infoPost()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param('plat_set/a');
            $result = $this->validate($data, 'PlatSet');
            if ($result !== true) {
                $this->error($result);
            }
            cmf_set_option('plat_set', $data);

            $this->success("保存成功！", '');

        }
    }

}
