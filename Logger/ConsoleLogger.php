<?php

namespace OpsWay\TecDocMigration\Logger;

class ConsoleLogger
{
    static public $countItem = 0;

    public function __invoke($item, $status, $msg)
    {
        if ((++self::$countItem % 1000) == 0) {
            echo self::$countItem." ";
        }
        if (!$status) {
            echo "Warning: ". $msg . print_r($item, true). PHP_EOL;
        }
    }

}