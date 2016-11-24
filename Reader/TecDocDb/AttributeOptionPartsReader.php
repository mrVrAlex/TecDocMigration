<?php

namespace OpsWay\TecDocMigration\Reader\TecDocDb;

class AttributeOptionPartsReader extends TecdocDbAbstract
{
    const SQL_STMT =<<<'SQL'
SELECT 'parts_group' as `attribute`, CONCAT('td_gr_',GA.GA_NR) as code, DT.TEX_TEXT as `value` FROM `GENERIC_ARTICLES` as GA
LEFT JOIN DESIGNATIONS as D ON GA.GA_DES_ID = D.DES_ID AND D.DES_LNG_ID = 4
LEFT JOIN DES_TEXTS DT ON DT.TEX_ID = D.DES_TEX_ID
SQL;
    const SQL_STMT2 =<<<'SQL'
SELECT 'fitting_place' as `attribute`, CONCAT('td_ft_',GA.GA_NR) as code, DT2.TEX_TEXT as `value` FROM `GENERIC_ARTICLES` as GA
LEFT JOIN DESIGNATIONS as D2 ON GA.GA_DES_ID_ASSEMBLY = D2.DES_ID AND D2.DES_LNG_ID = 4
LEFT JOIN DES_TEXTS DT2 ON DT2.TEX_ID = D2.DES_TEX_ID
SQL;
    const SQL_STMT3 =<<<'SQL'
SELECT 'supplier' as `attribute`, CONCAT('td_sup_', `SUP_ID`) as `code`, `SUP_BRAND` as `value` FROM `SUPPLIERS`
SQL;

    protected $tec_doc_statuses = [
        ['attribute' => 'td_status', 'value' => 'Article to be discontinued'],
        ['attribute' => 'td_status', 'value' => 'in Preparation'],
        ['attribute' => 'td_status', 'value' => 'No longer supplied by manufacturer'],
        ['attribute' => 'td_status', 'value' => 'Normal'],
        ['attribute' => 'td_status', 'value' => 'Not Available'],
        ['attribute' => 'td_status', 'value' => 'Not supplied individually'],
        ['attribute' => 'td_status', 'value' => 'On Demand'],
        ['attribute' => 'td_status', 'value' => 'Only supplied in parts list'],
        ['attribute' => 'td_status', 'value' => 'Pseudo Article'],
        ['attribute' => 'td_status', 'value' => 'To be discontinued article in preparation'],
    ];
    static public $attributes = [
        self::SQL_STMT,
        self::SQL_STMT2,
        self::SQL_STMT3,
    ];

    protected $processed = false;
    protected $options = [];

    public function read()
    {
        if (!$this->processed) {
            foreach (self::$attributes as $attrSql) {
                $stmt = $this->connection->query($attrSql);
                $this->options = array_merge($this->options, $stmt->fetchAll());
            }
            foreach ($this->tec_doc_statuses as $row){
                $row['code'] = strtolower(str_replace(' ','_',$row['value']));
                $this->options[] = $row;
            }
            $this->processed = true;
        }
        return array_shift($this->options);
    }

    protected function normalizeCode($label)
    {
        return strtolower(preg_replace('/_+/','_',str_replace([' ','/','(',')','-',',','.'],'_',$label)));
    }
}
