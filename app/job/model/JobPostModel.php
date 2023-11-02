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

use app\admin\model\RouteModel;
use app\admin\model\RegionModel;
use think\Model;

/**
 * @property mixed id
 */
class JobPostModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'job_post';

    protected $type = [
        'more' => 'array',
    ];

    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;

    /**
     * 关联 user表
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('UserModel', 'user_id');
    }

    /**
     * 关联分类表
     * @return \think\model\relation\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany('JobCategoryModel', 'job_category_post', 'category_id', 'post_id');
    }

    /**
     * 关联标签表
     * @return \think\model\relation\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany('JobTagModel', 'job_tag_post', 'tag_id', 'post_id');
    }

    /**
     * published_time 自动完成
     * @param $value
     * @return false|int
     */
    public function setPublishedTimeAttr($value)
    {
        return strtotime($value);
    }

    /**
     * 后台管理添加岗位
     * @param array        $data       岗位数据
     * @param array|string $categories 岗位分类 id
     * @return $this
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function adminAddPost($data, $categories)
    {
        $data['user_id'] = cmf_get_current_admin_id();

        if (!empty($data['more']['thumbnail'])) {
            $data['more']['thumbnail'] = cmf_asset_relative_url($data['more']['thumbnail']);
            $data['thumbnail']         = $data['more']['thumbnail'];
        }

        if (!empty($data['more']['audio'])) {
            $data['more']['audio'] = cmf_asset_relative_url($data['more']['audio']);
        }

        if (!empty($data['more']['video'])) {
            $data['more']['video'] = cmf_asset_relative_url($data['more']['video']);
        }
        // 坐标转换
        $location = \coordpositiontransform\CoordPositionTransform::bd09_To_Gcj02(floatval($data['latitude']),floatval($data['longitude']));
        $data['gcj02_latitude']  = $location['lat'];
        $data['gcj02_longitude'] = $location['lon'];
        
        $this->save($data);

        if (is_string($categories)) {
            $categories = explode(',', $categories);
        }

        $this->categories()->save($categories);

        $data['post_keywords'] = str_replace('，', ',', $data['post_keywords']);
        $data['advantage_keywords'] = str_replace('，', ',', $data['post_keywords']);

        $keywords = explode(',', $data['post_keywords']);

        $this->addTags($keywords, $this->id);
        $this->syncRegionOpenStatus($data);

        return $this;

    }

    /**
     * 后台管理编辑岗位
     * @param array        $data       岗位数据
     * @param array|string $categories 岗位分类 id
     * @return $this
     * @throws \think\Exception
     */
    public function adminEditPost($data, $categories)
    {

        unset($data['user_id']);

        if (!empty($data['more']['thumbnail'])) {
            $data['more']['thumbnail'] = cmf_asset_relative_url($data['more']['thumbnail']);
            $data['thumbnail']         = $data['more']['thumbnail'];
        }

        if (!empty($data['more']['audio'])) {
            $data['more']['audio'] = cmf_asset_relative_url($data['more']['audio']);
        }

        if (!empty($data['more']['video'])) {
            $data['more']['video'] = cmf_asset_relative_url($data['more']['video']);
        }
        
        // 坐标转换
        $location = \coordpositiontransform\CoordPositionTransform::bd09_To_Gcj02(floatval($data['latitude']),floatval($data['longitude']));
        $data['gcj02_latitude']  = $location['lat'];
        $data['gcj02_longitude'] = $location['lon'];

        unset($data['categories']);

        $post = self::find($data['id']);

        $post->save($data);

        if (is_string($categories)) {
            $categories = explode(',', $categories);
        }

        $oldCategoryIds        = $post->categories()->column('category_id');
        $sameCategoryIds       = array_intersect($categories, $oldCategoryIds);
        $needDeleteCategoryIds = array_diff($oldCategoryIds, $sameCategoryIds);
        $newCategoryIds        = array_diff($categories, $sameCategoryIds);

        if (!empty($needDeleteCategoryIds)) {
            $post->categories()->detach($needDeleteCategoryIds);
        }

        if (!empty($newCategoryIds)) {
            $post->categories()->attach(array_values($newCategoryIds));
        }

        $data['post_keywords'] = str_replace('，', ',', $data['post_keywords']);
        $data['advantage_keywords'] = str_replace('，', ',', $data['post_keywords']);

        $keywords = explode(',', $data['post_keywords']);

        $this->addTags($keywords, $data['id']);
        $this->syncRegionOpenStatus($data);

        return $this;

    }

    /**
     * 同步开放城市县区
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    protected function syncRegionOpenStatus ($data)
    {
        // 发布
        if ($data['post_status'] == 1) {

            $provinceId = $data['province_id'];
            $cityId     = $data['city_id'];
            $countyId   = $data['county_id'];

            $where = [$provinceId, $cityId, $countyId];

            $regionModel = new RegionModel();
            $result = $regionModel->where('id', 'in', $where)->update(['is_opening' => 1]);
        } else if ($data['post_status'] == 0) {

            $provinceId = $data['province_id'];
            $cityId     = $data['city_id'];
            $countyId   = $data['county_id'];

            $baseMap = [
                ['post_status', '=', 1],
                ['delete_time', '=', 0]
            ];

            $countProvince  = $this->where($baseMap)->where('province_id', $provinceId)->count();
            $countCity      = $this->where($baseMap)->where('city_id', $cityId)->count();
            $countCounty    = $this->where($baseMap)->where('county_id', $countyId)->count();
            
            $regionModel = new RegionModel();

            $openStatus = $countProvince > 0 ? 1 : 0;
            $regionModel->where('id', '=', $provinceId)->update(['is_opening' => $openStatus]);

            $openStatus = $countCity > 0 ? 1 : 0;
            $regionModel->where('id', '=', $cityId)->update(['is_opening' => $openStatus]);

            $openStatus = $countCounty > 0 ? 1 : 0;
            $regionModel->where('id', '=', $countyId)->update(['is_opening' => $openStatus]);
        }
    }

    /**
     * 增加标签
     * @param $keywords
     * @param $postId
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function addTags($keywords, $postId)
    {
        $jobTagModel = new JobTagModel();

        $tagIds = [];

        $data = [];

        if (!empty($keywords)) {

            $oldTagIds = JobTagPostModel::where('post_id', $postId)->column('tag_id');

            foreach ($keywords as $keyword) {
                $keyword = trim($keyword);
                if (!empty($keyword)) {
                    $findTag = $jobTagModel->where('name', $keyword)->find();
                    if (empty($findTag)) {
                        $tagId = $jobTagModel->insertGetId([
                            'name' => $keyword
                        ]);
                    } else {
                        $tagId = $findTag['id'];
                    }

                    if (!in_array($tagId, $oldTagIds)) {
                        array_push($data, ['tag_id' => $tagId, 'post_id' => $postId]);
                    }

                    array_push($tagIds, $tagId);

                }
            }


            if (empty($tagIds) && !empty($oldTagIds)) {
                JobTagPostModel::where('post_id', $postId)->delete();
            }

            $sameTagIds = array_intersect($oldTagIds, $tagIds);

            $shouldDeleteTagIds = array_diff($oldTagIds, $sameTagIds);

            if (!empty($shouldDeleteTagIds)) {
                JobTagPostModel::where('post_id', $postId)
                    ->where('tag_id', 'in', $shouldDeleteTagIds)
                    ->delete();
            }

            if (!empty($data)) {
                JobTagPostModel::insertAll($data);
            }


        } else {
            JobTagPostModel::where('post_id', $postId)->delete();
        }
    }

    /**
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function adminDeletePage($data)
    {

        if (isset($data['id'])) {
            $id = $data['id']; //获取删除id

            $res = $this->where('id', $id)->find();

            if ($res) {
                $res = json_decode(json_encode($res), true); //转换为数组

                $recycleData = [
                    'object_id'   => $res['id'],
                    'create_time' => time(),
                    'table_name'  => 'portal_post#page',
                    'name'        => $res['post_title'],
                    'user_id'     => cmf_get_current_admin_id()
                ];

                PortalPostModel::startTrans(); //开启事务
                $transStatus = false;
                try {
                    PortalPostModel::where('id', $id)->update([
                        'delete_time' => time()
                    ]);
                    RecycleBinModel::insert($recycleData);

                    $transStatus = true;
                    // 提交事务
                    PortalPostModel::commit();
                } catch (\Exception $e) {

                    // 回滚事务
                    PortalPostModel::rollback();
                }
                return $transStatus;


            } else {
                return false;
            }
        } elseif (isset($data['ids'])) {
            $ids = $data['ids'];

            $res = $this->where('id', 'in', $ids)
                ->select();

            if ($res) {
                $res = json_decode(json_encode($res), true);
                foreach ($res as $key => $value) {
                    $recycleData[$key]['object_id']   = $value['id'];
                    $recycleData[$key]['create_time'] = time();
                    $recycleData[$key]['table_name']  = 'portal_post';
                    $recycleData[$key]['name']        = $value['post_title'];

                }

                PortalPostModel::startTrans(); //开启事务
                $transStatus = false;
                try {
                    PortalPostModel::where('id', 'in', $ids)
                        ->update([
                            'delete_time' => time()
                        ]);


                    RecycleBinModel::insertAll($recycleData);

                    $transStatus = true;
                    // 提交事务
                    PortalPostModel::commit();

                } catch (\Exception $e) {

                    // 回滚事务
                    PortalPostModel::rollback();


                }
                return $transStatus;


            } else {
                return false;
            }

        } else {
            return false;
        }
    }

}
