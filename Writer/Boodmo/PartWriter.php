<?php

namespace OpsWay\TecDocMigration\Writer\Boodmo;

class PartWriter extends BoodmoAbstract
{
    protected $families = [];

    /**
     * @param $item array
     *
     * @return bool
     */
    public function write(array $item)
    {
        if (isset($item['categories'])) {
            $item['categories'] = $this->getIdByCode("catalog_parts", explode(",", $item['categories']));
            if (empty($item['categories'])) {
                $item['categories'] = '{}';
            } else {
                $item['categories'] = '{' . implode(",", $item['categories']) . '}';
            }
        }
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
        foreach (['sku','name', 'family_id', 'categories'] as $generalField) {
            $data[$generalField] = $item[$generalField];
            unset($item[$generalField]);
        }
        $data['attributes'] = json_encode($item);
        return $this->connection->insert("parts", $data);
    }
}