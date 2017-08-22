<?php

namespace Soldo\Resources;

use Soldo\Exceptions\SoldoInvalidEvent;
use Soldo\Exceptions\SoldoInvalidFingerprintException;
use Soldo\Exceptions\SoldoInvalidPathException;
use Soldo\Exceptions\SoldoInvalidRelationshipException;
use Soldo\Exceptions\SoldoInvalidResourceException;
use Soldo\Validators\ResourceValidatorTrait;

/**
 * Class Resource
 * @package Soldo\Resources
 */
abstract class Resource
{
    use ResourceValidatorTrait;

    /**
     * Remote path of resource list
     *
     * @var string
     */
    protected static $basePath;

    /**
     * Remote path of the single resources
     *
     * @var string
     */
    protected $path;

    /**
     * Event identifier based on the resource status
     *
     * @var string
     */
    protected $eventType;

    /**
     * List of resource attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * List of attributes that can be updated
     *
     * @var array
     */
    protected $whiteListed = [];

    /**
     * An array containing a map of the resource relationships
     *
     * @var array
     */
    protected $relationships = [];

    /**
     * An array containing the list of attributes that need to be casted into a Resource
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
     * Populate resource attribute with the array provided
     *
     * @param array $data
     * @return $this
     */
    public function fill($data)
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException(
                'Trying to fill resource with malformed data'
            );
        }

        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * Return true if the attribute need to be casted
     *
     * @param string $attributeName
     * @return bool
     */
    private function shouldCast($attributeName)
    {
        return array_key_exists($attributeName, $this->cast);
    }

    /**
     * Set attribute with the name and value provided
     * Attributes cast happens here
     *
     * @param string $name
     * @param mixed $value
     * @throws SoldoInvalidResourceException
     */
    public function __set($name, $value)
    {
        if ($this->shouldCast($name)) {
            $className = $this->cast[$name];
            $this->validateClassName($className);

            $this->attributes[$name] = new $className($value);

            return;
        }

        $this->attributes[$name] = $value;
    }

    /**
     * Return a given attribute
     *
     * @param string $name
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
     * Build an array representation of the resource
     * Also call toArray on casted attributes
     *
     * @return array
     */
    public function toArray()
    {
        $attributes = [];
        foreach ($this->attributes as $key => $value) {
            if (array_key_exists($key, $this->cast)) {
                /** @var \Soldo\Resources\Resource $value */
                $attributes[$key] = $value->toArray();
                continue;
            }
            $attributes[$key] = $value;
        }

        return $attributes;
    }

    /**
     * Build a fingerprint concatenating the resource properties listed in $fingerprintOrder
     * plus the $internalToken (in the order indicated by the 'token' item in the fingerprintOrder array)
     *
     * @param array $fingerprintOrder
     * @param string $internalToken
     *
     * @return string
     * @throws SoldoInvalidFingerprintException
     */
    public function buildFingerprint($fingerprintOrder, $internalToken)
    {
        // $fingerprintOrder must contain at least one resource property name and the a 'token' item
        if (count($fingerprintOrder) < 2 || !in_array('token', $fingerprintOrder)) {
            throw new SoldoInvalidFingerprintException(
                'Invalid fingerprint order arrays'
            );
        }

        $data = '';
        foreach ($fingerprintOrder as $attributeName) {
            if ($attributeName === 'token') {
                $data .= $internalToken;
                continue;
            }

            if ($this->{$attributeName} === null) {
                throw new SoldoInvalidFingerprintException(
                    static::class . ' ' . $attributeName . ' is not defined'
                );
            }

            $data .= $this->{$attributeName};
        }

        return hash('sha512', $data);
    }


    /**
     * Get full remote path of the single resource
     *
     * @throws SoldoInvalidPathException
     * @return string
     */
    final public function getRemotePath()
    {
        $basePath = self::getBasePath();

        if ($this->path === null) {
            return $basePath;
        }

        $resourcePath = $this->getResourcePath();

        return $basePath . $resourcePath;
    }

    /**
     * Extract an array of resources data from the rawData returned by the API
     *
     * @param array $rawData
     * @param string $relationshipName
     * @throws SoldoInvalidRelationshipException
     * @return mixed
     */
    private function getRelationshipData($rawData, $relationshipName)
    {
        if (!is_array($rawData) || !array_key_exists($relationshipName, $rawData)) {
            throw new SoldoInvalidRelationshipException(
                'Trying to build a relationship with invalid data'
            );
        }

        return $rawData[$relationshipName];
    }

    /**
     * Build and return an array of Resource
     *
     * @param string $relationshipName
     * @param array $rawData
     * @return array
     */
    public function buildRelationship($relationshipName, $rawData)
    {
        $className = $this->getRelationshipClass($relationshipName);
        $this->validateClassName($className);

        $data = $this->getRelationshipData($rawData, $relationshipName);

        $relationship = [];
        foreach ($data as $relationshipData) {
            $relationship[] = new $className($relationshipData);
        }

        return $relationship;
    }

    /**
     * Get relationship class given the relationship name
     *
     * @param string $relationshipName
     * @throws SoldoInvalidRelationshipException
     * @return mixed
     */
    private function getRelationshipClass($relationshipName)
    {
        if (!array_key_exists($relationshipName, $this->relationships)) {
            throw new SoldoInvalidRelationshipException(
                'Relationship ' . $relationshipName . ' is not defined'
            );
        }

        $class = $this->relationships[$relationshipName];

        return $class;
    }

    /**
     * Get relationship remote path
     *
     * @param string $relationshipName
     * @throws SoldoInvalidRelationshipException
     * @return string
     */
    public function getRelationshipRemotePath($relationshipName)
    {
        $className = $this->getRelationshipClass($relationshipName);
        $this->validateClassName($className);

        $relationshipPath = call_user_func([$className, 'getBasePath']);

        return $this->getRemotePath() . $relationshipPath;
    }

    /**
     * Remove all not whitelisted key from the array
     *
     * @param array $data
     * @return array
     */
    public function filterWhiteList($data)
    {
        return array_intersect_key($data, array_flip($this->whiteListed));
    }

    /**
     * Return a string representing the event type based on the resource status
     * E.g. for a Payment Refused Transactions it will return transaction.payment_refused
     *
     * @return null|string
     * @throws SoldoInvalidEvent
     */
    public function getEventType()
    {
        if ($this->eventType === null) {
            return null;
        }

        $eventParts = [];
        preg_match_all('/\{(\S+?)\}/', $this->eventType, $parts);

        if (empty($parts[0])) {
            return null;
        }

        foreach ($parts[1] as $key => $attributeName) {
            if ($this->{$attributeName} === null) {
                throw new SoldoInvalidEvent(
                    static::class . ' ' . $attributeName . ' is not defined'
                );
            }

            $eventParts[] = trim(strtolower($this->{$attributeName}));
        }

        $classShortName = strtolower((new \ReflectionClass($this))->getShortName());
        return $classShortName . '.' . implode('_', $eventParts);
    }

    /**
     * Build a full qualified path replacing {string} occurrence
     * with $this->{string} attribute
     *
     * @throws SoldoInvalidPathException
     * @return mixed
     */
    private function getResourcePath()
    {
        if (@preg_match('/^\/[\S]+$/', $this->path) !== 1) {
            throw new SoldoInvalidPathException(
                static::class . ' basePath seems to be invalid'
            );
        }

        $remotePath = $this->path;
        preg_match_all('/\{(\S+?)\}/', $this->path, $parts);
        foreach ($parts[1] as $key => $attributeName) {
            if ($this->{$attributeName} === null) {
                throw new SoldoInvalidPathException(
                    static::class . ' ' . $attributeName . ' is not defined'
                );
            }

            $remotePath = str_replace(
                $parts[0][$key],
                urlencode($this->{$attributeName}),
                $remotePath
            );
        }

        return $remotePath;
    }

    /**
     * Get base path
     *
     * @throws SoldoInvalidPathException
     * @return string
     */
    final public static function getBasePath()
    {
        if (static::$basePath === null ||
            @preg_match('/^\/[\S]+$/', static::$basePath) !== 1) {
            throw new SoldoInvalidPathException(
                static::class . ' basePath seems to be invalid'
            );
        }

        return static::$basePath;
    }
}
