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
namespace app\admin\validate;

use app\admin\model\RouteModel;
use think\Validate;

class PlatSetValidate extends Validate
{
    protected $rule = [
        'mission_settings'    => 'require',
        'vision_settings'     => 'require',
        'about_us_settings'   => 'require',
    ];

    protected $message = [
        'mission_settings'             => 'Mission不能为空',
        'vision_settings'         => 'Vision不能为空',
        'about_us_settings'          => 'About us 不能为空',
    ];

}
