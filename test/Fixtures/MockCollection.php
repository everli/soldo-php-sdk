<?php

namespace Soldo\Tests\Fixtures;

use Soldo\Resources\SoldoCollection;


/**
 * Class MockColleciton
 */
class MockCollection extends SoldoCollection
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
