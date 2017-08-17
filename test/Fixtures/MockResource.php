<?php

namespace Soldo\Tests\Fixtures;

use Soldo\Resources\Resource;

/**
 * Class MockResource
 */
class MockResource extends Resource
{
    protected static $basePath = '/resources';

    /**
     * @param array $cast
     */
    public function setCast($cast)
    {
        $this->cast = $cast;
    }

    /**
     * @param $whiteListed
     */
    public function setWhitelisted($whiteListed)
    {
        $this->whiteListed = $whiteListed;
    }

    /**
     * @param $relationships
     */
    public function setRelationships($relationships)
    {
        $this->relationships = $relationships;
    }

    /**
     * @param $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @param $basePath
     */
    public static function setBasePath($basePath)
    {
        self::$basePath = $basePath;
    }
}
