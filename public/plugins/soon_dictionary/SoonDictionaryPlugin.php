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
namespace plugins\soon_dictionary;
use cmf\lib\Plugin;

class SoonDictionaryPlugin extends Plugin
{
    public $info = [
        'name'        => 'SoonDictionary',
        'title'       => '数据词典',
        'description' => '自动生成数据词典插件',
        'status'      => 1,
        'author'      => 'soon',
        'version'     => '1.0'
    ];

    public $hasAdmin = 1; // 插件是否有后台管理界面

    // 插件安装
    public function install()
    {
        return true;
    }

    // 插件卸载
    public function uninstall()
    {
        return true;
    }
}