<?php
/**
 * Created by SublimeText3.
 * User: soon
 * Date: 2022/06/22
 * Time: 18:23
 */
namespace plugins\job_analysis;

use app\job\model\JobPostModel;
use cmf\lib\Plugin;

class JobAnalysisPlugin extends Plugin
{
    public $info     = [
        'name'        => 'JobAnalysis',
        'title'       => '岗位数据分析',
        'description' => '岗位数据分析',
        'status'      => 1,
        'author'      => 'soon',
        'version'     => '1.0.0'
    ];
    public $hasAdmin = 0;//插件是否有后台管理界面

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

    public function adminDashboard()
    {
        $today          = strtotime(date('Y-m-d'));
        $jobPostModel   = new JobPostModel();
        $where          = [
            ['post.post_status', '=', 1],
            ['post.delete_time', '=', 0]
        ];

        //总发布岗位数量
        $totalCount = $jobPostModel->alias('post')->where($where)->count('id');
        //当天发布岗位数量
        $todayCount = $jobPostModel->alias('post')->where(array_merge($where, [['post.published_time', '>=', $today], ['post.published_time','<=', time()]]))->count('id');
        //未发布数量
        $noPublishedCount = $jobPostModel->alias('post')->where(array_merge($where, [['post.post_status' ,'=',0]]))->count('id');
        //未发布岗位
        $noPublishedPosts = $jobPostModel->alias('post')->field('post.id,post.post_title,post.create_time')->where(array_merge($where, [['post.post_status' ,'=',0]]))->limit(5)->select()->toArray();
        //浏览量
        $totalViewCount = $jobPostModel->alias('post')->where($where)->sum('post_hits');
        //岗位浏览量Top10
        $postViewCount = $jobPostModel->alias('post')->field('id,post_title,sum(post_hits) as view_count')->where($where)->group('id')->order('view_count desc')->limit(5)->select()->toArray();
        //栏目岗位数量前10条
        $categoryCount = $jobPostModel->alias('post')->join('job_category_post category_post', 'post.id = category_post.post_id')->join('job_category category', 'category.id = category_post.post_id')->field('count(post.id) as count,category_post.category_id,category.name')->where($where)->group('category_post.category_id')->order('count desc')->limit(10)->select()->toArray();
        $map           = ['category' => [], 'data' => []];
        foreach ($categoryCount as $value) {
            $map['category'][] = $value['name'];
            $map['data'][]     = $value['count'];
        }
        $data = [
            'total_count'        => $totalCount,
            'today_count'        => $todayCount,
            'category_count'     => $categoryCount,
            'map_data'           => $map,
            'no_published_count' => $noPublishedCount,
            'total_view_count'   => $totalViewCount,
            'post_view_count'    => $postViewCount,
            'no_published_posts' => $noPublishedPosts
        ];
        $this->assign($data);

        return [
            'width'  => 12,
            'view'   => $this->fetch('widget'),
            'plugin' => 'PostAnalysis'
        ];
    }
}