<?php
namespace OpsWay\TecDocMigration;

use OpsWay\TecDocMigration\Config\DbConfiguration;
use OpsWay\TecDocMigration\Logger\ConsoleLogger;
use OpsWay\TecDocMigration\Processor\ReadWriteProcessor;
use OpsWay\TecDocMigration\Reader\ReaderFactory;
use OpsWay\TecDocMigration\Writer\WriterFactory;

include 'vendor/autoload.php';

if (PHP_SAPI !== 'cli') {
    die('This can be run only on CLI mode.'.PHP_EOL);
}

if ((!isset($argv[1])) || (!isset($argv[2]))) {
    die ('Please input required params: import.php ReaderClass WriterClass'.PHP_EOL);
}

$startTime = microtime(true);
echo "Start Time: ".date("d-m-Y H:i:s").PHP_EOL;
$dbConn = new DbConfiguration(include 'config.php');
try {
    $processor = new ReadWriteProcessor(
        ReaderFactory::create($argv[1], $dbConn->getConnection(DbConfiguration::DB_TECDOC)),
        WriterFactory::create($argv[2], $dbConn->getConnection(DbConfiguration::DB_BOODMO)),
        new ConsoleLogger()
    );
    $processor->processing();
    echo PHP_EOL;
} catch (\Exception $e) {
    echo "ERROR: ".$e->getMessage(). PHP_EOL;
}
echo "End Time: ".date("d-m-Y H:i:s").PHP_EOL;
$endTime = microtime(true);
echo "Total Time: " .  round((($endTime - $startTime))/60,3);
echo PHP_EOL;
echo "Total Peak Usage Memory: ". round(memory_get_peak_usage(true) / 1024 / 1024, 3) . PHP_EOL;