<?php
use OpsWay\TecDocMigration\Config\DbConfiguration;
return [
    DbConfiguration::DB_TECDOC => [
        'dbname' => 'boodmo_db',
        'user' => 'root',
        'password' => 'CmZAUAMZ025E79qfWSLG',
        'host' => 'localhost',
        'driver' => 'pdo_mysql',
        'charset' => 'UTF8',
    ],
    DbConfiguration::DB_BOODMO => [
        'dbname' => 'boodmo',
        'user' => 'boodmo',
        'password' => 'qwerty',
        'host' => 'localhost',
        'port' => '5432',
        'charset' => 'UTF8',
        'driver' => 'pdo_pgsql',
    ]
];