<?php
namespace app\modules\v1\controllers;

use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
use yii\filters\auth\QueryParamAuth;
use yii\data\Pagination;
use yii;

/**
 * 七牛控制器.
 */
class QiniuController
{
    public $enableCsrfValidation = false;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'get-upload-token' => ['get'],
                    'create-completely-url' => ['post'],
                ],
            ],
            'access' => [
                'class' => '\app\components\AccessControl',
            ],
        ];
    }

    /**
     * 创建一个完整的URL.
     *
     * @return array
     *
     * @throws BadRequestHttpException
     */
    public function actionCreateCompletelyUrl()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $key = Yii::$app->request->post('key');
        if ($key === null) {
            throw new BadRequestHttpException('key can not be blank');
        }

        /* @var \app\components\QiniuComponent $qiniu */
        $qiniu = Yii::$app->qiniu;
        $url = $qiniu->completelyUrl($key);

        return [
            'url' => $url,
        ];
    }

    /**
     * 获取上传的TOKEN.
     *
     * @return array
     */
    public function actionGetUploadToken()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $bucket = Yii::$app->params['qiniu.bucket'];
        $expires = Yii::$app->params['qiniu.upload_token_expires'];

        /* @var \app\components\QiniuComponent $qiniu */
        $qiniu = Yii::$app->qiniu;
        $token = $qiniu->getUploadToken($bucket, null, $expires);

        return [
            'token' => $token,
            'bucket' => $bucket,
            'expires' => $expires,
        ];
    }
}