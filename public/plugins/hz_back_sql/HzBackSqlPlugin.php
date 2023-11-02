<?php
// +----------------------------------------------------------------------
// | Author: heizai <876555425@qq.com>
// +----------------------------------------------------------------------
namespace plugins\hz_back_sql;
use cmf\lib\Plugin;

/**
 * 数据库备份 (适配thinkcmf6.0/thinkphp6)
 * Class HzBackSqlPlugin
 * @package plugins\hz_back_sql
 */
class HzBackSqlPlugin extends Plugin
{

	public $info = [
		'name'        => 'HzBackSql',//Demo插件英文名，改成你的插件英文就行了
		'title'       => '数据备份',
		'description' => '数据备份,还原',
		'status'      => 1,
		'author'      => 'soon',
		'version'     => '1.0',
		'demo_url'    => '',
		'author_url'  => ''
	];

	public $hasAdmin = 1;//插件是否有后台管理界面

	// 插件安装
	public function install()
	{

		return true;//安装成功返回true，失败false
	}

	// 插件卸载
	public function uninstall()
	{

		return true;//卸载成功返回true，失败false
	}
}