<?php

namespace Soldo\Tests\Fixtures;

use Soldo\Resources\Collection;

/**
 * Class MockColleciton
 */
class MockCollection extends Collection
{
    protected $itemType = MockResource::class;

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        parent::setPath($path);
    }

    /**
     * @param string $itemType
     */
    public function setItemType($itemType)
    {
        parent::setItemType($itemType);
    }
}
