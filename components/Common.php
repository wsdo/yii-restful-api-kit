<?php
namespace app\components;

use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use Yii;

class Common extends Component
{

    public static function getRandUga($all_uga_data,$ugaAnswer){
        //所有uga 问题id
        // $all_uga_data =MapUgaQuestion::find()->select(['id'])->asArray()->all();
        $all_uga_id =[];
        if(!empty($all_uga_data)){
            foreach ($all_uga_data as $row) {
                $all_uga_id[] = $row['id'];
            }
        }
        //所有已经选择的uga问题
        $has_answer = [];
        if(!empty($ugaAnswer)){
            foreach($ugaAnswer as $row){
                $has_answer[] = $row['uga_id'];
                //如果存在已经选择的 则剔除
                $index = array_search($row['uga_id'],$all_uga_id);
                if($index !==false){
                    unset($all_uga_id[$index]);
                }

            }
        }
        if(empty($all_uga_id)){
            $this_uga_id = 0;
        }else{
            //下一个uga问题
            $this_uga_id = array_rand($all_uga_id,1);
            $this_uga_id = $all_uga_id[$this_uga_id];
        }
        return $this_uga_id;
    }
    /**
     * 获取openid
     * @param $user_id 用户id
     * @return 用户用户的open id
     */
    public static function getOpenid($user_id)
    {
        $account = Account::find()
                ->where(['user_id' => $user_id])
                ->one();
        return $account->client_id;
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
     * 抓取图片信息
     * @param $url
     * @return mixed
     */
    public static function curlGet($url)
    {
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
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $temp = curl_exec($ch);
        return $temp;
    }
    public static function request_by_curl($remote_server,$post_string,$upToken) {

        $headers = array();
        $headers[] = 'Content-Type:image/png';
        $headers[] = 'Authorization:UpToken '.$upToken;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$remote_server);
        //curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER ,$headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public static function addMapFriend($user_id){
    	$session = Yii::$app->session;
	    $data = $session->get('addfriend');
	    $session->remove('addfriend');
	    if($data){
	    	if($data['FriendMyAnswer']['friend_question_id'] >0){
			    $myAnswer= FriendMyAnswer::find()->where(['user_id'=>$user_id,'friend_question_id'=>$data['FriendMyAnswer']['friend_question_id'],'friend_id'=>$data['FriendMyAnswer']['friend_id']])->one();
	            if(!$myAnswer){
	                $myAnswer = new FriendMyAnswer();;
	            }
			    $myAnswer->created_at = time();
			    $myAnswer->user_id = $user_id;
			    $myAnswer->my_answer = trim($data['FriendMyAnswer']['my_answer']);
			    $myAnswer->is_show=1;
			    $myAnswer->friend_id = $data['FriendMyAnswer']['friend_id'];
			    $myAnswer->friend_question_id = $data['FriendMyAnswer']['friend_question_id'];
			    $myAnswer->save();
		    }
		    $myfriendlist = MyFriendList::find()->where(['user_id'=>$user_id,'friend_id'=>$data['MyFriendList']['friend_id']])->one();
		    if(!$myfriendlist){
			    $myfriendlist= new MyFriendList();
		    }
		    $myfriendlist->user_id = $user_id;
		    $myfriendlist->friend_id = $data['MyFriendList']['friend_id'];
		    $myfriendlist->apply_at = time();
		    $myfriendlist->is_agree = 0;
		    $myfriendlist->status = 0;
            //插入好友申请push
            if($myfriendlist->save()) {
                $push_Exists = WechatPush::find()
                            ->where(['from_id' => $myfriendlist->id, 'type' => WechatPush::FRIEND_APPLY])
                            ->exists();
                if(!$push_Exists) {
                    WechatPushService::friendApply($myfriendlist->id);
                }
            }

		    return ['status'=>1,'data'=>'保存成功'];
	    }
    }
}
