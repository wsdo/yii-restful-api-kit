<?php
namespace app\modules\v1\controllers;

use yii\rest\ActiveController;
use app\models\Article;
use app\models\Tag;
use yii;

class TagController extends \yii\rest\ActiveController
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
        $tag = new Tag();
        $request = Yii::$app->request;
        $post = $request->post();
        $tag->name = $post['name'];
        $tag->desc = $post['desc'];
        print_r($post);
        $result = $tag->save();
        return $result;
    }

    public function actionUpdate()
    {   
        // $article = new Tag();
        $request = Yii::$app->request;
        $post = $request->post();
        $tag = $tag::findOne($post['id']);
        $tag->name = $post['name'];
        $tag->content = $post['desc'];
        $result = $tag->save();
        return $result;
    }

    public function actionList()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $tag = Tag::find()->asArray()->all();
        // print_r($get['id']);
        // die;
        return $tag;
    }

    public function actions()
    {

        $actions = parent::actions();

        // 全部的API都手动写出来,然后用权限控制
        unset($actions['delete'], $actions['create'], $actions['index'], $actions['view'], $actions['update']);

        return $actions;
    }
}