<?php

namespace OpsWay\TecDocMigration\Writer;

class Stub implements WriterInterface
{

    /**
     * @param $item array
     *
     * @return bool
     */
    public function write(array $item)
    {
        return true;
    }

}
