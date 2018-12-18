<?php
namespace app\modules\v1\controllers;

use yii\rest\ActiveController;
use app\models\Article;
use yii\helpers\ArrayHelper;
use yii\filters\auth\QueryParamAuth;
use yii\data\Pagination;
use yii;

class WechatJsSdkController {
  private $appId;
  private $appSecret;

  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
  }

  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();
    // $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    // $url = $returnUrl = 'http://stark.ngrok.wdevelop.cn/post';
    $url = Yii::$app->request->referrer;
    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage; 
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  private function getJsApiTicket() {
    $session = Yii::$app->session;
    if($session->has('jsapi_ticket')){
      $data = $session['access_token'];
      if($data['expire_time']< time()){
        $jsapi_ticket = $data['access_token'];
      }else{
        $jsapi_ticket = $this->generateApiTicket();
      }
    }else{
      $jsapi_ticket = $this->generateApiTicket();
      $session['jsapi_ticket'] = ['expire_time' => time() + 7000,'jsapi_ticket' => $jsapi_ticket];
    }
    return $jsapi_ticket;
  }

  public function getAccessToken() {
    $session = Yii::$app->session;
    if($session->has('access_token')){
      $data = $session['access_token'];
      if($data['expire_time']< time()){
        $access_token = $data['access_token'];
      }else{
        $access_token = $this->generateToken();
      }
    }else{
      $access_token = $this->generateToken();
      $session['access_token'] = ['expire_time' => time() + 7000,'jsapi_ticket' => $access_token];
    }
    return $access_token;
  }

  public function generateToken(){
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
    $res = json_decode(file_get_contents($url),true);
    return $res['access_token'];
  }

  public function generateApiTicket(){
      $accessToken = $this->getAccessToken();
      $url = "http://api.weixin.qq.com/cgi-bin/ticket/getticket?type=1&access_token=$accessToken";
      $res = json_decode($this->httpGet($url));
    return $res->ticket;
  }
  public function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_URL, $url);
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
  }

}

