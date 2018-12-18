<?php

namespace app\components;

use yii\base\Component;
use Qiniu\Storage\UploadManager;
use Qiniu\Auth;
use Yii;
use yii\base\InvalidConfigException;

class QiniuComponent extends Component
{
    /**
     * read only
     *
     * @var
     */
    public $accessKey;
    /**
     * read only
     * @var
     */
    public $secretKey;

    /**
     * @var Auth
     */
    private $_auth;

    public function init()
    {
        parent::init();

        if (!$this->accessKey) {
            throw new InvalidConfigException('accessKey can not be blank');
        }

        if (!$this->secretKey) {
            throw new InvalidConfigException('secretKey can not be blank');
        }

        if (!isset(Yii::$app->params['qiniu.access_domain']) || Yii::$app->params['qiniu.access_domain'] === null) {
            throw new InvalidConfigException("Yii::\$app->params['qiniu.access_domain'] can not be blank.");
        }

        if (!isset(Yii::$app->params['qiniu.bucket']) || Yii::$app->params['qiniu.bucket'] === null) {
            throw new InvalidConfigException("Yii::\$app->params['qiniu.bucket'] can not be blank.");
        }

        $this->_auth = new Auth($this->accessKey, $this->secretKey);
    }

    public function getAccessKey()
    {
        return $this->accessKey;
    }

    public function getSecretKey()
    {
        return $this->secretKey;
    }

    public function getUploadToken(
        $bucket = null,
        $key = null,
        $expires = null,
        $policy = null,
        $strictPolicy = true
    ) {
        $bucket = $bucket === null ? Yii::$app->params['qiniu.bucket'] : $bucket;
        $expires = $expires === null ? Yii::$app->params['qiniu.upload_token_expires'] : $expires;

        return $this->_auth->uploadToken($bucket, $key, $expires, $policy, $strictPolicy);
    }

    public function uploadFile($filepath,$filename, $bucket = null)
    {
        $bucket = $bucket === null ? Yii::$app->params['qiniu.bucket'] : $bucket;

        $upManager = new UploadManager();
        $uploadToken = $this->getUploadToken($bucket);
        if(empty($filename)){
            $filename = basename($file);
        }
        list($ret, $err) = $upManager->putFile($uploadToken, $filename, $filepath);
        if ($err !== null) {
            return $err;
        } else {
            return $ret;
        }
    }

    public function completelyUrl($key)
    {
        return Yii::$app->params['qiniu.access_domain'] . $key;
    }
}