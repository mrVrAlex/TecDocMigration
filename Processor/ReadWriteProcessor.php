<?php
namespace OpsWay\TecDocMigration\Processor;

use OpsWay\TecDocMigration\Reader\ReaderInterface;
use OpsWay\TecDocMigration\Writer\WriterInterface;

class ReadWriteProcessor
{
    /**
     * @var ReaderInterface
     */
    protected $reader;
    /**
     * @var WriterInterface
     */
    protected $writer;
    /**
     * @var callable
     */
    protected $logger;

    /**
     * @param ReaderInterface $reader
     * @param WriterInterface $writer
     * @param callable        $logger
     */
    public function __construct(ReaderInterface $reader, WriterInterface $writer, $logger)
    {
        $this->setReader($reader);
        $this->setWriter($writer);
        $this->setLogger($logger);
    }

    public function processing()
    {
        while ($item = $this->reader->read()){
            try {
                $status = $this->writer->write($item);
                $msg = '';
            } catch (\Exception $e) {
                $status = false;
                $msg = $e->getMessage();
            }
            call_user_func($this->logger, $item, $status, $msg);
        }
    }

    /**
     * @param WriterInterface $writer
     */
    public function setWriter($writer)
    {
        $this->writer = $writer;
    }

    /**
     * @param callable $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param ReaderInterface $reader
     */
    public function setReader($reader)
    {
        $this->reader = $reader;
    }
}