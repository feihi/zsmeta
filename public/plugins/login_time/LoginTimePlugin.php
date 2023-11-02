<?php
// +----------------------------------------------------------------------
// | LoginTime [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 soon All rights reserved.
// +----------------------------------------------------------------------
// | Author: soon <feoyo@qq.com>
// +----------------------------------------------------------------------
namespace plugins\login_time;
use cmf\lib\Plugin;
class LoginTimePlugin extends Plugin
{

    public $info = [
        'name'        => 'LoginTime',
        'title'       => '登陆状态时长控制',
        'description' => '登陆状态时长控制',
        'status'      => 1,
        'author'      => 'soon',
        'version'     => '1.0',
        'demo_url'    => 'http://www.sandbean.com',
        'author_url'  => 'http://www.sandbean.com'
    ];

    public $hasAdmin = 1;

    public function install()
    {
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
            'expire'         => 86400,
            // 前缀
            'prefix'         => '',
        ];
        
        cmf_set_dynamic_config(['session' => $logintime]);
        return true;
    }

    public function uninstall()
    {
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
            'expire'         => 1440,
            // 前缀
            'prefix'         => '',
        ];
        
        cmf_set_dynamic_config(['session' => $logintime]);
        return true;
    }
}