<?php

namespace Soldo\Resources;
use Respect\Validation\Validator;
use Soldo\Exceptions\SoldoInvalidRelationshipException;

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
     * An array containing a map of the resource relationships
     * The key represents also the path of the child resource(s)
     *
     * @var array
     */
    protected $relationships = [];

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
     * @return string
     */
    public function getRemotePath()
    {
        return $this->basePath . '/' . $this->id;
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
     */
    private function validateRelationshipRawData($relationshipName, $data)
    {
        $validator = Validator::key($relationshipName, Validator::arrayType()->notEmpty());
        if($validator->validate($data) === false) {
            throw new SoldoInvalidRelationshipException(
                'Could not build ' . $relationshipName . ' relationship '
                .'with the array provided'
            );
        }
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
     */
    private function validateRelationship($relationshipName)
    {
        if(!array_key_exists($relationshipName, $this->relationships)) {
            throw new \InvalidArgumentException(
                'There is no relationship mapped with '
                . $relationshipName . ' name'
            );
        }

        $className = $this->relationships[$relationshipName];
        if(class_exists($className) === false) {
            throw new \InvalidArgumentException(
                'Invalid resource class name '
                . $className . ' doesn\'t exist'
            );
        }
    }


}
