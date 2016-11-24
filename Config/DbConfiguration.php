<?php
namespace OpsWay\TecDocMigration\Config;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

class DbConfiguration
{
    const DB_TECDOC = 'TECDOC';
    const DB_BOODMO = 'BOODMO_PG';

    /**
     * @var \Doctrine\DBAL\Connection[]
     */
    protected $connections = [];

    /**
     * @param $config array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __construct(array $config)
    {
        foreach ($config as $dbKey => $connectionParams) {
            $this->connections[$dbKey] = DriverManager::getConnection($connectionParams, new Configuration());
        }
    }

    /**
     * @param $database string
     *
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection($database) {
        if (!isset($this->connections[$database])) {
            throw new \BadMethodCallException('Database key does not exists.');
        }
        return $this->connections[$database];
    }


}