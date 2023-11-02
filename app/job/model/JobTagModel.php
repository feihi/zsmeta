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
namespace app\job\model;

use think\Model;

class JobTagModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'job_tag';

    public static   $STATUS = array(
        0=>"未启用",
        1=>"已启用",
    );
}