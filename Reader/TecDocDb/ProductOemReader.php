<?php

namespace OpsWay\TecDocMigration\Reader\TecDocDb;

class ProductOemReader extends TecdocDbAbstract
{
    protected $processed = false;
    const SQL_STMT_PROD_LIST =<<<'SQL'
SELECT `ARL_ART_ID` as `td_id`, `ARL_SEARCH_NUMBER` as `sku`, `ARL_DISPLAY_NR`, CONCAT('td_sup_',`ARL_BRA_ID`) as `brand_code` FROM `ART_LOOKUP` WHERE `ARL_KIND` = 3
	ORDER BY ARL_ART_ID ASC
SQL;

    public function read()
    {
        if ($this->processed) {
            return null;
        }

        if (!$this->isReadyFetch($this->stmt)){
            $this->stmt = $this->prepareQuery(self::SQL_STMT_PROD_LIST);
        }
        $data = $this->stmt->fetch();
        if (!$data) {
            $this->processed = true;
            return ['end' => $this->processed];
        }
        return $data;
    }

}
