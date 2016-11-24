<?php

namespace OpsWay\TecDocMigration\Writer\Boodmo;

class PartAssociationWriter extends BoodmoAbstract
{
    protected $families = [];

    /**
     * @param $item array
     *
     * @return bool
     */
    public function write(array $item)
    {
        if ((!isset($item['APPLICABILITY'])) && (!isset($item['REPLACEMENT']))){
            return true;
        }
        $data = [];
        if (isset($item['APPLICABILITY'])) {
            $item['applicability'] = $this->getIdByCode("vehicles", explode(",", $item['APPLICABILITY']));
            if (!empty($item['applicability'])) {
                $data['applicability'] = json_encode(['vehicle' => $item['applicability'], 'category' => []]);
            }
        }

        if (isset($item['REPLACEMENT'])) {
            $item['replacement'] = $this->getIdByCode("parts", explode(",", $item['REPLACEMENT']), 'sku');
            if (!empty($item['replacement'])) {
                $data['replacement'] = '{' . implode(",",$item['replacement']) . '}';
            }
        }

        if (count($data) == 0) {
            return true;
        }

        return $this->connection->update("parts", $data, ['sku' => $item['sku']]);
    }
}