<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Resources\ExpenseCentre;
use Soldo\Resources\ExpenseCentres;

/**
 * Class ExpenseCentresTest
 */
class ExpenseCentresTest extends TestCase
{
    /**
     * @return array
     */
    protected function getCollectionData()
    {
        return [
            'total' => 2,
            'pages' => 1,
            'page_size' => 25,
            'current_page' => 0,
            'results_size' => 2,
            'results' =>
                [
                    [
                        'id' => 'soldo-000008',
                        'name' => 'Test Department',
                        'custom_reference_id' => 'mySecondCustomReference',
                        'status' => 'ACTIVE',
                        'visible' => true,
                    ],
                    [
                        'id' => 'soldo-000009',
                        'name' => 'Marketing',
                        'status' => 'ACTIVE',
                        'visible' => true,
                    ],
                ],
        ];
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidCollectionException
     */
    public function testFillInvalidData()
    {
        $collection = new ExpenseCentres();
        $collection->fill([]);
    }

    public function testFill()
    {
        $collectionData = $this->getCollectionData();
        $collection = new ExpenseCentres();
        $items = $collection->fill($collectionData)->get();

        foreach ($items as $key => $item) {
            /** @var  $item  ExpenseCentre */
            $this->assertInstanceOf(ExpenseCentre::class, $item);
            $this->assertEquals($collectionData['results'][$key], $item->toArray());
        }
    }

    public function testGetRemotePath()
    {
        $collection = new ExpenseCentres();
        $this->assertEquals('/expensecentres', $collection->getRemotePath());
    }
}
