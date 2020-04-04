<?php

return [

    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => 'container_mysql',
            'port' => 3306,
            'database' => 'run_event',
            'username' => 'root',
            'password' => 'runevent@2020',
            'charset' => 'utf8'
        ],
    ]
];