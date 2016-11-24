<?php

namespace OpsWay\TecDocMigration\Writer\Boodmo;

class FamilyWriter extends BoodmoAbstract
{

    /**
     * @param $item array
     *
     * @return bool
     */
    public function write(array $item)
    {
        $item['attributes'] = $this->getIdByCode("attributes", explode(",",$item['attributes']));
        if (empty($item['attributes'])) {
            throw new \RuntimeException("Attributes code not found.");
        }
        $item['attributes'] = '{' . implode(",",$item['attributes']) . '}';
        return $this->connection->insert("families", $item);
    }
}