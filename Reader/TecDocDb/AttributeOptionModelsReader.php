<?php

namespace OpsWay\TecDocMigration\Reader\TecDocDb;

class AttributeOptionModelsReader extends TecdocDbAbstract
{
    protected $options = [
        ['attribute' => 'MODELTYPE', 'code' => 'car', 'value' => 'Cars'],
        ['attribute' => 'MODELTYPE', 'code' => 'truck', 'value' => 'Trucks'],
        ['attribute' => 'MODELTYPE', 'code' => 'bus', 'value' => 'Buses'],
        ['attribute' => 'MODELTYPE', 'code' => 'bike', 'value' => 'Bikes'],
    ];

    public function read()
    {
        return array_shift($this->options);
    }

    protected function normalizeCode($label)
    {
        return strtolower(preg_replace('/_+/','_',str_replace([' ','/','(',')','-',',','.'],'_',$label)));
    }
}
