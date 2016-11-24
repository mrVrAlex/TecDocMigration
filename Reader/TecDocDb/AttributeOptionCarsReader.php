<?php

namespace OpsWay\TecDocMigration\Reader\TecDocDb;

class AttributeOptionCarsReader extends TecdocDbAbstract
{
    const SQL_STMT =<<<'SQL'
SELECT
(SELECT DES_TEXTS.TEX_TEXT
			FROM DES_TEXTS AS DES_TEXTS
			JOIN DESIGNATIONS AS DESIGNATIONS ON (DES_TEXTS.TEX_ID = DESIGNATIONS.DES_TEX_ID)
			WHERE (DESIGNATIONS.DES_ID = TYPES.%REAL_NAME%) AND (DESIGNATIONS.DES_LNG_ID=4) LIMIT 1
			) AS code
	FROM TYPES
WHERE (SUBSTRING(TYPES.TYP_CTM, 250+2, 1) = '1')
GROUP BY code HAVING code IS NOT NULL
SQL;

    static public $attributes = [
        ['attribute' => 'BODYTYPE', '%REAL_NAME%' => 'TYP_KV_MODEL_DES_ID'],
        ['attribute' => 'BODYTYPE', '%REAL_NAME%' => 'TYP_KV_BODY_DES_ID'],
        ['attribute' => 'TYPEABS', '%REAL_NAME%' => 'TYP_KV_ABS_DES_ID'],
        ['attribute' => 'TYPEASR', '%REAL_NAME%' => 'TYP_KV_ASR_DES_ID'],
        ['attribute' => 'TYPEENGINE', '%REAL_NAME%' => 'TYP_KV_ENGINE_DES_ID'],
        ['attribute' => 'BRAKETYPE', '%REAL_NAME%' => 'TYP_KV_BRAKE_TYPE_DES_ID'],
        ['attribute' => 'BRAKESYSTEM', '%REAL_NAME%' => 'TYP_KV_BRAKE_SYST_DES_ID'],
        ['attribute' => 'TYPEFUEL', '%REAL_NAME%' => 'TYP_KV_FUEL_DES_ID'],
        ['attribute' => 'TYPECATALYST', '%REAL_NAME%' => 'TYP_KV_CATALYST_DES_ID'],
        ['attribute' => 'TYPEAXLE', '%REAL_NAME%' => 'TYP_KV_AXLE_DES_ID'],
        ['attribute' => 'TYPETRANS', '%REAL_NAME%' => 'TYP_KV_TRANS_DES_ID'],
    ];

    protected $processed = false;
    protected $options = [];

    public function read()
    {
        if (!$this->processed) {
            foreach (self::$attributes as $attr) {
                $stmt = $this->connection->query(str_replace('%REAL_NAME%', $attr['%REAL_NAME%'], self::SQL_STMT));
                foreach ($stmt->fetchAll() as $row) {
                    $this->options[] = ['attribute'   => $attr['attribute'],
                               'code'        => $this->normalizeCode($row['code']),
                               'value' => $row['code'],
                    ];
                }
                $this->processed = true;
            }
        }
        return array_shift($this->options);
    }

    protected function normalizeCode($label)
    {
        return strtolower(preg_replace('/_+/','_',str_replace([' ','/','(',')','-',',','.'],'_',$label)));
    }
}
