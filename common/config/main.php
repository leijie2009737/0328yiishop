<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager'=>[
            'class'=>\yii\rbac\DbManager::className()
        ],
//        'redis' => [
//            'class' => 'yii\redis\Connection',
//            'hostname' => 'localhost',
//            'port' => 6379,
//            'database' => 0,
//        ],
    ],
];

