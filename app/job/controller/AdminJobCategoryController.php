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
namespace app\job\controller;

use app\admin\model\RouteModel;
use app\job\model\JobCategoryPostModel;
use app\job\model\RecycleBinModel;
use cmf\controller\AdminBaseController;
use app\job\model\JobCategoryModel;
use app\admin\model\ThemeModel;


class AdminJobCategoryController extends AdminBaseController
{
    /**
     * 岗位分类列表
     * @adminMenu(
     *     'name'   => '分类管理',
     *     'parent' => 'job/AdminIndex/default',
     *     'display'=> true,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '岗位分类列表',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $content = hook_one('job_admin_category_index_view');

        if (!empty($content)) {
            return $content;
        }

        $jobCategoryModel = new JobCategoryModel();
        $keyword          = $this->request->param('keyword');

        if (empty($keyword)) {
            $categoryTree = $jobCategoryModel->adminCategoryTableTree();
            $this->assign('category_tree', $categoryTree);
        } else {
            $categories = $jobCategoryModel->where('name', 'like', "%{$keyword}%")
                ->where('delete_time', 0)->select();
            $this->assign('categories', $categories);
        }

        $this->assign('keyword', $keyword);

        return $this->fetch();
    }

    /**
     * 添加岗位分类
     * @adminMenu(
     *     'name'   => '添加岗位分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加岗位分类',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function add()
    {
        $content = hook_one('job_admin_category_add_view');

        if (!empty($content)) {
            return $content;
        }

        $parentId            = $this->request->param('parent', 0, 'intval');
        $jobCategoryModel    = new JobCategoryModel();
        $categoriesTree      = $jobCategoryModel->adminCategoryTree($parentId);

        $themeModel        = new ThemeModel();
        $listThemeFiles    = $themeModel->getActionThemeFiles('job/List/index');
        $articleThemeFiles = $themeModel->getActionThemeFiles('job/Article/index');

        $this->assign('list_theme_files', $listThemeFiles);
        $this->assign('article_theme_files', $articleThemeFiles);
        $this->assign('categories_tree', $categoriesTree);
        return $this->fetch();
    }

    /**
     * 添加岗位分类提交
     * @adminMenu(
     *     'name'   => '添加岗位分类提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '添加岗位分类提交',
     *     'param'  => ''
     * )
     */
    public function addPost()
    {
        $jobCategoryModel = new JobCategoryModel();

        $data = $this->request->param();

        $result = $this->validate($data, 'JobCategory');

        if ($result !== true) {
            $this->error($result);
        }

        $result = $jobCategoryModel->addCategory($data);

        if ($result === false) {
            $this->error('添加失败!');
        }

        $this->success('添加成功!', url('AdminJobCategory/index'));
    }

    /**
     * 编辑岗位分类
     * @adminMenu(
     *     'name'   => '编辑岗位分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑岗位分类',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit()
    {

        $content = hook_one('job_admin_category_edit_view');

        if (!empty($content)) {
            return $content;
        }

        $id = $this->request->param('id', 0, 'intval');
        if ($id > 0) {
            $jobCategoryModel = new JobCategoryModel();
            $category            = $jobCategoryModel->find($id)->toArray();

            $categoriesTree = $jobCategoryModel->adminCategoryTree($category['parent_id'], $id);

            $themeModel        = new ThemeModel();
            $listThemeFiles    = $themeModel->getActionThemeFiles('job/List/index');
            $articleThemeFiles = $themeModel->getActionThemeFiles('job/Post/index');

            $routeModel = new RouteModel();
            $alias      = $routeModel->getUrl('job/List/index', ['id' => $id]);

            $category['alias'] = $alias;
            $this->assign($category);
            $this->assign('list_theme_files', $listThemeFiles);
            $this->assign('article_theme_files', $articleThemeFiles);
            $this->assign('categories_tree', $categoriesTree);
            return $this->fetch();
        } else {
            $this->error('操作错误!');
        }

    }

    /**
     * 编辑岗位分类提交
     * @adminMenu(
     *     'name'   => '编辑岗位分类提交',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '编辑岗位分类提交',
     *     'param'  => ''
     * )
     */
    public function editPost()
    {
        $data = $this->request->param();

        $result = $this->validate($data, 'JobCategory');

        if ($result !== true) {
            $this->error($result);
        }

        $jobCategoryModel = new JobCategoryModel();

        $result = $jobCategoryModel->editCategory($data);

        if ($result === false) {
            $this->error('保存失败!');
        }

        $this->success('保存成功!');
    }

