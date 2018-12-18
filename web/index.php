<?php
// require('../helpers/Env.php');

// \Env::init();
// $config = \Env::webConfig();
use codemix\yii2confload\Config;
// comment out the following two lines when deployed to production
// defined('YII_DEBUG') or define('YII_DEBUG', true);
// defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
// require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

// $config = require __DIR__ . '/../config/web.php';

// (new yii\web\Application($config))->run();

$config = Config::bootstrap(__DIR__ . '/..');

Yii::createObject('yii\web\Application', [$config->web(),$config->console()])->run();
// $application = Yii::createObject('yii\console\Application', [$config->console()]);
// exit($application->run());
