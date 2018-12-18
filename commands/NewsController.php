<?php
namespace app\commands;

use app\components\Common;
use app\models\News;
use app\models\Article;
use Yii;
class NewsController extends \yii\console\Controller
{

    public function actionList($keyword = '清河', $count = 20,$page = 0)
    {   
        $page = 0;
        for ($i=0; $i < 20; $i++) {
            $page+=$count;
            print_r('$page'.$page);
            $this->create($keyword,$page);
        }
    }  
    public function create($keyword = '清河', $page = 0, $count = 20)
    {   
        // $keyword = '清河';
        $url = "https://www.toutiao.com/search_content/?offset=${page}&format=json&keyword=${keyword}&autoload=true&count=${count}0&cur_tab=1";
        print_r($url);
        $data = Common::curlGet($url);
        $data = json_decode($data, true);
        foreach($data['data'] as $item){
            if (isset($item['title'])) {
                $News = new News();
                $News->title = $item['title'];
                $News->user_id = 5;
                $News->excerpt = $item['abstract'];
                $News->content = $item['abstract'];
                $News->source = $item['source'];
                // if($item['has_image']){
                $News->large_image_url = isset($item['large_image_url']) ? $item['large_image_url']: 'http://file.shudong.wang/logo.jpeg';
                $News->middle_image_url = isset($item['middle_image_url']) ?$item['middle_image_url'] : 'http://file.shudong.wang/logo.jpeg';
                $News->image_url = isset($item['image_url']) ? $item['image_url'] : 'http://file.shudong.wang/logo.jpeg';
                // }
                $News->save();
                print_r($item['title']."\n");
            }
        }
        // return json_decode($data);
        // return ['name' => 'stark'];
        // $i =  ['name'=>'stark','age'=>'18','sex'=>'man'];
        // return $i;
    }
}
