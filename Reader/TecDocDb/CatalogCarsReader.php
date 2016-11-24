<?php

namespace OpsWay\TecDocMigration\Reader\TecDocDb;

class CatalogCarsReader extends TecdocDbAbstract
{
    const SQL_STMT =<<<'SQL'
    SELECT
        CONCAT('brand_',MFA_ID) as code,
        MFA_BRAND as `name`,
        'tecdoc_cars' as `parent`,
        'IS_BRAND' as `type`,
        MFA_PC_MFC,
        MFA_CV_MFC
    FROM
        MANUFACTURERS
    WHERE
        ((MFA_PC_MFC=1) and (SUBSTRING(MFA_PC_CTM, 250+2, 1)='1'))
        OR ((MFA_CV_MFC=1) and (SUBSTRING(MFA_CV_CTM, 250+2, 1)='1'))
    ORDER BY MFA_BRAND;
SQL;

    const SQL_STMT2 =<<<'SQL'
	SELECT DISTINCT
		CONCAT('model_',MODELS.MOD_ID) as code,
		CONCAT('brand_',MODELS.MOD_MFA_ID) as parent,
        'IS_MODEL' as `type`,
				CONCAT(
            DES_TEXTS.TEX_TEXT,
            IFNULL(CONCAT(
                CONCAT(
                    ' (',
                    SUBSTRING(MODELS.MOD_PCON_START,1,4)
                ),
                IFNULL(
                   	CONCAT(
                       	' - ',
                       	SUBSTRING(MODELS.MOD_PCON_END,1,4),
                       	')'
                    ),
                    ')'
               )
            ),'')

        )
        		AS `name`
    FROM
			MODELS
		INNER JOIN MANUFACTURERS ON (MODELS.MOD_MFA_ID=MANUFACTURERS.MFA_ID)
		INNER JOIN COUNTRY_DESIGNATIONS ON COUNTRY_DESIGNATIONS.CDS_ID = MODELS.MOD_CDS_ID
		INNER JOIN DES_TEXTS ON DES_TEXTS.TEX_ID = COUNTRY_DESIGNATIONS.CDS_TEX_ID
	WHERE
		(MANUFACTURERS.MFA_ID = ? ) AND (COUNTRY_DESIGNATIONS.CDS_LNG_ID=4)
		AND (((MODELS.MOD_PC=1) AND (SUBSTRING(MOD_PC_CTM, 250+2, 1)='1'))  -- only passanger cars
			OR ((MODELS.MOD_CV=1) AND (SUBSTRING(MOD_CV_CTM, 250+2, 1)='1'))) -- only truck cars
		AND (SUBSTRING(CDS_CTM, 250+2, 1)='1')
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