    /**
     * 岗位分类选择对话框
     * @adminMenu(
     *     'name'   => '岗位分类选择对话框',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> true,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '岗位分类选择对话框',
     *     'param'  => ''
     * )
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function select()
    {
        $ids              = $this->request->param('ids');
        $selectedIds      = explode(',', $ids);
        $jobCategoryModel = new JobCategoryModel();

        $tpl = <<<tpl
<tr class='data-item-tr'>
    <td>
        <input type='checkbox' class='js-check' data-yid='js-check-y' data-xid='js-check-x' name='ids[]'
               value='\$id' data-name='\$name' \$checked>
    </td>
    <td>\$id</td>
    <td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>
</tr>
tpl;

        $categoryTree = $jobCategoryModel->adminCategoryTableTree($selectedIds, $tpl);

        $categories = $jobCategoryModel->where('delete_time', 0)->select();

        $this->assign('categories', $categories);
        $this->assign('selectedIds', $selectedIds);
        $this->assign('categories_tree', $categoryTree);
        return $this->fetch();
    }

    /**
     * 岗位分类排序
     * @adminMenu(
     *     'name'   => '岗位分类排序',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '岗位分类排序',
     *     'param'  => ''
     * )
     */
    public function listOrder()
    {
        parent::listOrders('job_category');
        $this->success("排序更新成功！", '');
    }

    /**
     * 岗位分类显示隐藏
     * @adminMenu(
     *     'name'   => '岗位分类显示隐藏',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '岗位分类显示隐藏',
     *     'param'  => ''
     * )
     */
    public function toggle()
    {
        $data                = $this->request->param();
        $jobCategoryModel = new JobCategoryModel();
        $ids                 = $this->request->param('ids/a');

        if (isset($data['ids']) && !empty($data["display"])) {
            $jobCategoryModel->where('id', 'in', $ids)->update(['status' => 1]);
            $this->success("更新成功！");
        }

        if (isset($data['ids']) && !empty($data["hide"])) {
            $jobCategoryModel->where('id', 'in', $ids)->update(['status' => 0]);
            $this->success("更新成功！");
        }

    }

    /**
     * 删除岗位分类
     * @adminMenu(
     *     'name'   => '删除岗位分类',
     *     'parent' => 'index',
     *     'display'=> false,
     *     'hasView'=> false,
     *     'order'  => 10000,
     *     'icon'   => '',
     *     'remark' => '删除岗位分类',
     *     'param'  => ''
     * )
     */
    public function delete()
    {
        $jobCategoryModel = new JobCategoryModel();
        $id                  = $this->request->param('id');
        //获取删除的内容
        $findCategory = $jobCategoryModel->where('id', $id)->find();

        if (empty($findCategory)) {
            $this->error('分类不存在!');
        }
        //判断此分类有无子分类（不算被删除的子分类）
        $categoryChildrenCount = $jobCategoryModel->where(['parent_id' => $id, 'delete_time' => 0])->count();

        if ($categoryChildrenCount > 0) {
            $this->error('此分类有子类无法删除!');
        }

        $categoryPostCount = JobCategoryPostModel::where('category_id', $id)->count();

        if ($categoryPostCount > 0) {
            $this->error('此分类有岗位无法删除!');
        }

        $data   = [
            'object_id'   => $findCategory['id'],
            'create_time' => time(),
            'table_name'  => 'job_category',
            'name'        => $findCategory['name'],
            'user_id'     => cmf_get_current_admin_id()
        ];
        $result = $jobCategoryModel
            ->where('id', $id)
            ->update(['delete_time' => time()]);
        if ($result) {
            RecycleBinModel::insert($data);
            $this->success('删除成功!');
        } else {
            $this->error('删除失败');
        }
    }
}
