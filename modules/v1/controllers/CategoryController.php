<?php
namespace app\modules\v1\controllers;

use yii\rest\ActiveController;
use app\models\Article;
use app\models\Category;
use yii;

class CategoryController extends \yii\rest\ActiveController
{
    public $modelClass='app\models\User';

    public function actionIndex()
    {
        return ['name'=>'stark'];
        // $i =  ['name'=>'stark','age'=>'18','sex'=>'man'];
        // return $i;
    }

    public function actionCreate()
    {   
        $Category = new Category();
        $request = Yii::$app->request;
        $post = $request->post();
        $Category->name = $post['name'];
        $Category->desc = $post['desc'];
        $result = $Category->save();
        return $result;
    }

    public function actionUpdate()
    {   
        // $article = new Category();
        $request = Yii::$app->request;
        $post = $request->post();
        $Category = Category::findOne($post['id']);
        $Category->name = $post['name'];
        $Category->content = $post['desc'];
        $result = $Category->save();
        return $result;
    }

    public function actionList()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $Category = Category::find()->asArray()->all();
        // print_r($get['id']);
        // die;
        return $Category;
    }

    public function actions()
    {

        $actions = parent::actions();

        // 全部的API都手动写出来,然后用权限控制
        unset($actions['delete'], $actions['create'], $actions['index'], $actions['view'], $actions['update']);

        return $actions;
    }
}