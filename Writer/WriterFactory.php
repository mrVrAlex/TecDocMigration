<?php
namespace OpsWay\TecDocMigration\Writer;

use Doctrine\DBAL\Connection;

class WriterFactory
{
    static public function create($name, $connectionRead) {
        if (!class_exists(__NAMESPACE__ . '\\' . $name)) {
            throw new \RuntimeException(sprintf('Reader "%s" does not found.' . PHP_EOL, $name));
        } else {
            $name = __NAMESPACE__ . '\\' . $name;
        }
        $instance = new $name($connectionRead);
        if (!($instance instanceof WriterInterface)) {
            throw new \RuntimeException(sprintf('Writer should implement WriterInterface'));
        }
        return $instance;
    }
}