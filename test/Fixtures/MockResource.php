<?php

namespace Soldo\Tests\Fixtures;

use Soldo\Resources\Resource;

/**
 * Class MockResource
 */
class MockResource extends Resource
{

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
     * @param $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }
}
