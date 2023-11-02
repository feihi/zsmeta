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
namespace plugins\soon_dictionary\controller;

use cmf\controller\PluginBaseController;
use think\facade\Db;

class AdminIndexController extends PluginBaseController
{

    protected function initialize()
    {
        parent::initialize();
        $adminId = cmf_get_current_admin_id(); //获取后台管理员id，可判断是否登录
        if (!empty($adminId)) {
            $this->assign('admin_id', $adminId);
        }
    }

    /**
     * Dictionary
     * @package plugins\soon_dictionary\controller
     * @adminMenuRoot(
     *     'name'   =>'数据字典',
     *     'action' =>'default',
     *     'parent' => 'admin/Plugin/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   =>'dashboard',
     *     'remark' =>'自动生成数据词典'
     *     'param'  => ''
     * )
     */
    public function index()
    {
        $tables = Db::query("show tables");
        $dictionary = [];
        $databasename = config('database.connections.mysql.database');
        
        !$tables && $this->error("获取数据表数据失败！");

        // 取得所有的表名
        foreach($tables as $k => $v)
        { 
            $dictionary[]['TABLE_NAME'] = $v['Tables_in_'.$databasename];
        }

        foreach($dictionary as $k => $v)
        {
            $comment = Db::query("SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE table_name = '".$v['TABLE_NAME']."' AND table_schema = '".$databasename."'");

            foreach($comment as $kc => $vc)
            {
                $dictionary[$k]['TABLE_COMMENT'] = $vc['TABLE_COMMENT'];
            }

            $column = Db::query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '".$v['TABLE_NAME']."' AND table_schema = '".$databasename."'");

            $fields = array ();
            foreach($column as $kn=>$vn)
            {
                $fields[] = $vn;
            }

            $dictionary[$k]['COLUMN'] = $fields;
        }
        
        $html = '';
        foreach($dictionary as $k => $v)
        {
            $html .= "<h4>{$v['TABLE_NAME']} {$v['TABLE_COMMENT']}</h4>";
            $html .= "<table class=\"table table-bordered\"><thead><tr><th>字段名</th><th>数据类型</th><th>默认值</th><th>允许非空</th><th>自动递增</th><th>备注</th></tr></thead><tbody>";
            foreach($v['COLUMN'] as $vc)
            {
                $html .= "<tr>";
                $html .= "<td>".(isset($vc['COLUMN_NAME']) ? $vc['COLUMN_NAME'] : "")."</td>";
                $html .= "<td>".(isset($vc['COLUMN_TYPE']) ? $vc['COLUMN_TYPE'] : "")."</td>";
                $html .= "<td>".(isset($vc['COLUMN_DEFAULT']) ? $vc['COLUMN_DEFAULT'] : "")."</td>";
                $html .= "<td>".(isset($vc['IS_NULLABLE']) ? $vc['IS_NULLABLE'] : "")."</td>";
                $html .= "<td>".(isset($vc['EXTRA']) ? $vc['EXTRA'] : "")."</td>";
                $html .= "<td>".(isset($vc['COLUMN_COMMENT']) ? $vc['COLUMN_COMMENT'] : "")."</td>";
                $html .= "</tr>";
            }
            $html .= "</tbody></table>";
        }

        $this->assign('html',$html);
        return $this->fetch('/admin_index');
    }
}
//EOF