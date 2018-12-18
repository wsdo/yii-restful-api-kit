<?php
namespace app\modules\v1\controllers;

use yii\rest\ActiveController;


use yii\filters\AccessControl;
use app\filters\AccessToken;
use yii\filters\VerbFilter;
use yii\base\Controller;
use app\models\Article;
use yii\data\Pagination;
// use
use yii;

class TestController extends \yii\rest\ActiveController
{
    public $modelClass='app\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // $behaviors['access'] = [
        //         'class' => AccessToken::className(),
        //         'only' => ['index'],
        //         'rules' => [
        //             [
        //                 'actions' => ['index'],
        //                 'allow' => true,
        //                 'roles' => ['@'],
        //             ],
        //         ],
        // ];

        // $behaviors['apiauth'] = [
        //         'class' => AccessToken::className(),
        //         'exclude' => ['authorize', 'register', 'accesstoken','index'],
        // ];
        return $behaviors;
    }
    public function acionIndex()
    {
        $parser = new \HyperDown\Parser;
        $str = "## 王树东";
        $html = $parser->makeHtml($str);
        return ['name'=>$html];
    }
}


        //    'class' => AccessControl::className(),
        //         'only' => ['list'],
        //         'rules' => [
        //             [
        //                 'actions' => ['list'],
        //                 'allow' => true,
        //                 // 'roles' => ['@'],
        //             ],
        //         ],
