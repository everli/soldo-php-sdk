<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Exceptions\SoldoInvalidCollectionException;
use Soldo\Tests\Fixtures\MockCollection;
use Soldo\Tests\Fixtures\MockResource;


/**
 * Class SoldoCollectionTest
 */
class SoldoCollectionTest extends TestCase
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
            "results" => []
        ];

        for ($i = 0; $i < 25; $i++) {
            $item = [];
            $item['id'] = $i;
            $item['foo'] = 'bar';
            $data['results'][] = $item;
        }

        return $data;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFillNullItemType()
    {
        $collection = new MockCollection();
        $collection->setItemType(null);
        $collection->fill([]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Could not generate a Soldo collection InvalidClassName doesn't exist
     */
    public function testFillInvalidItemType()
    {
        $collection = new MockCollection();
        $collection->setItemType('InvalidClassName');
        $collection->fill([]);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidCollectionException
     */
    public function testFillEmptyData()
    {
        $collection = new MockCollection();
        $collection->fill([]);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidCollectionException
     */
    public function testFillMissingPages()
    {
        $data = $this->getCollectionData();
        unset($data['pages']);
        $collection = new MockCollection();
        $collection->fill($data);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidCollectionException
     */
    public function testFillInvalidPages()
    {
        $data = $this->getCollectionData();
        $data['pages'] = 'FOO';
        $collection = new MockCollection();
        $collection->fill($data);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidCollectionException
     */
    public function testFillMissingTotal()
    {
        $data = $this->getCollectionData();
        unset($data['total']);
        $collection = new MockCollection();
        $collection->fill($data);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidCollectionException
     */
    public function testFillInvalidTotal()
    {
        $data = $this->getCollectionData();
        $data['total'] = 'FOO';
        $collection = new MockCollection();
        $collection->fill($data);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidCollectionException
     */
    public function testFillMissingPageSize()
    {
        $data = $this->getCollectionData();
        unset($data['page_size']);
        $collection = new MockCollection();
        $collection->fill($data);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidCollectionException
     */
    public function testFillInvalidPageSize()
    {
        $data = $this->getCollectionData();
        $data['page_size'] = 'FOO';
        $collection = new MockCollection();
        $collection->fill($data);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidCollectionException
     */
    public function testFillMissingCurrentPage()
    {
        $data = $this->getCollectionData();
        unset($data['current_page']);
        $collection = new MockCollection();
        $collection->fill($data);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidCollectionException
     */
    public function testFillInvalidCurrentPage()
    {
        $data = $this->getCollectionData();
        $data['current_page'] = 'FOO';
        $collection = new MockCollection();
        $collection->fill($data);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidCollectionException
     */
    public function testFillMissingResultsSize()
    {
        $data = $this->getCollectionData();
        unset($data['results_size']);
        $collection = new MockCollection();
        $collection->fill($data);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidCollectionException
     */
    public function testFillInvalidResultsSize()
    {
        $data = $this->getCollectionData();
        $data['results_size'] = 'FOO';
        $collection = new MockCollection();
        $collection->fill($data);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidCollectionException
     */
    public function testFillMissingResults()
    {
        $data = $this->getCollectionData();
        unset($data['results']);
        $collection = new MockCollection();
        $collection->fill($data);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidCollectionException
     */
    public function testFillInvalidResults()
    {
        $data = $this->getCollectionData();
        $data['results_size'] = 'FOO';
        $collection = new MockCollection();
        $collection->fill($data);

        $data = $this->getCollectionData();
        $data['results_size'] = 1;
        $collection = new MockCollection();
        $collection->fill($data);
    }

    public function testFill()
    {
        $data = $this->getCollectionData();
        $collection = new MockCollection();
        $collection->fill($data);

        $itemsArray = [];
        $items = $collection->get();
        foreach ($items as $item) {
            /** @var $item MockResource */
            $this->assertInstanceOf(MockResource::class, $item);
            $itemsArray[] = $item->toArray();
        }
        $this->assertEquals($data['results'], $itemsArray);
    }



}
