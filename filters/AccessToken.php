<?php
/**
 * 登录状态过滤
 */
namespace app\filters;

use Yii;
use app\components\SessionCan;
use app\base\Error;
use app\helpers\client\AppClient;
use app\components\Jwt;
use yii\base\ActionFilter;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
use yii\web\ForbiddenHttpException;

use Firebase\JWT\SignatureInvalidException;

class AccessToken extends AccessControl
{
    public function beforeAction($action)
    {
        // echo '<pre>';
        // print_r($action);
        // die;
        $this->filterAccessToken();
        return parent::beforeAction($action);
    }

    /**
     * 用以检查用户access-token有效
     */
    public function filterAccessToken()
    {

      $session = Yii::$app->session;
      // var_dump($session->get('token'));
      // die;
        $token = isset($_REQUEST['access-token']) ? $_REQUEST['access-token'] : null;
        $headers = Yii::$app->getRequest()->getHeaders();
        // print_r($headers);
        // die;
        Yii::info("access-token check for toekn[{$token}]", 'application.service');
        $token = isset($headers['access-token']) ? $headers['access-token'] : null;
        if(empty($headers['access-token'])){
            throw new ForbiddenHttpException(Yii::t('yii', 'token is not empty'));
            // Yii::$app->response->error(Error::COMMON_SIGN,'token不能为空');

        }

        //从token中恢复信息
        $userinfo = null;
        //session信息还在
        AppClient::getInstance();
        //尝试从token中恢复用户信息
        // var_dump($token);die;

        if($token != $session->get('token')){
          throw new ForbiddenHttpException(Yii::t('yii', 'token is dont match'));
        }

        try {
            $userinfo = Jwt::getJwtInfo($token);
            if ($userinfo == 0) {
                throw new ForbiddenHttpException(Yii::t('yii', 'token is dont match'));
            }
        } catch (SignatureInvalidException $exception) {
            Yii::$app->response->error(Error::COMMON_SIGN,'token解析错误');
        }
        if ( empty($userinfo) ||empty($userinfo['uid']) ) {
            Yii::endProfile('FILTER-SESSION-'.YII_REQUEST_START_TIME);
            Yii::$app->response->error(Error::USER_NEED_LOGIN,'请先登录');
        }
        SessionCan::init($userinfo);

        Yii::info("{$token} check for uid[{$userinfo['uid']}], complete,
                userInfo from \r\n".VarDumper::dumpAsString($userinfo),
            'application.service'
        );
        return true;
    }
}
