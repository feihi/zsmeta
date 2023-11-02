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
namespace app\job\validate;

use think\Validate;

class JobPostValidate extends Validate
{
    protected $rule = [
        'categories' => 'require',
        'post_title' => 'require',
        'hourly_wage' => 'require',
        "monthly_wage_min" => 'require',
        "monthly_wage_max" => 'require',
        "age_min" => 'require',
        "age_max" => 'require',
        "post_keywords" => 'require',
        "advantage_keywords" => 'require',
        "post_excerpt" => 'require',
        "post_intro" => 'require',
        "compensation_benefit" => 'require',
        "admission_requirement" => 'require',
        "company_intro" => 'require',
        "province_id" => 'require',
        "city_id" => 'require',
        "county_id" => 'require',
        "address" => 'require',
        "longitude" => 'require',
        "latitude" => 'require',
        "entry_process" => 'require',
        "kindly_reminder" => 'require',
        "published_time" => 'require',
        "customer_name" => 'require',
        "customer_weixin" => 'require',
        "customer_hotline" => 'require',
    ];

    protected $message = [
        'categories.require' => '请指定岗位分类！',
        'post_title.require' => '岗位名称不能为空！',
        'hourly_wage.require' => '岗位时薪不能为空！',
        "monthly_wage_min.require" => '岗位起薪不能为空！',
        "monthly_wage_max.require" => '岗位最高薪不能为空！',
        "age_min.require" => '岗位最小年龄不能为空！',
        "age_max.require" => '岗位最大年龄不能为空！',
        "post_keywords.require" => '岗位标签不能为空！',
        "advantage_keywords.require" => '企业优势标签不能为空！',
        "post_excerpt.require" => '岗位描述不能为空！',
        "post_intro.require" => '岗位介绍不能为空！',
        "compensation_benefit.require" => '岗位薪资福利不能为空！',
        "admission_requirement.require" => '录取条件福利不能为空！',
        "company_intro.require" => '企业介绍不能为空！',
        "province_id.require" => '企业地址省份不能为空！',
        "city_id.require" => '企业地址城市不能为空！',
        "county_id.require" => '企业地址县区不能为空！',
        "address.require" => '企业详细地址不能为空！',
        "longitude.require" => '地址坐标不能为空！',
        "latitude.require" => '地址坐标不能为空！',
        "entry_process.require" => '入职流程不能为空！',
        "kindly_reminder.require" => '温馨提示不能为空！',
        "customer_name.require" => '客服姓名不能为空！',
        "customer_weixin.require" => '客服微信不能为空！',
        "customer_hotline.require" => '客服电话不能为空！',
    ];

    protected $scene = [
//        'add'  => ['user_login,user_pass,user_email'],
//        'edit' => ['user_login,user_email'],
    ];
}