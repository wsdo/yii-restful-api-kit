<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "news".
 *
 * @property integer $id
 * @property string $title
 * @property integer $user_id
 * @property string $username
 * @property string $address
 * @property string $contacts
 * @property string $excerpt
 * @property string $content
 * @property string $mobile
 * @property integer $status
 * @property string $tag_name
 * @property integer $pv
 * @property integer $uv
 * @property string $content_md
 * @property integer $category
 * @property integer $tag_id
 * @property integer $type
 * @property integer $create_time_gmt
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $comment_status
 * @property integer $comment_count
 * @property integer $ping_status
 * @property integer $article_password
 * @property string $category_name
 * @property string $image_url
 * @property string $middle_image_url
 * @property string $large_image_url
 * @property string $source
 * @property integer $has_image
 */
class News extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'status', 'pv', 'uv', 'category', 'tag_id', 'type', 'create_time_gmt', 'create_time', 'update_time', 'comment_status', 'comment_count', 'ping_status', 'article_password', 'has_image'], 'integer'],
            [['excerpt', 'content', 'content_md'], 'string'],
            [['title', 'username'], 'string', 'max' => 200],
            [['address', 'contacts', 'tag_name', 'category_name', 'image_url', 'middle_image_url', 'large_image_url', 'source'], 'string', 'max' => 255],
            [['mobile'], 'string', 'max' => 44],
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
            'username' => 'Username',
            'address' => 'Address',
            'contacts' => 'Contacts',
            'excerpt' => 'Excerpt',
            'content' => 'Content',
            'mobile' => 'Mobile',
            'status' => 'Status',
            'tag_name' => 'Tag Name',
            'pv' => 'Pv',
            'uv' => 'Uv',
            'content_md' => 'Content Md',
            'category' => 'Category',
            'tag_id' => 'Tag ID',
            'type' => 'Type',
            'create_time_gmt' => 'Create Time Gmt',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'comment_status' => 'Comment Status',
            'comment_count' => 'Comment Count',
            'ping_status' => 'Ping Status',
            'article_password' => 'Article Password',
            'category_name' => 'Category Name',
            'image_url' => 'Image Url',
            'middle_image_url' => 'Middle Image Url',
            'large_image_url' => 'Large Image Url',
            'source' => 'Source',
            'has_image' => 'Has Image',
        ];
    }
}
