<?php

namespace Soldo\Resources;

use Respect\Validation\Validator;
use Soldo\Exceptions\SoldoInvalidPathException;
use Soldo\Exceptions\SoldoInvalidRelationshipException;
use Soldo\Exceptions\SoldoCastException;

/**
 * Class Resource
 * @package Soldo\Resources
 */
abstract class Resource
{
    /**
     * @var string
     */
    protected static $basePath;

    /**
     * @var array
     */
    protected $attributes = [];

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
     * into a Resource or one of its child class (e.g. a Wallet)
     *
     * @var array
     */
    protected $cast = [];

    /**
     * Resource constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->fill($data);
    }

    /**
     * Validate that className is a valid class and that it is of type Resource
     *
     * @param $className
     * @param $attributeName
     * @param $data
     * @throws SoldoCastException
     * @return boolean
     */
    private function validateAttributeCast($className, $attributeName, $data)
    {
        if (class_exists($className) === false) {
            throw new SoldoCastException(
                'Could not cast ' . $attributeName . '. '
                . $className . ' doesn\'t exist'
            );
        }

        // create a dummy object and check if it is a Resource child
        $dummy = new $className();
        if (is_a($dummy, '\Soldo\Resources\Resource') === false) {
            throw new SoldoCastException(
                'Could not cast ' . $attributeName . '. '
                . $className . ' is not a Resource child'
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
        if (array_key_exists($name, $this->cast)) {
            $class = $this->cast[$name];

            if ($value instanceof $class === false) {
                $this->validateAttributeCast($class, $name, $value);
                $this->attributes[$name] = new $class($value);
                return;
            }
        }

        $this->attributes[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        return null;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $attributes = [];
        foreach ($this->attributes as $key => $value) {
            /** @var Resource $value */
            if (array_key_exists($key, $this->cast)) {
                $attributes[$key] = $value->toArray();
                continue;
            }
            $attributes[$key] = $value;
        }

        return $attributes;
    }

    /**
     * @param $basePath
     * @throws SoldoInvalidPathException
     */
    private function validateBasePath($basePath)
    {
        if ($basePath === null) {
            throw new SoldoInvalidPathException(
                'Static property ' . static::class . '::$basePath'
                . ' cannot be null'
            );
        }

        if (preg_match('/^\/[\S]+$/', $basePath) === 0) {
            throw new SoldoInvalidPathException(
                'Static property ' . static::class . '::$basePath'
                . ' seems to be not a valid path'
            );
        }
    }


    /**
     * @param $path
     * @throws SoldoInvalidPathException
     */
    protected function validatePath($path)
    {
        if (preg_match('/^\/[\S]+$/', $path) === 0) {
            throw new SoldoInvalidPathException(
                'The attribute $path of ' . static::class . '.'
                . ' seems to be not a valid path.'
            );
        }
    }


    /**
     * @param $attribute
     *
     * @throws SoldoInvalidPathException
     */
    protected function validateAttribute($attribute)
    {
        if ($this->{$attribute} === null) {
            throw new SoldoInvalidPathException(
                'The attribute "' . $attribute . '" of ' . static::class
                . ' is not defined'
            );
        }
    }

    /**
     * @return string
     */
    public final function getRemotePath()
    {
        $basePath = self::getBasePath();
        $this->validateBasePath($basePath);

        // immediately return base path if path is not defined
        if($this->path === null) {
            return static::$basePath;
        }

        $this->validatePath($this->path);
        $remote_path = $this->path;
        preg_match_all('/\{(\S+?)\}/', $this->path, $variables);
        foreach ($variables[1] as $key => $attribute) {
            $this->validateAttribute($attribute);
            $remote_path = str_replace($variables[0][$key], urlencode($this->{$attribute}), $remote_path);
        }
        return $basePath . $remote_path;
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
     * @return string
     */
    final static public function getBasePath()
    {
        return static::$basePath;
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
}
