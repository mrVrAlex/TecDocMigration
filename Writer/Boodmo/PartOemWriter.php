<?php

namespace OpsWay\TecDocMigration\Writer\Boodmo;

class PartOemWriter extends BoodmoAbstract
{
    protected $brands = [];
    protected $activeTecDocProductID = 0;
    protected $activeOemIDs = [];

    /**
     * @param $item array
     *
     * @return bool
     */
    public function write(array $item)
    {
        $fake = false;
        if ($this->activeTecDocProductID !== 0) {
            $SQL = "UPDATE parts SET oem_numbers = '{"
                .implode(',', $this->activeOemIDs)
                ."}' WHERE attributes @> '{\"td_id\": \""
                .$this->activeTecDocProductID . "\"}'";
            if (isset($item['end'])) {
                $this->connection->executeUpdate($SQL);
                return true;
            }
            if ($item['td_id'] != $this->activeTecDocProductID) {
                $this->connection->executeUpdate($SQL);
                $this->activeOemIDs = [];
            }
        }
        $this->activeTecDocProductID = $item['td_id'];

        if (empty($this->brands)) {
            foreach ($this->connection->fetchAll("SELECT id, code FROM catalog_vehicles WHERE type = 'IS_BRAND'") as $row) {
                $this->brands[$row['code']] = $row['id'];
            }
        }
        $data = ['number' => $item['number']];
        if (!isset($this->brands[$item['brand_code']])) {
            $data['brand_id'] = 11063;
            $fake = true;
        } else {
            $data['brand_id'] = $this->brands[$item['brand_code']];
        }

        $this->connection->insert("oem_numbers", $data);
        $id = $this->connection->lastInsertId('oem_numbers_id_seq');

        if ($fake) {
            @file_put_contents('/tmp/fake_brand.log',$id.','.$item['brand_code']."\n", FILE_APPEND);
        }
        $this->activeOemIDs[] = $id;
        return true;
    }
}