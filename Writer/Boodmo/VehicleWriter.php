<?php

namespace OpsWay\TecDocMigration\Writer\Boodmo;

class VehicleWriter extends BoodmoAbstract
{
    protected $families = [];

    /**
     * @param $item array
     *
     * @return bool
     */
    public function write(array $item)
    {

        $item['categories'] = $this->getIdByCode("catalog_vehicles", explode(",",$item['categories']));
        if (empty($item['categories'])) {
            throw new \RuntimeException("Categories codes {$item['categories']} not found.");
        }
        $item['categories'] = '{' . implode(",",$item['categories']) . '}';
        if (!isset($this->families[$item['family']])) {
            $id = $this->getIdByCode("families",$item['family']);
            if (empty($id)){
                throw new \RuntimeException("Family with code {$item['family']} not found.");
            }
            $this->families[$item['family']] = $id;
        }
        $item['family_id'] = $this->families[$item['family']];
        unset($item['family']);
        $data = [];
        foreach (['code','name', 'family_id', 'categories'] as $generalField) {
            $data[$generalField] = $item[$generalField];
            unset($item[$generalField]);
        }
        $data['attributes'] = json_encode($item);
        return $this->connection->insert("vehicles", $data);
    }
}