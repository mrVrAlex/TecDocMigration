<?php

namespace OpsWay\TecDocMigration\Writer\Boodmo;

class AttributeOptionWriter extends BoodmoAbstract
{

    /**
     * @param $item array
     *
     * @return bool
     */
    public function write(array $item)
    {
        $item['attribute_id'] = $this->getIdByCode("attributes", $item['attribute']);
        if (!$item['attribute_id']) {
            throw new \RuntimeException("Attribute with code {$item['attribute']} does not exist.");
        }
        unset($item['attribute']);
        return $this->connection->insert("attribute_options", $item);
    }
}