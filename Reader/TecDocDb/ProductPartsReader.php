<?php

namespace OpsWay\TecDocMigration\Reader\TecDocDb;

class ProductPartsReader extends TecdocDbAbstract
{
    const MAX_MEDIA_IMAGES = 10;
    const SQL_STMT_PROD_LIST =<<<'SQL'
SELECT
		CONCAT('ga_',GA.LAG_GA_ID) as `family`,
		ART_ID as `td_id`,
		ART_ARTICLE_NR,
		CONCAT(BR.BRA_MFC_CODE,'_',AL.ARL_SEARCH_NUMBER) as `sku`,
		DES_TEXTS.TEX_TEXT AS `name`,
		DES_TEXTS2.TEX_TEXT AS `description`,
		LOWER(REPLACE(DES_TEXTS3.TEX_TEXT,' ','_')) AS `td_status`,
		AL2.ARL_DISPLAY_NR as `EAN`,
CONCAT('td_sup_', `SUP_ID`) as `supplier`,
CONCAT('td_ft_',GA.LAG_GA_ID) as `fitting_place`,
CONCAT('td_gr_',GA.LAG_GA_ID) as `parts_group`,
GROUP_CONCAT(DISTINCT GA.LAG_GA_ID) as `GA_ID`,
GROUP_CONCAT(CONCAT(GRA.GRA_SUPID,'/',ART_ID,GRA.GRA_PREFICS,GRA.GRA_EXT)) as `PATHIMAGES`,
`ART_PACK_SELFSERVICE`,
`ART_MATERIAL_MARK`,
`ART_REPLACEMENT`,
`ART_ACCESSORY`
	FROM
				   ARTICLES
		INNER JOIN DESIGNATIONS ON DESIGNATIONS.DES_ID = ART_COMPLETE_DES_ID
							   AND DESIGNATIONS.DES_LNG_ID = 4
		INNER JOIN DES_TEXTS ON DES_TEXTS.TEX_ID = DESIGNATIONS.DES_TEX_ID
		 LEFT JOIN DESIGNATIONS AS DESIGNATIONS2 ON DESIGNATIONS2.DES_ID = ART_DES_ID
												AND DESIGNATIONS2.DES_LNG_ID = 4
		 LEFT JOIN DES_TEXTS AS DES_TEXTS2 ON DES_TEXTS2.TEX_ID = DESIGNATIONS2.DES_TEX_ID
		INNER JOIN SUPPLIERS ON SUP_ID = ART_SUP_ID
		INNER JOIN ART_COUNTRY_SPECIFICS ON ACS_ART_ID = ART_ID
		INNER JOIN DESIGNATIONS AS DESIGNATIONS3 ON DESIGNATIONS3.DES_ID = ACS_KV_STATUS_DES_ID
												AND DESIGNATIONS3.DES_LNG_ID = 4
		INNER JOIN DES_TEXTS AS DES_TEXTS3 ON DES_TEXTS3.TEX_ID = DESIGNATIONS3.DES_TEX_ID
		INNER JOIN `ART_LOOKUP` AS AL ON AL.ARL_ART_ID = ART_ID AND AL.`ARL_KIND` = 1
        LEFT JOIN `ART_LOOKUP` AS AL2 ON AL2.ARL_ART_ID = ART_ID AND AL2.ARL_KIND = 5 AND (SUBSTRING(AL2.ARL_CTM, 250+2, 1)='1')
		LEFT JOIN BRANDS as BR ON BR.BRA_ID = ART_SUP_ID
        LEFT JOIN LINK_ART_GA as GA ON GA.LAG_ART_ID = ART_ID
        LEFT JOIN GRAFORARTICLES as GRA ON GRA.GRA_ARTID = ART_ID
	WHERE (SUBSTRING(ART_CTM, 250+2, 1)='1')	AND (SUBSTRING(ACS_CTM, 250+2, 1)='1') AND (SUBSTRING(AL.ARL_CTM, 250+2, 1)='1')
	GROUP BY `sku`
	ORDER BY ART_ID ASC
SQL;

    const SQL_STMT_PROD_INFO =<<<'SQL'
SELECT
    CONCAT('CRI_',CRI_ID) as `code`,
    IFNULL(DES_TEXTS2.TEX_TEXT, ACR_VALUE) AS `value`
FROM
              ARTICLE_CRITERIA
    LEFT JOIN DESIGNATIONS AS DESIGNATIONS2 ON DESIGNATIONS2.DES_ID = ACR_KV_DES_ID
    LEFT JOIN DES_TEXTS AS DES_TEXTS2 ON DES_TEXTS2.TEX_ID = DESIGNATIONS2.DES_TEX_ID
    LEFT JOIN CRITERIA ON CRI_ID = ACR_CRI_ID
    LEFT JOIN DESIGNATIONS ON DESIGNATIONS.DES_ID = CRI_DES_ID
    LEFT JOIN DES_TEXTS ON DES_TEXTS.TEX_ID = DESIGNATIONS.DES_TEX_ID
WHERE
    ACR_ART_ID = ? AND
    (DESIGNATIONS.DES_LNG_ID IS NULL OR DESIGNATIONS.DES_LNG_ID = 4) AND
    (DESIGNATIONS2.DES_LNG_ID IS NULL OR DESIGNATIONS2.DES_LNG_ID = 4)
    AND (SUBSTRING(ACR_CTM, 250+2, 1)='1')
SQL;

