<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tag".
 *
 * @property integer $id
 * @property integer $pid
 * @property string $name
 * @property string $desc
 * @property integer $type
 * @property integer $create_time_gmt
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $status
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'type', 'create_time_gmt', 'create_time', 'update_time', 'status'], 'integer'],
            [['name', 'desc'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'pid' => 'Pid',
            'name' => '标签名字',
            'desc' => '标签描述',
            'type' => '类型',
            'create_time_gmt' => '文章gmt格林威治格式',
            'create_time' => '文章创建时间',
            'update_time' => '更新时间',
            'status' => '用户id',
        ];
    }
}
