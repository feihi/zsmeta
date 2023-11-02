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

class JobValidate extends Validate
{
    protected $rule = [
        'post_title'        =>  'require',
	    'post_content'      =>  'require',
	    'categories'        =>  'require'
    ];
    protected $message = [
        'post_title.require'    =>  '岗位标题不能为空',
	    'post_content.require'  =>  '内容不能为空',
	    'categories.require'    =>  '文章分类不能为空'
    ];

    protected $scene = [
        'article'  => [ 'post_title' , 'post_content' , 'categories' ],
        'page' => ['post_title']
    ];
}