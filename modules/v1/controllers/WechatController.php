<?php
namespace app\modules\v1\controllers;

use yii\rest\ActiveController;
use app\models\Article;
use yii\helpers\ArrayHelper;
use yii\filters\auth\QueryParamAuth;
use yii\data\Pagination;
use codemix\yii2confload\Config;
use app\modules\v1\controllers\WechatJsSdkController as JSSDK;
use yii;

define("TOKEN", "stark");

class WechatController extends \yii\rest\ActiveController
{
    public $enableCsrfValidation = false;
    private $help = "使用帮助: 回复数字查看活动文章中对应序号的活动";
    public $modelClass='app\models\User';
    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() == JSON_ERROR_NONE;
    }

    /**
     * 微信接口接入方法
     */
    public function actionValid()
    {
        if (isset($_GET['echostr'])) { // 验证
            $this->valid();
        } else { //响应消息和事件
            $this->responseMsg();
        }
    }

    /**
     * 微信接入验证
     */
    public function valid()
    {
        $echoStr = Yii::$app->request->get("echostr");
        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        }
    }

    public function actionSign(){
        $sdk = new JSSDK(Config::env('WEIXIN_APP_ID'), Config::env('WEIXIN_APP_SECRET'));
        return $sdk->GetSignPackage();
    }
    /**
     * 验证签名
     *
     * @return bool
     * @throws Exception 如果TOKEN没有定义, 则抛出异常
     */
    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $request = Yii::$app->request;
        $signature = $request->get("signature");
        $timestamp = $request->get("timestamp");
        $nonce = $request->get("nonce");

        $token = TOKEN;
        $tmpArr = [$token, $timestamp, $nonce];
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        return $tmpStr == $signature;
    }

    
    /**
     * 响应消息和事件
     */
    public function responseMsg()
    {
        // $postData = $GLOBALS['HTTP_RAW_POST_DATA'];
        $postData = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        if (empty($postData)) {
            file_put_contents('wx.log','post数据为空'.FILE_APPEND."\n",FILE_APPEND);
        }
        file_put_contents('wx.log',$postData,FILE_APPEND);
        if (!empty($postData)) {
            libxml_disable_entity_loader(true);
            $obj = simplexml_load_string($postData, "SimpleXMLElement", LIBXML_NOCDATA);

            $MsgType = $obj->MsgType;

            switch ($MsgType) {
                case 'text':
                    echo $this->receiveText($obj);
                    break;
                case 'image':
                    echo $this->receiveImage($obj);
                    break;
                case 'voice':
                    echo $this->receiveVoice($obj);
                    break;
                case 'event':
                    $this->receiveEvent($obj); //当退订的时候不用输出, 所以这里将echo去掉, 在里面去处理
                    break;
                default:
                    break;
            }
        } else {
            echo "Error";
        }
    }
    /**
     * 接收文字消息
     *
     * @param  SimpleXMLElement $obj 接收的微信对象
     * @return string
     */
    public function receiveText($obj)
    {
        $Content = trim( $obj->Content );
        $res = $this->GetData($Content);
        // $res = $res;
        file_put_contents('wx.log',$res,FILE_APPEND);
        //数字回复对应的活动信息
        return $this->replyText($obj, $res);
    }
    /**
     * 回复文本
     *
     * @param  SimpleXMLElement $obj     接收的微信对象
     * @param  string           $Content 回复的文本内容
     * @return string
     */
    public function replyText($obj, $Content)
    {
        $replyXml = "<xml>
						<ToUserName><![CDATA[".$obj->FromUserName."]]></ToUserName>
						<FromUserName><![CDATA[".$obj->ToUserName."]]></FromUserName>
						<CreateTime>".time()."</CreateTime>
						<MsgType><![CDATA[text]]></MsgType>
						<Content><![CDATA[".$Content."]]></Content>
						</xml>";
        return $replyXml;
    }

    /**
     * cur get
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public static function CurlGet($url){
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
    
    public function GetData($info)
    {
        $url = 'http://www.tuling123.com/openapi/api';
        $key = '018d7b365e3140a9bf9e7f77ab7e977a';
        $data = $this->CurlGet($url.'?key='.$key.'&info='.$info);
        return $data;
    }
}
