<?php

namespace OpsWay\TecDocMigration\Writer\Boodmo;

class AttrGroupWriter extends BoodmoAbstract
{

    /**
     * @param $item array
     *
     * @return bool
     */
    public function write(array $item)
    {
        return $this->connection->insert("attribute_groups", $item);
    }
}