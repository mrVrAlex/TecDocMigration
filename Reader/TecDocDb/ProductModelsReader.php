<?php

namespace OpsWay\TecDocMigration\Reader\TecDocDb;

class ProductModelsReader extends TecdocDbAbstract
{
    const SQL_STMT =<<<'SQL'
	SELECT DISTINCT
		CONCAT('ct_c_',MODELS.MOD_ID,'_',MODELS.MOD_MFA_ID) as sku,
        DES_TEXTS.TEX_TEXT AS `name`,
        MODELS.MOD_PCON_START as TYP_PCON_START,
        MODELS.MOD_PCON_END as TYP_PCON_END,
       `MOD_PC`,
       `MOD_CV`,
		'IS_MODEL' as family,
		'1' as enabled
	FROM
			MODELS
		INNER JOIN COUNTRY_DESIGNATIONS ON COUNTRY_DESIGNATIONS.CDS_ID = MODELS.MOD_CDS_ID
		INNER JOIN DES_TEXTS ON DES_TEXTS.TEX_ID = COUNTRY_DESIGNATIONS.CDS_TEX_ID
	WHERE
		(((MODELS.MOD_PC=1) AND (SUBSTRING(MOD_PC_CTM, 250+2, 1)='1'))  			OR ((MODELS.MOD_CV=1) AND (SUBSTRING(MOD_CV_CTM, 250+2, 1)='1'))) 		AND (SUBSTRING(CDS_CTM, 250+2, 1)='1')
	GROUP BY sku
SQL;

    public function read()
    {
        if (!$this->isReadyFetch($this->stmt)){
            $this->stmt = $this->prepareQuery(self::SQL_STMT);
        }
        $data = $this->stmt->fetch();
        if (!$data) return null;

        $data['MODELTYPE'] = ($data['MOD_PC'])?'car':'truck';
        unset($data['MOD_PC']);
        unset($data['MOD_CV']);
        $data['categories'] = $data['sku'];
        if ($this->stepExecution) {
            $this->stepExecution->incrementSummaryInfo('read');
        }
        return $data;
    }
}
