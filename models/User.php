<?php

namespace app\models;
// use dektrium\user\models\User as BaseUser;
use yii\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $name
 * @property string $real_name
 * @property string $headimgurl
 * @property integer $age
 * @property string $country
 * @property string $province
 * @property string $city
 * @property integer $sex
 * @property string $openid
 * @property string $language
 * @property string $wechat_data
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property string $unconfirmed_email
 * @property integer $blocked_at
 * @property string $registration_ip
 * @property integer $flags
 * @property string $mobile
 * @property integer $status
 * @property string $wechat_id
 * @property integer $last_login_at
 * @property integer $last_active_at
 * @property string $email_confirmation_token
 * @property string $password_reset_token
 * @property integer $is_email_verified
 * @property string $unionid
 * @property string $access_token
 * @property integer $subscribe
 * @property integer $subscribe_time
 * @property string $provider
 * @property string $nickname
 * @property string $username
 * @property string $password
 * @property string $scenario
 */
class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['age', 'sex', 'created_at', 'updated_at', 'blocked_at', 'flags', 'status', 'last_login_at', 'last_active_at', 'is_email_verified', 'subscribe', 'subscribe_time'], 'integer'],
            [['wechat_data'], 'string'],
            [['name', 'real_name', 'headimgurl', 'country', 'province', 'city'], 'string', 'max' => 255],
            [['openid', 'language', 'email', 'unconfirmed_email'], 'string', 'max' => 190],
            [['password_hash', 'email_confirmation_token', 'password_reset_token', 'unionid'], 'string', 'max' => 60],
            [['auth_key', 'access_token'], 'string', 'max' => 32],
            [['registration_ip', 'mobile', 'wechat_id'], 'string', 'max' => 45],
            [['provider', 'nickname', 'username', 'password', 'scenario'], 'string', 'max' => 200],
            [['email'], 'unique'],
            [['mobile'], 'unique'],
            [['unionid'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'real_name' => 'Real Name',
            'headimgurl' => 'Headimgurl',
            'age' => 'Age',
            'country' => 'Country',
            'province' => 'Province',
            'city' => 'City',
            'sex' => 'Sex',
            'openid' => 'Openid',
            'language' => 'Language',
            'wechat_data' => 'Wechat Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'auth_key' => 'Auth Key',
            'unconfirmed_email' => 'Unconfirmed Email',
            'blocked_at' => 'Blocked At',
            'registration_ip' => 'Registration Ip',
            'flags' => 'Flags',
            'mobile' => 'Mobile',
            'status' => 'Status',
            'wechat_id' => 'Wechat ID',
            'last_login_at' => 'Last Login At',
            'last_active_at' => 'Last Active At',
            'email_confirmation_token' => 'Email Confirmation Token',
            'password_reset_token' => 'Password Reset Token',
            'is_email_verified' => 'Is Email Verified',
            'unionid' => 'Unionid',
            'access_token' => 'Access Token',
            'subscribe' => 'Subscribe',
            'subscribe_time' => 'Subscribe Time',
            'provider' => 'Provider',
            'nickname' => 'Nickname',
            'username' => 'Username',
            'password' => 'Password',
            'scenario' => 'Scenario',
        ];
    }
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getAuthKey()
    {
        return '';
    }
    public function validateAuthKey($authKey)
    {
        return true;
    }
}
