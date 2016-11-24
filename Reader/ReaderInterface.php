<?php

namespace OpsWay\TecDocMigration\Reader;

interface ReaderInterface
{
    /**
     * @return array|null
     */
    public function read();
}