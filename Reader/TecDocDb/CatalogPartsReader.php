<?php

namespace OpsWay\TecDocMigration\Reader\TecDocDb;

class CatalogPartsReader extends TecdocDbAbstract
{
    const SQL_STMT =<<<'SQL'
SELECT CONCAT('ct_',STR_ID) as code, CONCAT('ct_',STR_ID_PARENT) as parent, DES_TEXTS.TEX_TEXT as `name`
FROM `SEARCH_TREE`
LEFT JOIN DESIGNATIONS ON DESIGNATIONS.DES_ID = SEARCH_TREE.STR_DES_ID AND DESIGNATIONS.DES_LNG_ID = 4
LEFT JOIN DES_TEXTS ON DES_TEXTS.TEX_ID = DESIGNATIONS.DES_TEX_ID
ORDER BY SEARCH_TREE.STR_SORT ASC
SQL;

    const ROOT_CODE = 'tecdoc_parts';

    public function read()
    {
        if (!$this->isReadyFetch($this->stmt)){
            $this->stmt = $this->prepareQuery(self::SQL_STMT);
            $data = $this->getRootCategory();
        } else {
            $data = $this->stmt->fetch();
            if (!$data) return null;
            if (!$data['parent']) $data['parent'] = self::ROOT_CODE;
        }
        return $data;
    }

    protected function getRootCategory()
    {
        return ['code' => self::ROOT_CODE, 'parent' => null, 'name' => 'Root TecDoc Catalog'];
    }
}
