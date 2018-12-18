<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "article_img".
 *
 * @property integer $id
 * @property integer $article_id
 * @property integer $status
 * @property string $thumbnail
 * @property string $normal
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $count
 */
class ArticleImg extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_img';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['article_id', 'status', 'create_time', 'update_time', 'count'], 'integer'],
            [['thumbnail', 'normal'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'article_id' => 'Article ID',
            'status' => 'Status',
            'thumbnail' => 'Thumbnail',
            'normal' => 'Normal',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'count' => 'Count',
        ];
    }
}
