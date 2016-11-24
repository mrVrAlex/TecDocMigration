<?php

namespace OpsWay\TecDocMigration\Reader\TecDocDb;

class AssocPartsReader extends TecdocDbAbstract
{
    const SQL_STMT_PROD_LIST =<<<'SQL'
SELECT
		ART_ID as `td_id`,
		ART_ARTICLE_NR,
		CONCAT(BR.BRA_MFC_CODE,'_',AL.ARL_SEARCH_NUMBER) as `sku`
	FROM
				   ARTICLES
		INNER JOIN ART_COUNTRY_SPECIFICS ON ACS_ART_ID = ART_ID
		INNER JOIN `ART_LOOKUP` AS AL ON AL.ARL_ART_ID = ART_ID AND AL.`ARL_KIND` = 1
		LEFT JOIN BRANDS as BR ON BR.BRA_ID = ART_SUP_ID
	WHERE (SUBSTRING(ART_CTM, 250+2, 1)='1')	AND (SUBSTRING(ACS_CTM, 250+2, 1)='1') AND (SUBSTRING(AL.ARL_CTM, 250+2, 1)='1')
	GROUP BY `sku`
SQL;

    const SQL_STMT_ASSOC_CARS =<<<'SQL'
SELECT CONCAT('CAR_',LINK_LA_TYP.LAT_TYP_ID) as `code` FROM `LINK_ART`
INNER JOIN LINK_LA_TYP ON LINK_LA_TYP.LAT_LA_ID = LINK_ART.LA_ID
WHERE `LA_ART_ID` = ?
SQL;

    const SQL_STMT_ASSOC_ANALOG =<<<'SQL'
SELECT DISTINCT CONCAT(BR.BRA_MFC_CODE,'_',`ARL_SEARCH_NUMBER`) as sku
FROM `ART_LOOKUP`
INNER JOIN SUPPLIERS as SUP ON SUP.SUP_ID = `ARL_BRA_ID`
LEFT JOIN BRANDS as BR ON BR.BRA_ID = `ARL_BRA_ID`
WHERE `ARL_ART_ID` = ? AND `ARL_KIND` IN (4)
AND (SUBSTRING(ARL_CTM, 250+2, 1)='1')
ORDER BY null
SQL;

    /** @var FieldNameBuilder */
    protected $fieldNameBuilder;

    public function read()
    {
        if (!$this->isReadyFetch($this->stmt)){
            $this->stmt = $this->prepareQuery(self::SQL_STMT_PROD_LIST, true);
        }
        $data = $this->stmt->fetch();
        if (!$data) return null;
        $data = array_merge($data, $this->getAssociation($data));
        return $data;
    }

    protected function getAssociation($data)
    {
        $assoc = [];
        $assocList = $this->prepareQuery(self::SQL_STMT_ASSOC_CARS, false, [$data['td_id']]);
        if ($assocList){
            $assocList = $assocList->fetchAll();
            if (count($assocList) > 0) {
                $assoc['APPLICABILITY'] = implode(",", array_column($assocList, 'code'));
            }
        }
        $assocList = $this->prepareQuery(self::SQL_STMT_ASSOC_ANALOG, false, [$data['td_id']]);
        if ($assocList){
            $assocList = $assocList->fetchAll();
            if (count($assocList) > 0) {
                $assoc['REPLACEMENT'] = implode(",", array_column($assocList, 'sku'));
            }
        }
        return $assoc;
    }


}
