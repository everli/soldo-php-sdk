<?php

namespace Soldo\Resources;

use Respect\Validation\Validator;
use Soldo\Exceptions\SoldoInvalidRelationshipException;
use Soldo\Exceptions\SoldoCastException;

/**
 * Class SoldoResource
 * @package Soldo\Resources
 */
abstract class SoldoResource
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
     * An array containing a map of the resource relationships
     * The key represents also the path of the child resource(s)
     *
     * @var array
     */
    protected $relationships = [];

    /**
     * An array containing the list of attributes that need to be casted
     * into a SoldoResource or one of its child class (e.g. a Wallet)
     *
     * @var array
     */
    protected $cast = [];

    /**
     * @var string
     */
    protected $basePath;

    /**
     * SoldoResource constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->fill($data);
    }

    /**
     * Validate that className is a valid class and that it is of type SoldoResource
     *
     * @param $className
     * @param $attributeName
     * @param $data
     * @throws SoldoCastException
     * @return boolean
     */
    private function validateResource($className, $attributeName, $data)
    {
        if (class_exists($className) === false) {
            throw new SoldoCastException(
                'Could not cast ' . $attributeName . '. '
                . $className . ' doesn\'t exist'
            );
        }

        // create a dummy object and check if it is a SoldoResource child
        $dummy = new $className();
        if (is_a($dummy, '\Soldo\Resources\SoldoResource') === false) {
            throw new SoldoCastException(
                'Could not cast ' . $attributeName . '. '
                . $className . ' is not a SoldoResource child'
            );
        }

        if (is_array($data) === false || empty($data)) {
            throw new SoldoCastException(
                'Could not cast ' . $attributeName . '. '
                . '$data is not a valid data set'
            );
        }

        return true;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function fill(array $data)
    {
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->cast)) {
                $class = $this->cast[$key];
                $this->validateResource($class, $key, $value);
                $this->{$key} = new $class($value);
                continue;
            }

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
        $attributes = [];
        foreach ($this->_attributes as $key => $value) {
            /** @var SoldoResource $value */
            if (array_key_exists($key, $this->cast)) {
                $attributes[$key] = $value->toArray();
                continue;
            }

            $attributes[$key] = $value;
        }

        return $attributes;
    }

    /**
     * @return string
     */
    public function getRemotePath()
    {
        if ($this->id === null) {
            throw new \BadMethodCallException(
                'Cannot retrieve remote path for ' . static::class . '.'
                . ' "id" attribute is not defined.'
            );
        }

        if ($this->basePath === null) {
            throw new \BadMethodCallException(
                'Cannot retrieve remote path for ' . static::class . '.'
                . ' "basePath" attribute is not defined.'
            );
        }

        if(preg_match('/^\/[\S]+$/', $this->basePath) === 0) {
            throw new \BadMethodCallException(
                'Cannot retrieve remote path for ' . static::class . '.'
                . ' "basePath" seems to be not a valid path.'
            );
        }

        return $this->basePath . '/' . urlencode($this->id);
    }

    /**
     * Get an array of child resource.
     *
     * @param $relationshipName
     * @param $data
     * @return array
     */
    public function buildRelationship($relationshipName, $data)
    {
        $this->validateRelationship($relationshipName);
        $this->validateRelationshipRawData($relationshipName, $data);

        $className = $this->relationships[$relationshipName];
        $relationship = [];
        foreach ($data[$relationshipName] as $r) {
            $relationship[] = new $className($r);
        }

        return $relationship;
    }

    /**
     * @param string $relationshipName
     * @param array $data
     * @throws SoldoInvalidRelationshipException
     * @return boolean
     */
    private function validateRelationshipRawData($relationshipName, $data)
    {
        $validator = Validator::key($relationshipName, Validator::arrayType()->notEmpty());
        if ($validator->validate($data) === false) {
            throw new SoldoInvalidRelationshipException(
                'Could not build ' . $relationshipName . ' relationship '
                . 'with the array provided'
            );
        }

        foreach ($data[$relationshipName] as $singleRelationship) {
            if (is_array($singleRelationship) === false) {
                throw new SoldoInvalidRelationshipException(
                    'Could not build ' . $relationshipName . ' relationship '
                    . 'with the array provided'
                );
            }
        }

        return true;
    }

    /**
     * @param $relationshipName
     * @return string
     */
    public function getRelationshipRemotePath($relationshipName)
    {
        $this->validateRelationship($relationshipName);

        return $this->getRemotePath() . '/' . $relationshipName;
    }

    /**
     * @param array $data
     * @return array
     */
    public function filterWhiteList($data)
    {
        return array_intersect_key($data, array_flip($this->whiteListed));
    }

    /**
     * Validate a relationship: the $this->relationship must contain a $relationshipName key
     * and the value of the key must be a valid resource name
     *
     * @param $relationshipName
     * @return boolean
     */
    private function validateRelationship($relationshipName)
    {
        if (!array_key_exists($relationshipName, $this->relationships)) {
            throw new \InvalidArgumentException(
                'There is no relationship mapped with "'
                . $relationshipName . '" name'
            );
        }

        $className = $this->relationships[$relationshipName];
        if (class_exists($className) === false) {
            throw new \InvalidArgumentException(
                'Invalid resource class name '
                . $className . ' doesn\'t exist'
            );
        }

        return true;
    }

    /**
     * @param array $cast
     */
    protected function setCast($cast)
    {
        $this->cast = $cast;
    }

    /**
     * @param $whiteListed
     */
    protected function setWhitelisted($whiteListed)
    {
        $this->whiteListed = $whiteListed;
    }

    /**
     * @param $relationships
     */
    protected function setRelationships($relationships)
    {
        $this->relationships = $relationships;
    }

    /**
     * @param $basePath
     */
    protected function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }
}
