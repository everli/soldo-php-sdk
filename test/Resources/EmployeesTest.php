<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Resources\Employee;
use Soldo\Resources\Employees;

/**
 * Class EmployeesTest
 */
class EmployeesTest extends TestCase
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
                        'id' => '62464771',
                        'name' => 'KXAWM',
                        'surname' => 'KXAWMSurname',
                        'job_title' => 'Content Strategist',
                        'department' => 'IT Department2',
                        'email' => '454723042106@soldo.com',
                        'mobile' => '454723042106',
                        'custom_reference_id' => 'myFirstCustomReference',
                        'status' => 'ACTIVE',
                        'visible' => false,
                    ],
                    [
                        'id' => '12621231',
                        'name' => 'Blake',
                        'surname' => 'Ferguson',
                        'job_title' => 'PR',
                        'email' => 'blake.ferguson@soldi.co.uk',
                        'mobile' => '+44100001',
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
        $collection = new Employees();
        $collection->fill([]);
    }

    public function testFill()
    {
        $collectionData = $this->getCollectionData();
        $collection = new Employees();
        $items = $collection->fill($collectionData)->get();

        foreach ($items as $key => $item) {
            /** @var  $item  Employee */
            $this->assertInstanceOf(Employee::class, $item);
            $this->assertEquals($collectionData['results'][$key], $item->toArray());
        }
    }

    public function testGetRemotePath()
    {
        $collection = new Employees();
        $this->assertEquals('/employees', $collection->getRemotePath());
    }
}
