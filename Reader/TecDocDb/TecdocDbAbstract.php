<?php

namespace OpsWay\TecDocMigration\Reader\TecDocDb;

use Doctrine\DBAL\Connection;
use OpsWay\TecDocMigration\Reader\ReaderInterface;

abstract class TecdocDbAbstract implements ReaderInterface
{
    protected static $STATE_IMPORT_MODE = 0;
    /**
     *
     * @var Connection
     */
    protected $connection;

    protected $stepExecution = false;
    /**
     * @var \Doctrine\DBAL\Driver\Statement|null
     */
    protected $stmt = null;
    /**
     * @var Integer
     */
    protected $importSize = 5000000;
    /**
     * @var Integer
     */
    protected $startOffset = 0;

    public function __construct(Connection $dbalConnection)  {
        $this->connection = $dbalConnection;
    }

    protected function prepareQuery($query, $useImportLimit = false, $binding = [])
    {
        if ($useImportLimit) {
            $query .= ' LIMIT '.$this->importSize . ' OFFSET '. $this->startOffset;
        }
        if (!empty($binding)) {
            if (substr_count($query,'?') != count($binding)){
                $query = str_replace('?', implode(',', array_fill(0, count($binding), '?')) ,$query);
            }
            $stmt = $this->connection->prepare($query);
            foreach ($binding as $param => $value) {
                $stmt->bindValue($param+1, $value);
            }
            $stmt->execute();
            return $stmt;
        }
        return $this->connection->query($query);
    }

    protected function isReadyFetch($statement)
    {
        self::$STATE_IMPORT_MODE = 1;
        return !is_null($statement);
    }

    /**
     * @return array
     */
    abstract public function read();

    /**
     * @return Int
     */
    public function getImportSize()
    {
        return $this->importSize;
    }

    /**
     * @param Int $importSize
     */
    public function setImportSize($importSize)
    {
        $this->importSize = $importSize;
    }

    /**
     * @return int
     */
    public function getStartOffset()
    {
        return $this->startOffset;
    }

    /**
     * @param int $startOffset
     */
    public function setStartOffset($startOffset)
    {
        $this->startOffset = $startOffset;
    }

    public static function getImportState()
    {
        return self::$STATE_IMPORT_MODE;
    }

    /**
     * Destructor
     * @return void
     */
    public function __destruct()
    {
        self::$STATE_IMPORT_MODE = 0;
    }
}