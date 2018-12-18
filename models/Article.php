<?php

namespace app\models;

use app\models\User;
use app\models\ArticleImg;
use Yii;

/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $title
 * @property integer $user_id
 * @property string $content
 * @property string $content_md
 * @property string $excerpt
 * @property integer $categroy
 * @property integer $tag_id
 * @property string $tag_name
 * @property integer $type
 * @property integer $create_time_gmt
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $comment_status
 * @property integer $comment_count
 * @property integer $ping_status
 * @property integer $article_password
 * @property integer $status
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['user_id', 'category', 'tag_id', 'type', 'create_time_gmt', 'create_time', 'update_time', 'comment_status', 'comment_count', 'ping_status', 'article_password', 'status'], 'integer'],
            [['content', 'content_md', 'excerpt', 'contacts'], 'string'],
            [['title'], 'string', 'max' => 200],
            [['tag_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'user_id' => 'User ID',
            'content' => 'Content',
            'contacts' => 'Contacts',
            'content_md' => 'Content Md',
            'excerpt' => 'Excerpt',
            'category' => 'category',
            'category_name' => 'category_name',
            'tag_id' => 'Tag ID',
            'tag_name' => 'Tag Name',
            'type' => 'Type',
            'create_time_gmt' => 'Create Time Gmt',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'comment_status' => 'Comment Status',
            'comment_count' => 'Comment Count',
            'ping_status' => 'Ping Status',
            'article_password' => 'Article Password',
            'status' => 'Status',
            'pv' => 'pv',
            'uv' => 'uv',
            'username' => 'username',
        ];
    }
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    /**
     * 图片列表
     * @return \yii\db\ActiveQuery
     */
    public function getImage()
    {
        return $this->hasMany(ArticleImg::className(), ['article_id' => 'id']);
    }
}
