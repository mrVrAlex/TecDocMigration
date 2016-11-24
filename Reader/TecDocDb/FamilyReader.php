<?php

namespace OpsWay\TecDocMigration\Reader\TecDocDb;

class FamilyReader extends TecdocDbAbstract
{
    const SQL_STMT_FAMILY_LIST =<<<'SQL'
SELECT
CONCAT('ga_',GA.GA_ID) as `code`,
GROUP_CONCAT(DISTINCT CONCAT('CRI_',ACR_CRI_ID)) as `attributes`,
CONCAT( DT.TEX_TEXT,
IFNULL(
    CONCAT(' (',  DT1.TEX_TEXT, ')'),
    ''
    )
) as `name`,
'PART' as `type`
FROM `GENERIC_ARTICLES` as GA
LEFT JOIN `ARTICLE_CRITERIA` ON GA.GA_ID = ACR_GA_ID
LEFT JOIN DESIGNATIONS as D ON GA.GA_DES_ID = D.DES_ID AND D.DES_LNG_ID = 4
LEFT JOIN DESIGNATIONS as D1 ON GA.GA_DES_ID_ASSEMBLY = D1.DES_ID AND D1.DES_LNG_ID = 4
LEFT JOIN DES_TEXTS DT ON DT.TEX_ID = D.DES_TEX_ID
LEFT JOIN DES_TEXTS DT1 ON DT1.TEX_ID = D1.DES_TEX_ID
GROUP BY GA.GA_ID
ORDER BY null
SQL;

    protected $familyStatic
        = [
            [
            'code'             => 'IS_CAR',
            'name'             => 'Vehicle Modification Family',
            'type'              => 'VEHICLE',
            'attributes'       => 'main_image',
            ],
           [
               'code'             => 'IS_MODEL',
               'name'             => 'Vehicle Models Sets',
               'type'             => 'CATALOG_VEHICLE',
               'attributes'       => 'main_image',
           ],
            [
                'code'             => 'IS_BRAND',
                'name'             => 'Vehicle Brand Sets',
                'type'              => 'CATALOG_VEHICLE',
                'attributes'       => 'main_image',
            ],
            [
                'code'             => 'IS_CATEGORY',
                'name'             => 'Category Part Sets',
                'type'              => 'CATALOG_PART',
                'attributes'       => 'main_image',
            ]
        ];

    protected $attributes = ['description','media_gallery'];

    public function read()
    {
        if (!$this->isReadyFetch($this->stmt)) {
            $this->stmt = $this->prepareQuery(self::SQL_STMT_FAMILY_LIST);
            $data = array_shift($this->familyStatic);
            if ($data['code'] == 'IS_CAR') {
                $data['attributes'] = implode(",",
                    array_merge(
                        explode(",", $data['attributes']),
                        array_column(AttributeCarsReader::$staticAttributes, 'code')
                    )
                );
            }
            $this->attributes = array_merge($this->attributes,
                array_column(AttributePartsReader::$attributesStatic, 'code'));
        } else {
            if (count($this->familyStatic) > 0) {
                $data = array_shift($this->familyStatic);
                if ($data['code'] == 'IS_MODEL') {
                    $data['attributes'] = implode(",",
                        array_merge(
                            explode(",", $data['attributes']),
                            array_column(AttributeModelsReader::$staticAttributes, 'code')
                        )
                    );
                }
            } else {
                $data = $this->stmt->fetch();
                if (!$data) {
                    return null;
                }
                $attr = [];
                if (!isset($data['attributes']) || $data['attributes'] != ''
                    || strtolower($data['attributes']) != 'null'
                ) {
                    $attr = explode(",", $data['attributes']);
                }
                $data['attributes'] = rtrim(implode(",", array_merge($this->attributes, $attr)), ',');
            }
        }
        if ($this->stepExecution) {
            $this->stepExecution->incrementSummaryInfo('read');
        }
        return $data;
    }
}
