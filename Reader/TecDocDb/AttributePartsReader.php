<?php

namespace OpsWay\TecDocMigration\Reader\TecDocDb;

class AttributePartsReader extends TecdocDbAbstract
{
    const SQL_STMT =<<<'SQL'
SELECT 'text' as `type`, CONCAT('CRI_',C.CRI_ID) as `code`, DT.TEX_TEXT as `name`, 'technical' as `group`
FROM `CRITERIA` as C
LEFT JOIN DESIGNATIONS as D ON C.CRI_DES_ID = D.DES_ID AND D.DES_LNG_ID = 4
LEFT JOIN DES_TEXTS DT ON DT.TEX_ID = D.DES_TEX_ID
SQL;

    protected $processed = false;
    protected $attributes = [];
    public static $attributesStatic
        = [
            ['type'                   => 'simpleselect',
             'code'                   => 'fitting_place',
             'name'            => 'Fitting place',
             'group'                  => 'grouping',
            ],
            ['type'                   => 'simpleselect',
             'code'                   => 'parts_group',
             'name'            => 'Group',
             'group'                  => 'grouping',
            ],
            ['type'                   => 'simpleselect',
             'code'                   => 'supplier',
             'name'            => 'Supplier',
             'group'                  => 'manufacturing',
            ],
            ['type'                   => 'text',
             'code'                   => 'ART_ARTICLE_NR',
             'name'            => 'Search Article Number',
             'group'                  => 'tecdoc_info',
            ],
            ['type'                   => 'text',
             'code'                   => 'EAN',
             'name'            => 'EAN',
             'group'                  => 'tecdoc_info',
            ],
            ['type'                   => 'textarea',
             'code'                   => 'replaced_articles',
             'name'            => 'Replacement Article Number',
             'group'                  => 'tecdoc_info',
            ],
            ['type'                   => 'boolean',
             'code'                   => 'ART_PACK_SELFSERVICE',
             'name'            => 'For independent use',
             'group'                  => 'tecdoc_info',
            ],
            ['type'                   => 'boolean',
             'code'                   => 'ART_MATERIAL_MARK',
             'name'            => 'Requires mandatory designations',
             'group'                  => 'tecdoc_info',
            ],
            ['type'                   => 'boolean',
             'code'                   => 'ART_ACCESSORY',
             'name'            => 'Accessory',
             'group'                  => 'tecdoc_info',
            ],
            ['type'                   => 'boolean',
             'code'                   => 'ART_REPLACEMENT',
             'name'            => 'Replacement part',
             'group'                  => 'tecdoc_info',
            ],
            ['type'                   => 'number',
             'code'                   => 'td_id',
             'name'            => 'TecDoc ID',
             'group'                  => 'tecdoc_info',
            ],
            ['type'                   => 'simpleselect',
             'code'                   => 'td_status',
             'name'            => 'TecDoc Status',
             'group'                  => 'tecdoc_info',
            ],
        ];

    public function read()
    {
        if (!$this->isReadyFetch($this->stmt)){
            $this->stmt = $this->prepareQuery(self::SQL_STMT);
            $this->attributes = array_merge(self::$attributesStatic, $this->stmt->fetchAll());
            $this->processed = true;
        }
        return array_shift($this->attributes);
    }
}
