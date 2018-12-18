<?php
$password = self::env('DB_PASSWORD') == null ? '' : self::env('DB_PASSWORD');
$host = self::env('DB_HOST');
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host='.$host.';dbname='.self::env('DB_NAME'),
    'username' => self::env('DB_USER'),
    'password' => $password,
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
