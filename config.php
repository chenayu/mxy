<?php
return [
    'mode' => 'dev',     //  dev （开发模式）   pro（上线模式)
    'redis' => [
        'scheme' => 'tcp',
        'host'   => '127.0.0.1',
        'port'   => 6379,
    ],
    'db' => [
        'host' => '127.0.0.1',
        'dbname' => 'blog',
        'user' => 'root',
        'pass' => '123456',
        'charset' => 'utf8',
    ],
    'email' => [
        'mode' => '',    // 值：debug  和 production
        'port' => 25,
        'host' => 'smtp.126.com',
        'name' => 'czxy_qz@126.com',
        'pass' => '12345678abcdefg',
        'from_email' => 'czxy_qz@126.com',
        'from_name' => '全栈1班',
    ]
];

?>