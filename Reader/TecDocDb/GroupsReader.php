<?php

namespace OpsWay\TecDocMigration\Reader\TecDocDb;

class GroupsReader extends TecdocDbAbstract
{
    public static $staticAttributes =  [
        ['code'=> 'other', 'name' => 'Other', 'sort' => 100],
        ['code'=> 'main', 'name' => 'Main', 'sort' => 1],
        ['code'=> 'tecdoc_info', 'name' => 'TecDoc Info', 'sort' => 2],
        ['code'=> 'manufacturing', 'name' => 'Manufacturing', 'sort' => 3],
        ['code'=> 'grouping', 'name' => 'Grouping', 'sort' => 5],
        ['code'=> 'technical', 'name' => 'Technical', 'sort' => 6],
        ['code'=> 'cars_information', 'name' => 'Car Information', 'sort' => 7],
        ['code'=> 'media', 'name' => 'Media', 'sort' => 4],
    ];

    protected $attributes = null;

    public function read()
    {
        if (is_null($this->attributes)) {
            $this->attributes = self::$staticAttributes;
        }
        return array_shift($this->attributes);
    }
}
