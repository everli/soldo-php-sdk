<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Resources\ExpenseCentre;

/**
 * Class ExpenseCentreTest
 */
class ExpenseCentreTest extends TestCase
{
    /**
     * @return array
     */
    private function getResourceData()
    {
        return [
            'id' => 'soldo-000008',
            'name' => 'Test Department',
            'custom_reference_id' => 'mySecondCustomReference',
            'status' => 'ACTIVE',
            'visible' => true,
        ];
    }

    public function testConstructor()
    {
        $data = $this->getResourceData();
        $resource = new ExpenseCentre();
        foreach ($data as $key => $value) {
            $this->assertNull($resource->{$key});
        }

        $resource = new ExpenseCentre($data);
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $resource->{$key});
        }
    }

    public function testFill()
    {
        $data = $this->getResourceData();
        $resource = new ExpenseCentre();
        foreach ($data as $key => $value) {
            $this->assertNull($resource->{$key});
        }

        $resource->fill($data);
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $resource->{$key});
        }
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testGetRemotePathMissingId()
    {
        $resource = new ExpenseCentre();
        $resource->getRemotePath();
    }

    public function testGetRemotePath()
    {
        $resource = new ExpenseCentre(['id' => 1]);
        $remote_path = $resource->getRemotePath();
        $this->assertEquals('/expensecentres/1', $remote_path);

        $resource->id = 'm0cpGDu45S';
        $remote_path = $resource->getRemotePath();
        $this->assertEquals('/expensecentres/m0cpGDu45S', $remote_path);
    }

    public function testToArray()
    {
        $resource = new ExpenseCentre();
        $this->assertEquals([], $resource->toArray());

        $data = $this->getResourceData();
        $resource = new ExpenseCentre($data);
        $this->assertEquals($data, $resource->toArray());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBuildRelationshipNotMappedRelationship()
    {
        $resource = new ExpenseCentre();
        $resource->buildRelationship('invalid-resource-name', []);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetRelationshipRemotePathNotMappedRelationship()
    {
        $resource = new ExpenseCentre();
        $remotePath = $resource->getRelationshipRemotePath('resources');
    }


    public function testFilterWhiteList()
    {
        $resource = new ExpenseCentre();

        $data = ['foo' => 'bar', 'john' => 'doe', 'lorem' => 'ipsum'];
        $whitelistedData = $resource->filterWhiteList($data);
        $this->assertEquals([], $whitelistedData);

        $data = ['foo' => 'bar', 'john' => 'doe', 'lorem' => 'ipsum', 'custom_reference_id' => 'id', 'assignee' => 'foo'];
        $whitelistedData = $resource->filterWhiteList($data);
        $this->assertEquals(['custom_reference_id' => 'id', 'assignee' => 'foo'], $whitelistedData);
    }
}
