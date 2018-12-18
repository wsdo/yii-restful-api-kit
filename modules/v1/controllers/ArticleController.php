<?php
namespace app\modules\v1\controllers;

use app\filters\AccessToken;
use app\models\Article;
use app\models\ArticleImg;
use app\models\User;
use app\service\ArticleService;
use codemix\yii2confload\Config;
use yii\data\Pagination;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use yii;

class ArticleController extends \yii\rest\ActiveController
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
        return ['name' => 'stark'];
        // $i =  ['name'=>'stark','age'=>'18','sex'=>'man'];
        // return $i;
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
     * 把图片存在服务器上面
     * @return [type] [description]
     */
    public function actionSaveImg()
    {
        // Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $data = $request->post();
        $img = $data['img'];
        // print_r($data);
        // die;
        // 获取七牛的token
        $token = self::GetUploadToken();
        $upToken = $token['token'];

        // 获取微信的 access_token
        $weixin_token = Config::env('WEIXIN_TOKEN');
        $appid = Config::env('WEIXIN_APP_ID');
        $secret = Config::env('WEIXIN_APP_SECRET');
        $url_get = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $secret;
        $json = $this->curlGet($url_get, '');
        $weixin_token = json_decode($json);
        // print_r($weixin_token);
        // die;
        $weixin_token = $weixin_token->access_token;
        // 从微信服务器下载
        $str = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=" . $weixin_token . "&media_id=" . $img;
        //  $str =  "https://api.weixin.qq.com/cgi-bin/media/get?access_token=".$weixin_token."&media_id=".$img;
        // $str = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".$weixin_token."&media_id=".$img;

        // 把从微信获得的数据用七牛上传到服务器上面
        $access_key = Config::env('QINIU_ACCESS_KEY');
        $secret_key = Config::env('QINIU_SECRET_KEY');
        // $this->request_by_curl($remote_server,$post_string,$upToken);
        $strr = file_get_contents($str);
        $fetch = base64_encode($strr);
        // $url = 'http://up-z1.qiniup.com/putb64/-1/'.$fetch;
        $imggg = $this->request_by_curl('http://up-z1.qiniup.com/putb64/-1', $fetch, $upToken);
        $imgs = json_decode(trim($imggg), true);
        // $qiniu = Yii::$app->qiniu;
        // $token = $qiniu->uploadFile($fetch,time().'stark');
        $imgss = $imgs['hash'];
        $imgUrl = 'http://file.shudong.wang/' . $imgss . '?imageslim';
        $thumbnail = 'http://file.shudong.wang/' . $imgss . '?imageMogr2/thumbnail/!70p';
        $result = [
            'thumbnail' => $thumbnail,
            'normal' => $imgUrl
        ];

        if ($imgss) {
            return ['status' => 1, 'message' => '图片上传成功','data' => $result];
        }

        return ['status' => 0, 'data' => '图片上传失败', 'data' => ''];
    }

    public function curlGet($url)
    {
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $temp = curl_exec($ch);
        return $temp;
    }

    public function send($url, $header = '')
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_POST, 1);
        $con = curl_exec($curl);
        if ($con === false) {
            echo 'CURL ERROR: ' . curl_error($curl);
        } else {
            return $con;
        }
    }

    public function urlsafe_base64_encode($str)
    {
        $find = array("+", "/");
        $replace = array("-", "_");
        return str_replace($find, $replace, base64_encode($str));
    }

    public function request_by_curl($remote_server, $post_string, $upToken)
    {

        $headers = array();
        //   $headers[] = 'Content-Type:image/png';
        //   $headers[] = 'application/octet-stream';
        $headers[] = 'Content-Type:multipart/form-data';
        $headers[] = 'Authorization:UpToken ' . $upToken;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_server);
        //curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
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
     * generate_access_token
     *
     * @desc 签名运算
     * @param string $access_key
     * @param string $secret_key
     * @param string $url
     * @param array  $params
     * @return string
     */
    public function generate_access_token($access_key, $secret_key, $url, $params = '')
    {
        $parsed_url = parse_url($url);
        $path = $parsed_url['path'];
        $access = $path;
        if (isset($parsed_url['query'])) {
            $access .= "?" . $parsed_url['query'];
        }
        $access .= "\n";
        if ($params) {
            if (is_array($params)) {
                $params = http_build_query($params);
            }
            $access .= $params;
        }
        $digest = hash_hmac('sha1', $access, $secret_key, true);
        return $access_key . ':' . $this->urlsafe_base64_encode($digest);
    }

    public function base64EncodeImage($image_file)
    {
        $base64_image = '';
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        return $base64_image;
    }
    public function actionCreate()
    {
        $articleService = new ArticleService();
        // if (!\Yii::$app->user->can('ArticleAdmin'))
        // {
        //     throw new ForbiddenHttpException('对不起，你没有进行该操作的权限。');
        // }
        $article = new Article();
        $request = Yii::$app->request;
        $post = $request->post();
        $user = Yii::$app->user;
        $parser = new \HyperDown\Parser;
        $article->username = $user->identity->username;
        $article->user_id = $user->identity->id;
        $article->title = 'no';
        $article->contacts = isset($post['contacts']) ? $post['contacts'] : '';
        $article->mobile = isset($post['mobile']) ? $post['mobile'] : '';
        $article->content = isset($post['content']) ? $post['content'] : '';
        $article->excerpt = isset($post['content']) ? $articleService->cutChar($post['content']) : '';
        $article->create_time = time();
        $article->update_time = time();
        $article->status = isset($post['status']) || 10;
        $article->tag_name = isset($post['tag'][0]) ? $post['tag'][0] : 'demand';
        $article->category = isset($post['category']) || 'no';
        $article->category_name = isset($post['category_name']) || 'no';
        $article->address = isset($post['address']) ? $post['address'] : '嘿，我没写地址哦！' ;
        if ($article->save()) {
            foreach($post['images'] as $item){
                $articleImg = new ArticleImg();
                $articleImg->thumbnail = isset($item['thumbnail']) ? $item['thumbnail'] : '';
                $articleImg->normal = isset($item['normal']) ? $item['normal'] : '';
                $articleImg->article_id = $article->id;
                $articleImg->status = 10;
                $articleImg->create_time = time();
                $articleImg->update_time = time();
                $articleImg->save();
            }
        }

        if ($article) {
            return ['status' => 200, 'data' => '文章创建成功'];
        } else {
            print_r($article->getErrors());
            return ['status' => 500, 'data' => '文章创建失败'];
        }
    }

    public function actionArticleUpdate()
    {
        if (!\Yii::$app->user->can('ArticleAdmin')) {
            throw new ForbiddenHttpException('对不起，你没有进行该操作的权限。');
        }
        $request = Yii::$app->request;
        $post = $request->post();
        $article = Article::findOne($post['id']);
        //
        // print_r($post);
        // die;
        $parser = new \HyperDown\Parser;
        // $article->title = $post['title'];
        // $article->content = $post['content_md'];
        $article->content = $parser->makeHtml($post['content']);

        $article->excerpt = $this->cutChar($post['content_md']);
        // print_r( $article->excerpt);
        // print_r(getPreg())
        // die;
        $article->content_md = $post['content'];
        // $article->create_time = time();
        $article->update_time = time();
        $article->status = $post['status'];
        // $article->content_md = $parser->makeHtml($post['content']);;
        $article->tag_id = $post['tag_id'];
        $result = $article->save();

        if ($result) {
            return ['status' => 200, 'data' => '文章更新成功'];
        }
        return ['status' => 200, 'data' => '文章更新成功1'];

    }

    public function CloseTags($html)
    {
        // 直接过滤错误的标签 <[^>]的含义是 匹配只有<而没有>的标签
        // 而preg_replace会把匹配到的用''进行替换
        $html = preg_replace('/<[^>]*$/', '', $html);

        // 匹配开始标签，这里添加了1-6，是为了匹配h1~h6标签
        preg_match_all('#<([a-z1-6]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $opentags = $result[1];
        // 匹配结束标签
        preg_match_all('#</([a-z1-6]+)>#iU', $html, $result);
        $closetags = $result[1];
        $len_opened = count($opentags);
        // 如何两种标签数目一致 说明截取正好
        if (count($closetags) == $len_opened) {return $html;}

        $opentags = array_reverse($opentags);
        // 过滤自闭和标签，也可以在正则中过滤 <(?!meta|img|br|hr|input)>
        $sc = array('br', 'input', 'img', 'hr', 'meta', 'link');

        for ($i = 0; $i < $len_opened; $i++) {
            $ot = strtolower($opentags[$i]);
            if (!in_array($opentags[$i], $closetags) && !in_array($ot, $sc)) {
                $html .= '</' . $opentags[$i] . '>';
            } else {
                unset($closetags[array_search($opentags[$i], $closetags)]);
            }
        }
        return $html;
    }
    /**
     * 统计文章次数
     * $id 文章id
     */
    public function ArticleCount($id)
    {
        $article = Article::findOne($id);
        $article->updateCounters(['pv' => 1]);

        $session = Yii::$app->session;

        // print_r(Yii::$app->request->userIP);
        // $session->remove('userip');
        if (!isset($_SESSION['userip'])) {
            $_SESSION['userip'] = Yii::$app->request->userIP;
            $article->updateCounters(['uv' => 1]);
        }
        $article->save();
    }

    public function actionDesc($id)
    {
        // $request = Yii::$app->request;
        // $get = $request->get();
        // print_r($get['id']);
        // die;
        // $id = $get['id'];
        // print_r(Yii::$app->user);
        // $user = Yii::$app->user;
        $this->ArticleCount($id);
        $article = Article::findOne($id);
        return $article;
    }

    public function actionList($perPage = 20)
    {
        $request = Yii::$app->request;
        $get = $request->get();

        $query = Article::find()->orderBy(['id' => SORT_DESC])->asArray();
        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => $perPage,
        ]);

        $totalCount = $pagination->totalCount;

        $article = $query->offset($pagination->offset)
            ->with(['user','image'])
            ->limit($pagination->limit)
            ->all();
        // $articleImg = ArticleImg::findOne()
        return [
            'article' => $article,
            'totalCount' => $totalCount,
        ];
    }

    public function actions()
    {

        $actions = parent::actions();

        // 全部的API都手动写出来,然后用权限控制
        unset($actions['delete'], $actions['create'], $actions['index'], $actions['view'], $actions['update']);

        return $actions;
    }
}
