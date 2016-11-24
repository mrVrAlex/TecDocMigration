<?php

namespace OpsWay\TecDocMigration\Writer\Boodmo;

class CatalogVehicleWriter extends BoodmoAbstract
{

    /**
     * @param $item array
     *
     * @return bool
     */
    public function write(array $item)
    {
        if (isset($item['parent']) || ($item['parent'] !== null)) {
            $item['parent_id'] = $this->getIdByCode("catalog_vehicles", $item['parent']);
            if (!$item['parent_id']) {
                throw new \RuntimeException("parent_id with code {$item['parent']} does not exist.");
            }
        }
        unset($item['parent']);
        return $this->connection->insert("catalog_vehicles", $item);
    }
}