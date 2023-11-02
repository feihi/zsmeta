<?php
// +----------------------------------------------------------------------
// | LoginTime [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 Tangchao All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tangchao <79300975@qq.com>
// +----------------------------------------------------------------------
namespace plugins\login_time\controller;

use think\facade\Db;
use cmf\controller\PluginBaseController;

class AdminIndexController extends PluginBaseController
{

    function _initialize()
    {
        $adminId = cmf_get_current_admin_id();
        if (!empty($adminId)) {
            $this->assign("admin_id", $adminId);
        } else {
            $this->error('未登录');
        }
    }

    function index()
    {
        $time = \think\facade\Config::get('session')['expire'];
        $this->assign("time", $time);
        return $this->fetch('/admin_index');
    }

    function time()
    {
        $time  = $this->request->param('time');
        preg_match_all('/\d+/',$time,$arr);
        $time = join('',$arr[0]);

        $logintime = [
            // session name
            'name'           => 'PHPSESSID',
            // SESSION_ID的提交变量,解决flash上传跨域
            'var_session_id' => '',
            // 驱动方式 支持file cache
            'type'           => 'file',
            // 存储连接标识 当type使用cache的时候有效
            'store'          => null,
            // 过期时间
            'expire'         => $time,
            // 前缀
            'prefix'         => '',
        ];

        cmf_set_dynamic_config(['session' => $logintime]);
        $this->success('配置成功');
    }

}
