<?php
namespace app\modules\v1\controllers;

use yii\rest\ActiveController;
use yii\filters\AccessControl;
use app\filters\AccessToken;
use yii\filters\VerbFilter;
use yii\base\Controller;
use app\models\Article;
use yii\data\Pagination;
use yii\web\ForbiddenHttpException;

use yii;

class PostController extends \yii\rest\ActiveController
// class ArticleController extends Controller
{

    public $modelClass='app\models\User';
        /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
                'class' => AccessToken::className(),
                'only' => ['index','create'],
                'rules' => [
                    [
                        'actions' => ['list','create'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
        ];
        return $behaviors;
    }

    public function checkAccess($action, $model=null,$params=[])
    {
        //对ActiveControllers类中默认实现了的方法进行权限设置
        if ($action === 'view')
        {
            if (\Yii::$app->user->can('ArticleViewer')) {
                return true;
            }
        }

        if ($action === 'view' || $action === 'update' || $action === 'delete'
            || $action === 'create'  || $action === 'index')
        {
            if (\Yii::$app->user->can('ArticleAdmin')) {
                return true;
            }
        }

        throw new ForbiddenHttpException('对不起，你没有进行该操作的权限。');

    }

    public function actionIndex()
    {
        return ['name'=>'stark'];
        // $i =  ['name'=>'stark','age'=>'18','sex'=>'man'];
        // return $i;
    }

    public function actionCreate()
    {
        if (!\Yii::$app->user->can('ArticleAdmin'))
        {
            throw new ForbiddenHttpException('对不起，你没有进行该操作的权限。');
        }
        $article = new Article();
        $request = Yii::$app->request;
        $post = $request->post();
        $user = Yii::$app->user;
        
        // var_dump($user->identity->username);
        // die;
        $parser = new \HyperDown\Parser;
        $article->username = $user->identity->username;
        $article->user_id = $user->id;
        $article->title = $post['title'];
        // $article->content = $post['content_md'];
        $article->content = $parser->makeHtml($post['content']);
        // $article->content = $post['content_md'];
        $article->excerpt =  $this->cutChar($post['content_md']);
        $article->content_md = $post['content'];
        $article->create_time = time();
        $article->update_time = time();
        $article->status = $post['status'];
        $article->category = $post['category'];
        $article->category_name = $post['category_name'];
        $result = $article->save();

        if ($result) {
          return ['status'=>200,'data'=>'文章创建成功'];
        }
    }

    public function actionArticleUpdate()
    {
        if (!\Yii::$app->user->can('ArticleAdmin'))
        {
            throw new ForbiddenHttpException('对不起，你没有进行该操作的权限。');
        }
        $request = Yii::$app->request;
        $post = $request->post();
        $article = Article::findOne($post['id']);
        //
        print_r($post);
        // die;
        $parser = new \HyperDown\Parser;
        $article->title = $post['title'];
        // $article->content = $post['content_md'];
        $article->content = $parser->makeHtml($post['content']);

        $article->excerpt =  $this->cutChar($post['content_md']);
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
          return ['status'=>200,'data'=>'文章更新成功'];
        }
        return ['status'=>200,'data'=>'文章更新成功1'];

    }

    		//截取摘要
		public function cutChar($document){
			$document = trim($document);
			if (strlen($document) <= 0){
			  return $document;
			}
			$search = array ("'<script[^>]*?>.*?</script>'si",  // 去掉 javascript
			                  "'<[\/\!]*?[^<>]*?>'si",          // 去掉 HTML 标记
			                  "'([\r\n])[\s]+'",                // 去掉空白字符
			                  "'/\s(?=\s)/'",
			                  "'/[nrt]/'",

			                  "'/[\n\r\t]/'",
			                  "'/s(?=s)/'",
			                  "'&(quot|#34);'i",                // 替换 HTML 实体
			                  "'&(amp|#38);'i",
			                  "'&(lt|#60);'i",
			                  "'&(gt|#62);'i",
			                  "'&(nbsp|#160);'i"
			                  );
			$replace = array ("","","\\1","\"","&","<",">"," ");
			$String=@preg_replace ($search, $replace, $document);
			return $this->sysSubStr($String,500,true);
        }



        public function sysSubStr($String,$Length,$Append = false){
    		if (strlen($String) <= $Length ){
        		return $String;
    		}else{
        		$I = 0;
        	while ($I < $Length) {
            	$StringTMP = substr($String,$I,1);
            	if (ord($StringTMP) >=224){
                	$StringTMP = substr($String,$I,3);
                	$I = $I + 3;
            	}elseif(ord($StringTMP) >=192){
                	$StringTMP = substr($String,$I,2);
                	$I = $I + 2;
            	}else{
                	$I = $I + 1;
            	}
            	$StringLast[] = $StringTMP;
        	}
        	$StringLast = implode("",$StringLast);
        	if($Append){
            	$StringLast .= "...";
        	}
        	return $StringLast;
  			}
		}


    public function  CloseTags($html) {
            // 直接过滤错误的标签 <[^>]的含义是 匹配只有<而没有>的标签
            // 而preg_replace会把匹配到的用''进行替换
            $html = preg_replace('/<[^>]*$/','',$html);

            // 匹配开始标签，这里添加了1-6，是为了匹配h1~h6标签
            preg_match_all('#<([a-z1-6]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
            $opentags = $result[1];
            // 匹配结束标签
            preg_match_all('#</([a-z1-6]+)>#iU', $html, $result);
            $closetags = $result[1];
            $len_opened = count($opentags);
            // 如何两种标签数目一致 说明截取正好
            if (count($closetags) == $len_opened) { return $html; }

            $opentags = array_reverse($opentags);
            // 过滤自闭和标签，也可以在正则中过滤 <(?!meta|img|br|hr|input)>
            $sc = array('br','input','img','hr','meta','link');

            for ($i=0; $i < $len_opened; $i++) {
                $ot = strtolower($opentags[$i]);
                if (!in_array($opentags[$i], $closetags) && !in_array($ot,$sc)) {
                    $html .= '</'.$opentags[$i].'>';
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
    public function ArticleCount($id){
        $article = Article::findOne($id);
        $article->updateCounters(['pv'=>1]);

        $session = Yii::$app->session;

        // print_r(Yii::$app->request->userIP);
        // $session->remove('userip');
        if ( !isset( $_SESSION['userip'] ) ) {
            $_SESSION['userip'] = Yii::$app->request->userIP;
            $article->updateCounters(['uv'=>1]);
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
    
        $query = Article::find()->orderBy(['id' => SORT_DESC ])->asArray();
        $countQuery = clone $query;
        $pagination = new Pagination([
                'totalCount' => $countQuery->count(),
                'pageSize' => $perPage,
            ]);

        $totalCount = $pagination->totalCount;

        $article = $query->offset($pagination->offset)
                ->limit($pagination->limit)
                ->all();

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