    const SQL_STMT_PROD_CAT =<<<'SQL'
SELECT DISTINCT CONCAT('ct_',LGS_STR_ID) as `code` FROM `LINK_GA_STR` WHERE `LGS_GA_ID` IN (?)
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

    const SQL_STMT_REPLACE_NUM =<<<'SQL'
SELECT GROUP_CONCAT(`SUA_NUMBER`) as `replaced_articles` FROM `SUPERSEDED_ARTICLES`
WHERE (SUBSTRING(SUA_CTM, 250+2, 1)='1') AND `SUA_ART_ID` = ?
GROUP BY `SUA_ART_ID`
SQL;

    public function read()
    {
        if (!$this->isReadyFetch($this->stmt)){
            $this->stmt = $this->prepareQuery(self::SQL_STMT_PROD_LIST, true);
        }
        $data = $this->stmt->fetch();
        if (!$data) return null;
        $listInfo = $this->prepareQuery(self::SQL_STMT_PROD_INFO, false, [$data['td_id']]);
        if ($listInfo) {
            foreach ($listInfo->fetchAll() as $row) {
                $data[$row['code']] = $row['value'];
            }
        }
        $catList = $this->prepareQuery(self::SQL_STMT_PROD_CAT, false, explode(",",$data['GA_ID']));
        if ($catList){
            $data['categories'] = implode(",",array_column($catList->fetchAll(),'code'));
        } else {
            @file_put_contents('/tmp/product_without_cat.txt', $data['td_id'] . ',', FILE_APPEND);
        }
        $replacement = $this->prepareQuery(self::SQL_STMT_REPLACE_NUM, false, [$data['td_id']]);
        if ($replacement){
            $replacement = $replacement->fetch();
            if (isset($replacement['replaced_articles'])) {
                $data['replaced_articles'] = $replacement['replaced_articles'];
            }
        }
        $data = array_merge($data, $this->getMedia($data));
        //$data = array_merge($data, $this->getAssociation($data));
        $data['name'] = substr($data['name'],0, 254);
        //unset($data['description']);
        unset($data['GA_ID']);
        unset($data['PATHIMAGES']);

        return $data;
    }

    protected function getAssociation($data)
    {
        $assoc = [];
        $assocList = $this->prepareQuery(self::SQL_STMT_ASSOC_CARS, false, [$data['td_id']]);
        if ($assocList){
            $assocList = $assocList->fetchAll();
            if (count($assocList) > 0) {
                $assoc['APPLICABILITY-products'] = implode(",", array_column($assocList, 'code'));
            }
        }
        $assocList = $this->prepareQuery(self::SQL_STMT_ASSOC_ANALOG, false, [$data['td_id']]);
        if ($assocList){
            $assocList = $assocList->fetchAll();
            if (count($assocList) > 0) {
                $assoc['REPLACEMENT-products'] = implode(",", array_column($assocList, 'sku'));
            }
        }
        return $assoc;
    }

    protected function getMedia($data)
    {
        if (!isset($data['PATHIMAGES']) || !$data['PATHIMAGES'] || (strtoupper($data['PATHIMAGES']) == 'NULL')) {
            return [];
        }
        $url = "http://media.boodmo.clients.opsway.com/img/";
        $folder = "/var/www/shared/media/tecdoc/";
        $mediaPath = array_reverse(explode(",",$data['PATHIMAGES']));
        $media = []; $i = 0;
        foreach ($mediaPath as $img){
            //if ($i > self::MAX_MEDIA_IMAGES) break;
            $fullpath = $folder . $img;
            /*
            // --------------------------------
            // Uncomment this Only for TEST server
            // --------------------------------
            if (!is_dir(dirname($fullpath))) {
                @mkdir(dirname($fullpath), 0777, true);
            }
            if (!file_exists($fullpath)) {
                @file_put_contents($fullpath, @fopen($url . $img, 'r'));
            }
            */
            if (file_exists($fullpath)){
                if ($i == 0) {
                    $media['main_image'] = str_replace("/var/www/shared/media","",$fullpath);
                } else {
                    if (!isset($media['media_gallery'])) {
                        $media['media_gallery'] = [];
                    }
                    $media['media_gallery'][] = str_replace("/var/www/shared/media","",$fullpath);
                }

                $i++;
            }
        }
        return $media;
    }
}
