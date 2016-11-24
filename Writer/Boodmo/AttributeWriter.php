<?php

namespace OpsWay\TecDocMigration\Writer\Boodmo;

class AttributeWriter extends BoodmoAbstract
{

    /**
     * @param $item array
     *
     * @return bool
     */
    public function write(array $item)
    {
        $item['group_id'] = $this->getIdByCode("attribute_groups", $item['group']);
        if (!$item['group_id']) {
            throw new \RuntimeException("Attribute Group with code {$item['group']} does not exist.");
        }
        unset($item['group']);
        return $this->connection->insert("attributes", $item);
    }
}