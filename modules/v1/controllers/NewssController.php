<?php
namespace app\modules\v1\controllers;

use app\filters\AccessToken;
use app\components\Common;
use app\models\Article;
use app\models\ArticleImg;
use app\models\User;
use app\service\ArticleService;
use codemix\yii2confload\Config;
use yii\data\Pagination;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use yii;

class NewsController extends \yii\rest\ActiveController
{

    public $modelClass = 'app\models\User';
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessToken::className(),
            'only' => ['index'],
            'rules' => [
                [
                    'actions' => ['list', 'create'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
        return $behaviors;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        //对ActiveControllers类中默认实现了的方法进行权限设置
        if ($action === 'view') {
            if (\Yii::$app->user->can('ArticleViewer')) {
                return true;
            }
        }

        if ($action === 'view' || $action === 'update' || $action === 'delete'
            || $action === 'create' || $action === 'index') {
            if (\Yii::$app->user->can('ArticleAdmin')) {
                return true;
            }
        }

        throw new ForbiddenHttpException('对不起，你没有进行该操作的权限。');

    }

    public function actionIndex()
    {
        // $url = "https://www.toutiao.com/search_content/?offset=0&format=json&keyword=%E6%B8%85%E6%B2%B3%E5%8E%BF&autoload=true&count=20&cur_tab=1&callback=__jp5";
        // $data = Common::curlGet($url);
        // return $data;
        $i =  ['name'=>'stark','age'=>'18','sex'=>'man'];
        return $i;
    }

    public function actionList($keyword = '清河县', $count = 20,$page = 0)
    {   
        // $keyword = '清河县';
        $url = "https://www.toutiao.com/search_content/?offset=${page}&format=json&keyword=${keyword}&autoload=true&count=${count}0&cur_tab=1";
        $data = Common::curlGet($url);
        return json_decode($data);
        // return ['name' => 'stark'];
        // $i =  ['name'=>'stark','age'=>'18','sex'=>'man'];
        // return $i;
    }
   
    public function actions()
    {

        $actions = parent::actions();

        // 全部的API都手动写出来,然后用权限控制
        unset($actions['delete'], $actions['create'], $actions['index'], $actions['view'], $actions['update']);

        return $actions;
    }
}
