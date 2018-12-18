<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property integer $pid
 * @property string $name
 * @property string $order
 * @property string $desc
 * @property string $path
 * @property string $nicename
 * @property string $description
 * @property integer $parent
 * @property integer $update_time
 * @property integer $status
 * @property integer $type
 * @property integer $create_time
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'parent', 'update_time', 'status', 'type', 'create_time'], 'integer'],
            [['name', 'order', 'desc', 'path', 'nicename'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => 'Pid',
            'name' => 'Name',
            'order' => 'Order',
            'desc' => 'Desc',
            'path' => 'Path',
            'nicename' => 'Nicename',
            'description' => 'Description',
            'parent' => 'Parent',
            'update_time' => 'Update Time',
            'status' => 'Status',
            'type' => 'Type',
            'create_time' => 'Create Time',
        ];
    }
}
