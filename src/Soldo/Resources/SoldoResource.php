<?php

namespace Soldo\Resources;

use Soldo\SoldoUtilities;

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
     * @var array
     */
    protected $whiteListed = [];

    /**
     * @var string
     */
    protected $basePath;


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
    public function fill(array $data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
        return $this;
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

    /**
     * TODO: overwrite this in Company, removing the id
     *
     * @return string
     */
    public function getRemotePath()
    {
        return $this->basePath . '/' . $this->id;
    }

    /**
     * @param array $data
     * @return array
     */
    public function filterWhiteList($data)
    {
        return array_intersect_key($data, array_flip($this->whiteListed));
    }


}
