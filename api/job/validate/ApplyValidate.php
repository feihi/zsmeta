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
namespace api\job\validate;

use think\Validate;

class ApplyValidate extends Validate
{
    protected $rule = [
        'realname'    =>  'require|chsAlpha',
	    'mobile'      =>  'require|mobile',
	    'idcard'      =>  'require|idCard',
        'post_id'     =>  'require|integer',
    ];

    protected $message = [
        'realname.require' =>  '姓名不能为空',
	    'mobile.require'   =>  '手机号不能为空',
        'idcard.require'   =>  '身份证不能为空',
	    'post_id.require'  =>  '岗位ID不能为空',
        'realname.chsAlpha' =>  '姓名格式有误',
        'mobile.mobile'    =>  '手机号不正确',
        'idcard.idCard'    =>  '身份证号码不正确',
        'post_id.integer'  =>  '岗位信息不正确',
    ];
}