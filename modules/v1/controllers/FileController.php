<?php
namespace app\modules\v1\controllers;

use yii\rest\ActiveController;
use yii\web\UploadedFile;
use app\models\UploadForm;
use yii;

class FileController extends \yii\rest\ActiveController
{
    public $modelClass='app\models\User';

    public function actionIndex()
    {
        $model = new UploadForm();

        // if (Yii::$app->request->isPost) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            var_dump($model);
             return $this->render('index', ['model' => $model]);
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
    public function actionUploadFile()
    {
        header('Access-Control-Allow-Origin:*');
        $data = $_FILES;
        // die;
        $qiniu = Yii::$app->qiniu;
        $postfix=$_FILES['data']['name'];

        if($data['data']['type'] == 'audio/mp3'){
            $postfix = 'stark.mp3';
        }
        $filename = date('YmdHis',time() + 28800).rand(1,1000).substr($postfix,strrpos($postfix,"."));
        $token = $qiniu->uploadFile($data['data']['tmp_name'],$filename);
        $url = 'http://file.shudong.wang/'.$token['key'];
        
        if ($token) {
            $result=  ['status' => 1,'data' => '文件上传成功','url'=> $url ];
        }else{
            $result = ['status' => 0,'data' => '文件上传失败'];
        }
        return $result;



        // $model = new UploadForm();

        // if (Yii::$app->request->isPost) {
            // $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            // var_dump($model);
            // if ($model->upload()) {
            //     // 文件上传成功
            //     return;
            // }
        // }

        // return $model;


        // $request = Yii::$app->request;
        // $data = $request->post();
        // // $data = $_FILES["file"];
        
        // $image = UploadedFile::getInstanceByName('file');
        // var_dump($image);
        // $token = self::GetUploadToken();
        // $upToken = $token['token'];
        // $access_key = Yii::$app->params['qiniu.accessKey'];
        // $secret_key = Yii::$app->params['qiniu.secretKey'];
        // $fetch =$data['img'];
        // $imggg = $this->request_by_curl('http://up-z1.qiniup.com',$fetch,$upToken);
        // $imgs = json_decode(trim($imggg),true);
        // // return ['status' => 1,'data' => '图片上传成功','url'=>$imgs];
        // $imgss = $imgs['hash'];
        // $imgUrl = 'https://obf949end.qnssl.com/'.$imgss;
        // print_r($imggg);
        // $data = '1.jpg';
        // $bucket = Yii::$app->params['qiniu.bucket'];
        // $qiniu = Yii::$app->qiniu;
            //判断是否上传成功（是否使用post方式上传）  
        // if(is_uploaded_file($_FILES['imageFile']['tmp_name'])) {  
        //     //把文件转存到你希望的目录（不要使用copy函数）  
        //     $uploaded_file=$_FILES['imageFile']['tmp_name'];  
    
        //     //我们给每个用户动态的创建一个文件夹  
        //     $user_path=$_SERVER['DOCUMENT_ROOT']."/wsd";  
        //     //判断该用户文件夹是否已经有这个文件夹  
        //     if(!file_exists($user_path)) {  
        //         mkdir($user_path);  
        //     }  
    
        //     //$move_to_file=$user_path."/".$_FILES['imageFile']['name'];  
        //     $file_true_name=$_FILES['imageFile']['name'];  
        //     $move_to_file=$user_path."/".time().rand(1,1000).substr($file_true_name,strrpos($file_true_name,"."));  
        //     //echo "$uploaded_file   $move_to_file";  
        //     if(move_uploaded_file($uploaded_file,iconv("utf-8","gb2312",$move_to_file))) {  
        //         echo $_FILES['imageFile']['name']."上传成功";  
        //     } else {  
        //         echo "上传失败";  
        //     }  
        // } else {  
        //     echo "上传失败";  
        // } 
        // $token = $qiniu->uploadFile($uploaded_file);
        // var_dump($token);
        // if ($imgss) {
        //     return ['status' => 1,'data' => '图片上传成功','url'=>$imgUrl];
        // }
        // return ['status' => 0,'data' => '图片上传失败'];

        return $token;
    }
     /**
     * 把图片存在服务器上面
     * @return [type] [description]
     */
    public function actionUploadImgData()
    {
        $request = Yii::$app->request;
        $data = $request->post();
        // var_dump($data);
        $token = self::GetUploadToken();
        $upToken = $token['token'];
        $access_key = Yii::$app->params['qiniu.accessKey'];
        $secret_key = Yii::$app->params['qiniu.secretKey'];
        $fetch =$data['img'];
        $imggg = $this->request_by_curl('http://up-z1.qiniup.com',$fetch,$upToken);
        $imgs = json_decode(trim($imggg),true);
        // return ['status' => 1,'data' => '图片上传成功','url'=>$imgs];
        $imgss = $imgs['hash'];
        $imgUrl = 'https://obf949end.qnssl.com/'.$imgss;
        print_r($imggg);

        if ($imgss) {
            return ['status' => 1,'data' => '图片上传成功','url'=>$imgUrl];
        }
        return ['status' => 0,'data' => '图片上传失败'];
    }

    public function request_by_curl($remote_server,$post_string,$upToken) {
        $headers = array();
        $headers[] = 'Content-Type:image/png';
        $headers[] = 'Authorization:UpToken '.$upToken;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$remote_server);
        //curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER ,$headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    /**
     * 获取七牛的token
     * @return array
     */
    public static function GetUploadToken()
    {
        $bucket = Yii::$app->params['qiniu.bucket'];
        $expires = Yii::$app->params['qiniu.upload_token_expires'];
        $qiniu = Yii::$app->qiniu;
        $token = $qiniu->getUploadToken($bucket, null, $expires);
        // print_r($token);
        return [
            'token' => $token,
            'bucket' => $bucket,
            'expires' => $expires,
        ];
    }

    /**
     * 获取七牛的token
     * @return array
     */
    public function actionGetUploadToken()
    {
        $bucket = Yii::$app->params['qiniu.bucket'];
        $expires = Yii::$app->params['qiniu.upload_token_expires'];
        $qiniu = Yii::$app->qiniu;
        $token = $qiniu->getUploadToken($bucket, null, $expires);
        // print_r($token);
        return [
            'token' => $token,
            'bucket' => $bucket,
            'expires' => $expires,
        ];
    }

}
