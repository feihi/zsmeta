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
namespace app\job\validate;

use app\admin\model\RouteModel;
use think\Validate;

class JobRefeeValidate extends Validate
{
    protected $rule = [
        'job_refee_title'        => 'require',
        'job_refee_title'        => 'require',
    ];

    protected $message = [
        'job_refee_title'         => '标题不能为空',
        'job_refee_title'         => '链接不能为空',
    ];
}
