<?php

namespace app\modules\v1\controllers;
use Yii;
use app\models\LoginForm;
use app\models\User;
use app\components\Jwt;
use yii\rest\ActiveController;
use codemix\yii2confload\Config;
use dektrium\user\models\Account;
// use yii\filters\auth\QueryParamAuth;
// use yii\helpers\ArrayHelper;
// use yii\rest\ActiveController;
// use yii\web\IdentityInterface;
use app\models\SignupForm;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];
    // public function behaviors()
    // {
    //     return ArrayHelper::merge (parent::behaviors(), [
    //         'authenticator' => [
    //             'class' => QueryParamAuth::className(),
    //             'optional' => [//过滤不需要验证的action
    //                 'login',
    //                 'signup-test'
    //             ],
    //         ]
    //     ] );
    // }


    // public function actionSignup()
    // {
    //     $model = new SignupForm();

    //     $model->load(Yii::$app->getRequest()->getBodyParams(), '');
    //     // var_dump(Yii::$app->getRequest()->getBodyParams());
    //     // return ['name' => 'stark'];
    //     // die;
    //     if ($model->signup()) {
    //         return ['resulte' => '注册成功！'];
    //     }
    //     else {
    //         $model->validate();
    //         return $model;
    //     }

    // }

    // public function actionRegister()
    // {

    //     $model = new SignupForm();
    //     $model->attributes = $this->request;

    //     if ($user = $model->signup()) {

    //         $data=$user->attributes;
    //         unset($data['auth_key']);
    //         unset($data['password_hash']);
    //         unset($data['password_reset_token']);

    //         Yii::$app->api->sendSuccessResponse($data);

    //     }

    // }

    /**
     * 添加测试用户
     */
    // public function actionSignupTest ()
    // {
    //     $user = new User();
    //     $user->generateAuthKey();
    //     $user->setPassword('123456');
    //     $user->username = '111';
    //     $user->email = '111@111.com';
    //     $user->save(false);
    //     return [
    //         'code' => 0
    //     ];
    // }
    /**
     * 登录
     */
    // public function actionLogin ()
    // {
    //     $model = new LoginForm();
    //     $model->setAttributes(Yii::$app->request->post());
    //     if ($user = $model->login()) {
    //         if ($user instanceof IdentityInterface) {
    //             return $user->access_token;
    //         } else {
    //             return $user->errors;
    //         }
    //     } else {
    //         return $model->errors;
    //     }
    // }
    public function actionInfo()
    {
        $id = Yii::$app->user->id;
        $username = '';
        $user = Yii::$app->user->identity;
        if($id){
            $username = Yii::$app->user->identity->name;
        }else{
            $this->redirect('/v1/user/login');
            return false;
        }
        return ['id'=>$id,'username'=>$username,'headimgurl'=>$user->headimgurl];
    }
    public function actionLogin()
    {
        //微信客户端的名称
        $client = 'wechat';

        //跳转过来的链接
        $returnUrl = Yii::$app->user->returnUrl;

        //微信跳转链接
        $redirect_uri = Yii::$app->params['domain']."/v1/user/register";
        //微信组件
        $wechat = Yii::$app->wechat;
        //Session组件
        $session = Yii::$app->session;
        // $redis = Yii::$app->redis;
        //获取openid
        $snsapi_userinfo_url = $wechat->getOauth2AuthorizeUrl($redirect_uri, 'authorize', 'snsapi_userinfo');
        // print_r($snsapi_userinfo_url);
        if (Yii::$app->request->get('code', null) === null) {
            return $this->redirect($snsapi_userinfo_url);
        }

    }

    public function actionRegister(){
        $wechat = Yii::$app->wechat;
        $provider = 'wechat';
        $home = Config::env('WECHAT_DOMAIN');
        //用户登录保存session的时间
        $duration = 3600 * 24 * 30;
        //跳转过来的链接
        $returnUrl = Yii::$app->user->returnUrl;
        $code = Yii::$app->request->get('code');
        $user = $this->GetWXUser($code);
        $session = Yii::$app->session;
        $openid = $user['openid'];
        // 判断是否存在这个用户
        $existUser = User::find()->where(['openid' => $openid, 'provider' => $provider])->one();
        $cookies = Yii::$app->response->cookies;
        // get the cookie collection (yii\web\CookieCollection) from the "response" component
        $cookies = Yii::$app->response->cookies;
        if($existUser){
            Yii::$app->user->login($existUser, $duration);
            $cookies->add(new \yii\web\Cookie([
                'name' => 'name',
                'expire' => time() + 3600 * 24,
                'value' => $existUser->name
            ]));

            $cookies->add(new \yii\web\Cookie([
                'name' => 'id',
                'expire' => time() + 3600 * 24,
                'value' => $existUser->id
            ]));

            $cookies->add(new \yii\web\Cookie([
                'name' => 'token',
                'expire' => time() + 3600 * 24,
                'value' => $existUser->id,
                'httpOnly' => false
            ]));

            return $this->redirect($home);
        }else{
            //生成6位字符随机密码
            $password = Yii::$app->security->generateRandomString(6);
            $nickname = $user['nickname'];
            // $openid = $user['openid'];
            // $unionid = $user['unionid'];
            // $subscribe = $user['subscribe'];
            // $subscribe_time = isset($user['subscribe_time'])?$user['subscribe_time']:0;

            //注册用户, 暂时不绑定用户, 直接使用微信昵称作为用户名
            $user = new User(
                [
                    // 'unionid' => $unionid,
                    'nickname' => $nickname,
                    'name' => $nickname,
                    'openid' => $openid,
                    // 'subscribe' => $subscribe,
                    // 'subscribe_time' => $subscribe_time,
                    'password' => $password,
                    'sex' => $user['sex'],
                    'city' => $user['city'],
                    'province' => $user['province'],
                    'country' => $user['country'],
                    'headimgurl' => $user['headimgurl'],
                ]
            );

            $user->scenario = 'register';
            $user->provider = 'wechat';
            //尝试保存用户
            if (!$user->save()) {

                //回滚事务
                // $transaction->rollBack();
                Yii::error('保存用户失败');
                $session->setFlash('danger', '登录失败, 请重新尝试');
                return $this->render('error');
            }else{
                return $this->redirect($home);
            }
        }
        return '注册成功';
    }

    public function GetWXUser($code)
    {
        $appid = Config::env('WEIXIN_APP_ID');
        $appsecret = Config::env('WEIXIN_APP_SECRET');
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code";
        $cont =  $this->CurlGet($url);
        $cont = (array)json_decode($cont);
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$cont['access_token']}&openid={$cont['openid']}&lang=zh_CN";
        $cont =  $this->CurlGet($url);
        $user = (array)json_decode($cont);
        return $user;
    }

    public function CurlGet($url){
    	$data = '';
		$ch = curl_init();
		$header = "Accept-Charset: utf-8";
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		// curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$temp = curl_exec($ch);
		return $temp;
    }
