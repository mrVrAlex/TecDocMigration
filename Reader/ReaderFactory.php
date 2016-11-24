<?php
namespace OpsWay\TecDocMigration\Reader;

use Doctrine\DBAL\Connection;

class ReaderFactory
{
    static public function create($name, $connectionRead) {
        if (!class_exists(__NAMESPACE__ . '\\'.$name)) {
            throw new \RuntimeException(sprintf('Reader "%s" does not found.'.PHP_EOL, $name));
        } else {
            $name = __NAMESPACE__ . '\\'.$name;
        }
        $instance = new $name($connectionRead);
        if (!($instance instanceof ReaderInterface)) {
            throw new \RuntimeException(sprintf('Reader should implement ReaderInterface'));
        }
        return $instance;
    }
}