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

use think\Model;

class RecycleBinModel extends Model
{
    /**
     * 模型名称
     * @var string
     */
    protected $name = 'recycle_bin';

    protected $autoWriteTimestamp = true;
    protected $update = false;

    public function user()
    {
        return $this->belongsTo('UserModel', 'user_id');
    }

}
