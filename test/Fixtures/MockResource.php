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
        parent::setCast($cast);
    }

    /**
     * @param $whiteListed
     */
    public function setWhitelisted($whiteListed)
    {
        parent::setWhitelisted($whiteListed);
    }

    /**
     * @param $relationships
     */
    public function setRelationships($relationships)
    {
        parent::setRelationships($relationships);
    }

    /**
     * @param $basePath
     */
    public function setBasePath($basePath)
    {
        parent::setBasePath($basePath);
    }
}
