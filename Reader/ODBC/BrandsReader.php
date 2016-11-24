<?php

namespace OpsWay\TecDocMigration\Reader\ODBC;

class BrandsReader extends DbAbstract
{
    const SQL_STMT =<<<'SQL'
SELECT
  ('brand_' + (MFA_ID cast string)) as code,
    MFA_BRAND as name,
    'tecdoc_cars' as parent,
    'IS_BRAND' as type,
    MFA_PC_MFC,
    MFA_CV_MFC
FROM
    TOF_MANUFACTURERS
WHERE
    ((MFA_PC_MFC=1))
    OR ((MFA_CV_MFC=1))
ORDER BY MFA_BRAND;
SQL;

    const SQL_STMT2 =<<<'SQL'
	SELECT DISTINCT
		CONCAT('model_',TOF_MODELS.MOD_ID) as code,
		CONCAT('brand_',TOF_MODELS.MOD_MFA_ID) as parent,
        'IS_MODEL' as `type`,
				CONCAT(
            TOF_DES_TEXTS.TEX_TEXT,
            IFNULL(CONCAT(
                CONCAT(
                    ' (',
                    SUBSTRING(TOF_MODELS.MOD_PCON_START,1,4)
                ),
                IFNULL(
                   	CONCAT(
                       	' - ',
                       	SUBSTRING(TOF_MODELS.MOD_PCON_END,1,4),
                       	')'
                    ),
                    ')'
               )
            ),'')

        )
        		AS `name`
    FROM
			TOF_MODELS
		INNER JOIN TOF_MANUFACTURERS ON (TOF_MODELS.MOD_MFA_ID=TOF_MANUFACTURERS.MFA_ID)
		INNER JOIN TOF_COUNTRY_DESIGNATIONS ON TOF_COUNTRY_DESIGNATIONS.CDS_ID = TOF_MODELS.MOD_CDS_ID
		INNER JOIN TOF_DES_TEXTS ON TOF_DES_TEXTS.TEX_ID = TOF_COUNTRY_DESIGNATIONS.CDS_TEX_ID
	WHERE
		(TOF_MANUFACTURERS.MFA_ID = ? ) AND (TOF_COUNTRY_DESIGNATIONS.CDS_LNG_ID=4)
		AND (((TOF_MODELS.MOD_PC=1) AND (SUBSTRING(TOF_MOD_PC_CTM, 250+2, 1)='1'))  -- only passanger cars
			OR ((TOF_MODELS.MOD_CV=1) AND (SUBSTRING(TOF_MOD_CV_CTM, 250+2, 1)='1'))) -- only truck cars
		AND (SUBSTRING(TOF_CDS_CTM, 250+2, 1)='1')
	GROUP BY code ORDER BY `name`;
SQL;

    const ROOT_CODE = 'tecdoc_cars';

    protected $brandCat = array();
    protected $dynamicTree = array();
    protected $results = array();

    public function read()
    {
        $item = null;
        if (!$this->isReadyFetch($this->stmt)){
            $this->stmt = $this->prepareQuery(self::SQL_STMT);
            $data = $this->getRootCategory();
            $this->brandCat = $this->stmt->fetchAll();
            $this->dynamicTree = $this->brandCat;
            array_unshift($this->dynamicTree, $data);
            $this->results = $this->dynamicTree;
            
            while ($row = array_shift($this->brandCat)) {
                $this->stmt = $this->connection->prepare(self::SQL_STMT2);
                $this->stmt->bindValue(1, str_replace('brand_', '', $row['code']));
                $this->stmt->execute();
                if (!$this->stmt) {
                    break;
                }
                $this->dynamicTree = $this->stmt->fetchAll();
                if (!$this->dynamicTree) {
                    break;
                }
                $this->results = array_merge($this->results, $this->dynamicTree);
            }
        }

        $item = array_shift($this->results);
        if ($item) {
            if (isset($item['MFA_PC_MFC']) && isset($item['MFA_CV_MFC'])) {
                if (($item['MFA_PC_MFC'] == 1) && ($item['MFA_CV_MFC'] == 1)) {
                    $item['attributes'] = '{"brand_listing":[11339,11338]}';
                } elseif ($item['MFA_PC_MFC'] == 1) {
                    $item['attributes'] = '{"brand_listing":[11338]}';
                } elseif ($item['MFA_CV_MFC'] == 1) {
                    $item['attributes'] = '{"brand_listing":[11339]}';
                }
                unset($item['MFA_PC_MFC']);
                unset($item['MFA_CV_MFC']);
            }
        }
        return $item;
    }

    protected function getRootCategory()
    {
        return ['code' => self::ROOT_CODE, 'parent' => null, 'name' => 'TecDoc CARS Catalog', 'type' => 'ROOT'];
    }
}
