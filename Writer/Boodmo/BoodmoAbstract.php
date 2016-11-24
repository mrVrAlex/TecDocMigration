<?php

namespace OpsWay\TecDocMigration\Writer\Boodmo;

use Doctrine\DBAL\Connection;
use OpsWay\TecDocMigration\Writer\WriterInterface;

abstract class BoodmoAbstract implements WriterInterface
{
    /**
     *
     * @var Connection
     */
    protected $connection;

    public function __construct(Connection $dbalConnection)  {
        $this->connection = $dbalConnection;
    }
    /**
     * @param $item array
     *
     * @return bool
     */
    abstract public function write(array $item);

    public function getIdByCode($table, $code, $field = 'code')
    {
        if (is_array($code)) {
            $stmt = $this->connection->executeQuery(
                "SELECT id FROM $table WHERE $field IN (?)",
                [$code],
                [Connection::PARAM_STR_ARRAY]
            );
            return array_map(function ($row) {
                return $row['id'];
            }, $stmt->fetchAll());
        }
        return $this->connection->fetchColumn("SELECT id FROM $table WHERE $field = ?", [$code]);
    }

}