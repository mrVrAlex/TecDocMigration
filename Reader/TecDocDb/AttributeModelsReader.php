<?php

namespace OpsWay\TecDocMigration\Reader\TecDocDb;

class AttributeModelsReader extends TecdocDbAbstract
{
    public static $staticAttributes =  [
        ['type' => 'simpleselect', 'code' => 'MODELTYPE', 'name' => 'Model Type', 'group' => 'cars_information'],
        ['type' => 'textarea', 'code' => 'description', 'name' => 'Description', 'group' => 'main'],
        ['type' => 'image', 'code' => 'main_image', 'name' => 'Main Picture', 'group' => 'media'],
        ['type' => 'gallery', 'code' => 'media_gallery', 'name' => 'Media Gallery', 'group' => 'media'],
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
