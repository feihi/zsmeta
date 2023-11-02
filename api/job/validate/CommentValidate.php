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

class CommentValidate extends Validate
{
    protected $rule = [
        'post_id'    =>  'require|integer',
        'parent_id'  =>  'integer',
        'content'    =>  'require'
    ];

    protected $message = [
        'content.require'  =>  '留言内容不能为空',
	    'post_id.require'  =>  '岗位ID不能为空',
        'post_id.integer'  =>  '岗位信息不正确',
        'parent_id.integer' =>  '留言ID不正确',
    ];
}