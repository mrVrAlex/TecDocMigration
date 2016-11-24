<?php

namespace OpsWay\TecDocMigration\Reader\TecDocDb;

class AttributeCarsReader extends TecdocDbAbstract
{
    public static $staticAttributes =  [
        ['type' => 'text', 'code' => 'TYP_PCON_START', 'name' => 'Construction Start', 'group' => 'cars_information'],
        ['type' => 'text', 'code' => 'TYP_PCON_END', 'name' => 'Construction End', 'group' => 'cars_information'],
        ['type' => 'simpleselect', 'code' => 'BODYTYPE', 'name' => 'Body Type', 'group' => 'cars_information'],
        ['type' => 'text', 'code' => 'TYP_KW_FROM', 'name' => 'Motor Power (kW) (from)', 'group' => 'cars_information'],
        ['type' => 'text', 'code' => 'TYP_KW_UPTO', 'name' => 'Motor Power (kW) (to)', 'group' => 'cars_information'],
        ['type' => 'text', 'code' => 'TYP_HP_FROM', 'name' => 'Engine power (hp) (from)', 'group' => 'cars_information'],
        ['type' => 'text', 'code' => 'TYP_HP_UPTO', 'name' => 'Engine power (hp) (to)', 'group' => 'cars_information'],
        ['type' => 'text', 'code' => 'TYP_CCM', 'name' => 'The volume of CCM', 'group' => 'cars_information'],
        ['type' => 'text', 'code' => 'TYP_CYLINDERS', 'name' => 'Number cylinders', 'group' => 'cars_information'],
        ['type' => 'text', 'code' => 'TYP_DOORS', 'name' => 'Number doors', 'group' => 'cars_information'],
        ['type' => 'text', 'code' => 'TYP_TANK', 'name' => 'Tank', 'group' => 'cars_information'],
        ['type' => 'text', 'code' => 'TYP_KV_VOLTAGE', 'name' => 'Voltage', 'group' => 'cars_information'],
        ['type' => 'simpleselect', 'code' => 'TYPEABS', 'name' => 'ABS', 'group' => 'cars_information'],
        ['type' => 'simpleselect', 'code' => 'TYPEASR', 'name' => 'ASR', 'group' => 'cars_information'],
        ['type' => 'simpleselect', 'code' => 'TYPEENGINE', 'name' => 'Engine type', 'group' => 'cars_information'],
        ['type' => 'text', 'code' => 'LISTCODEENGINE', 'name' => 'Engine codes', 'group' => 'cars_information'],
        ['type' => 'simpleselect', 'code' => 'BRAKETYPE', 'name' => 'Brake type', 'group' => 'cars_information'],
        ['type' => 'simpleselect', 'code' => 'BRAKESYSTEM', 'name' => 'Brake system', 'group' => 'cars_information'],
        ['type' => 'simpleselect', 'code' => 'TYPEFUEL', 'name' => 'Fuel type', 'group' => 'cars_information'],
        ['type' => 'simpleselect', 'code' => 'TYPECATALYST', 'name' => 'Catalyst type', 'group' => 'cars_information'],
        ['type' => 'text', 'code' => 'TYP_MAX_WEIGHT', 'name' => 'Tonnage (Max weight)', 'group' => 'cars_information'],
        ['type' => 'simpleselect', 'code' => 'TYPEAXLE', 'name' => 'Configuration axis', 'group' => 'cars_information'],
        ['type' => 'text', 'code' => 'TYP_CCM_TAX', 'name' => 'Technical CCM', 'group' => 'cars_information'],
        ['type' => 'text', 'code' => 'TYP_LITRES', 'name' => 'Engine liters', 'group' => 'cars_information'],
        ['type' => 'text', 'code' => 'TYP_VALVES', 'name' => 'Valves per combustion chamber', 'group' => 'cars_information'],
        ['type' => 'simpleselect', 'code' => 'TYPETRANS', 'name' => 'Transmission type', 'group' => 'cars_information'],
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
