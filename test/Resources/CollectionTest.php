<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Exceptions\SoldoInvalidCollectionException;
use Soldo\Exceptions\SoldoInvalidResourceException;
use Soldo\Resources\Collection;
use Soldo\Tests\Fixtures\MockCollection;
use Soldo\Tests\Fixtures\MockResource;

/**
 * Class CollectionTest
 *
 * @backupStaticAttributes enabled
 */
class CollectionTest extends TestCase
{
    /**
     * @return array
     */
    protected function getCollectionData()
    {
        $data = [
            "total" => 168,
            "pages" => 7,
            "page_size" => 25,
            "current_page" => 0,
            "results_size" => 25,
            "results" => [],
        ];

        for ($i = 0; $i < 25; $i++) {
            $item = [];
            $item['id'] = $i;
            $item['foo'] = 'bar';
            $data['results'][] = $item;
        }

        return $data;
    }

    public function testFillNullItemType()
    {
        $this->expectException(SoldoInvalidResourceException::class);
        new Collection(null);
    }

    public function testFillInvalidItemType()
    {
        $this->expectException(SoldoInvalidResourceException::class);
        new Collection('InvalidClassName');
    }

    public function testFillEmptyData()
    {
        $this->expectException(SoldoInvalidCollectionException::class);

        $collection = new Collection(MockResource::class);
        $collection->fill([]);
    }

    public function testFillMissingPages()
    {
        $this->expectException(SoldoInvalidCollectionException::class);

        $data = $this->getCollectionData();
        unset($data['pages']);
        $collection = new Collection(MockResource::class);
        $collection->fill($data);
    }

    public function testFillInvalidPages()
    {
        $this->expectException(SoldoInvalidCollectionException::class);

        $data = $this->getCollectionData();
        $data['pages'] = 'FOO';
        $collection = new Collection(MockResource::class);
        $collection->fill($data);
    }

    public function testFillMissingTotal()
    {
        $this->expectException(SoldoInvalidCollectionException::class);

        $data = $this->getCollectionData();
        unset($data['total']);
        $collection = new Collection(MockResource::class);
        $collection->fill($data);
    }

    public function testFillInvalidTotal()
    {
        $this->expectException(SoldoInvalidCollectionException::class);

        $data = $this->getCollectionData();
        $data['total'] = 'FOO';
        $collection = new Collection(MockResource::class);
        $collection->fill($data);
    }

    public function testFillMissingPageSize()
    {
        $this->expectException(SoldoInvalidCollectionException::class);

        $data = $this->getCollectionData();
        unset($data['page_size']);
        $collection = new Collection(MockResource::class);
        $collection->fill($data);
    }

    public function testFillInvalidPageSize()
    {
        $this->expectException(SoldoInvalidCollectionException::class);

        $data = $this->getCollectionData();
        $data['page_size'] = 'FOO';
        $collection = new Collection(MockResource::class);
        $collection->fill($data);
    }

    public function testFillMissingCurrentPage()
    {
        $this->expectException(SoldoInvalidCollectionException::class);

        $data = $this->getCollectionData();
        unset($data['current_page']);
        $collection = new Collection(MockResource::class);
        $collection->fill($data);
    }

    public function testFillInvalidCurrentPage()
    {
        $this->expectException(SoldoInvalidCollectionException::class);

        $data = $this->getCollectionData();
        $data['current_page'] = 'FOO';
        $collection = new Collection(MockResource::class);
        $collection->fill($data);
    }

    public function testFillMissingResultsSize()
    {
        $this->expectException(SoldoInvalidCollectionException::class);

        $data = $this->getCollectionData();
        unset($data['results_size']);
        $collection = new Collection(MockResource::class);
        $collection->fill($data);
    }

    public function testFillInvalidResultsSize()
    {
        $this->expectException(SoldoInvalidCollectionException::class);

        $data = $this->getCollectionData();
        $data['results_size'] = 'FOO';
        $collection = new Collection(MockResource::class);
        $collection->fill($data);
    }

    public function testFillMissingResults()
    {
        $this->expectException(SoldoInvalidCollectionException::class);

        $data = $this->getCollectionData();
        unset($data['results']);
        $collection = new Collection(MockResource::class);
        $collection->fill($data);
    }

    public function testFillInvalidResults()
    {
        $this->expectException(SoldoInvalidCollectionException::class);

        $data = $this->getCollectionData();
        $data['results_size'] = 'FOO';
        $collection = new Collection(MockResource::class);
        $collection->fill($data);

        $data = $this->getCollectionData();
        $data['results_size'] = 1;
        $collection = new Collection(MockResource::class);
        $collection->fill($data);
    }

    public function testFillAndGet()
    {
        $data = $this->getCollectionData();
        $collection = new Collection(MockResource::class);
        $items = $collection->fill($data)->get();

        $itemsArray = [];
        foreach ($items as $item) {
            /** @var $item MockResource */
            $this->assertInstanceOf(MockResource::class, $item);
            $itemsArray[] = $item->toArray();
        }
        $this->assertEquals($data['results'], $itemsArray);
    }

    public function testGetRemotePath()
    {
        $collection = new Collection(MockResource::class);
        $this->assertEquals('/resources', $collection->getRemotePath());
    }
}
