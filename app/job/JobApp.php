<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------

namespace app\job;

class JobApp
{

    // 应用安装
    public function install()
    {
        return true; //安装成功返回true，失败false
    }

    // 应用卸载
    public function uninstall()
    {
        return true; //卸载成功返回true，失败false
    }

}
