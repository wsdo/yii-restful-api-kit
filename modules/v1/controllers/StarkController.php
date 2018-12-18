<?php
namespace app\modules\v1\controllers;

use yii\rest\ActiveController;
use Yii;
use app\models\User;
use app\models\Test;
use codemix\yii2confload\Config;



/**
 * 活动控制器
 *
 * Class ActivityController
 *
 * @package app\modules\v1\controllers
 */
class StarkController extends \yii\rest\ActiveController
{
    public $modelClass='app\models\User';

   
    public function actionIndex()
    {
        // Config::initEnv(dirname(__DIR__));
        $setting = Config::env('WEIXIN_APP_ID');
        // $request = Yii::$app->request;
        // echo \Env::get('WEIXIN_APP_ID');
        $i =  ['name'=>'stark','age'=>'18','sex'=>'man','wechat' => $setting];
        // return \Env::get('WEIXIN_APP_ID');
        return $i;
    }

    public function actionCookie()
    {

        $cookies = Yii::$app->request->cookies;
        $session = Yii::$app->session;
        // $username = $cookies->get('id');
        $username = $cookies->get('name');

        // $username = $session;
        return $username;
    }

    public function actions()
    {

        $actions = parent::actions();

        // 全部的API都手动写出来,然后用权限控制
        unset($actions['delete'], $actions['create'], $actions['index'], $actions['view'], $actions['update']);

        return $actions;
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $data = $request->post();
        $test = new Test();
        $test->data = $data['data'];
        $test->save();
    }

    public function actionUser()
    {
        $user = User::find()->all();
        return $user;
    }
}