//     public function actionLogin()
//     {
//         $model = new LoginForm();

//         $model->load(Yii::$app->getRequest()->getBodyParams(), '');

//         // print_r($_POST); die;
// //         $model->username = $_POST['username'];
// //         $model->password = $_POST['password'];
//         // $id = $model->getUserId();
//         $id = User::find('username',$_POST['username'])->AsArray()->one();
//         // $id->id;
//         // print_r($id['id']);
//         // die;
//         if ($model->login()) {
//             $token = Jwt::createJwt(['uid'=> $id['id'] ]);
//             return ['token' => $token];
//         }
//         else {
//             $model->validate();
//             return $model;
//             \Yii::$app->response->error(Error::ACCOUNT_PASSWORD_ERROR, '账号或者密码错误');
//         }
//     }
    /**
     * 获取用户信息
     */
    // public function actionUserProfile ()
    // {
    //     // 到这一步，token都认为是有效的了
    //     // 下面只需要实现业务逻辑即可，下面仅仅作为案例，比如你可能需要关联其他表获取用户信息等等
    //     /** @var  $user User */
    //     $user = Yii::$app->user->identity;
    //     return [
    //         'id' => $user->id,
    //         'username' => $user->username,
    //         'email' => $user->email,
    //     ];
    // }

}
