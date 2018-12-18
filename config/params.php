<?php

return [
    'adminEmail' => 'wsd312@163.com',
    'support.email' => 'wsd312@163.com',
    'support.name' => 'My Support',
    'qiniu.upload_token_expires' => '3600',
    'qiniu.access_domain' => 'file.shudong.wang',
    'qiniu.bucket' => 'shudong',
    'qiniu.accessKey' => '',
    'qiniu.secretKey' => '',
    'user.passwordResetTokenExpire' => 3600,
    'user.emailConfirmationTokenExpire' => 43200, // 5 days
    'jwtKey' => "shudong", // 5 days
    'domain' => self::env('WECHAT_DOMAIN'),
];
