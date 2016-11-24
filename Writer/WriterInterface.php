<?php

namespace OpsWay\TecDocMigration\Writer;

interface WriterInterface
{
    /**
     * @param $item array
     *
     * @return bool
     */
    public function write(array $item);
}