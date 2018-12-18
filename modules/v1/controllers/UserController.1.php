<?php

namespace app\modules\v1\controllers;
use Yii;
use app\models\LoginForm;
use app\models\User;
use app\components\Jwt;
use yii\rest\ActiveController;
use codemix\yii2confload\Config;
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


    public function actionSignup()
    {
        $model = new SignupForm();
        
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        // var_dump(Yii::$app->getRequest()->getBodyParams());
        // return ['name' => 'stark'];
        // die;    
        if ($model->signup()) {
            return ['resulte' => '注册成功！'];
        }
        else {
            $model->validate();
            return $model;
        }
        
    }

    public function actionRegister()
    {

        $model = new SignupForm();
        $model->attributes = $this->request;

        if ($user = $model->signup()) {

            $data=$user->attributes;
            unset($data['auth_key']);
            unset($data['password_hash']);
            unset($data['password_reset_token']);

            Yii::$app->api->sendSuccessResponse($data);

        }

    }

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

    public function actionLogin()
    {
        $model = new LoginForm();
        
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        
//         $model->username = $_POST['username'];
//         $model->password = $_POST['password'];
        
        if ($model->login()) {
            $token = Jwt::createJwt(['uid'=> 1 ]);
            return ['access_token' => $model->login()];
        }
        else {
            $model->validate();
            return $model;
        }
    }
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
