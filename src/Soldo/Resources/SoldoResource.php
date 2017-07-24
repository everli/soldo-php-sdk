<?php

namespace Soldo\Resources;

/**
 * Class SoldoResource
 * @package Soldo\Resources
 */
class SoldoResource
{

    /**
     * @var array
     */
    protected $_attributes = [];


    /**
     * SoldoResource constructor.
     */
    public function __construct($data = [])
    {
        $this->fill($data);
    }

    /**
     * @param array $data
     */
    private function fill(array $data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->_attributes[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_attributes)) {
            return $this->_attributes[$name];
        }
        return null;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->_attributes;
    }

}
