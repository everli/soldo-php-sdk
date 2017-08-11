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
     * @param $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @param $itemType
     */
    public function setItemType($itemType)
    {
        $this->itemType = $itemType;
    }
}
