<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Resources\Employee;

/**
 * Class EmployeeTest
 */
class EmployeeTest extends TestCase
{
    /**
     * @return array
     */
    private function getResourceData()
    {
        return [
            'id' => 'soldo-000027',
            'name' => 'John',
            'surname' => 'Snow',
            'email' => 'jsnow@soldo.com',
            'mobile' => '+3911123323232',
            'status' => 'ACTIVE',
            'visible' => true,
        ];
    }

    public function testConstructor()
    {
        $data = $this->getResourceData();
        $resource = new Employee();
        foreach ($data as $key => $value) {
            $this->assertNull($resource->{$key});
        }

        $resource = new Employee($data);
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $resource->{$key});
        }
    }

    public function testFill()
    {
        $data = $this->getResourceData();
        $resource = new Employee();
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
        $resource = new Employee();
        $resource->getRemotePath();
    }

    public function testGetRemotePath()
    {
        $resource = new Employee(['id' => 1]);
        $remote_path = $resource->getRemotePath();
        $this->assertEquals('/employees/1', $remote_path);

        $resource->id = 'm0cpGDu45S';
        $remote_path = $resource->getRemotePath();
        $this->assertEquals('/employees/m0cpGDu45S', $remote_path);
    }

    public function testToArray()
    {
        $resource = new Employee();
        $this->assertEquals([], $resource->toArray());

        $data = $this->getResourceData();
        $resource = new Employee($data);
        $this->assertEquals($data, $resource->toArray());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBuildRelationshipNotMappedRelationship()
    {
        $resource = new Employee();
        $resource->buildRelationship('invalid-resource-name', []);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetRelationshipRemotePathNotMappedRelationship()
    {
        $resource = new Employee();
        $remotePath = $resource->getRelationshipRemotePath('resources');
    }

    public function testFilterWhiteList()
    {
        $resource = new Employee();

        $data = ['foo' => 'bar', 'john' => 'doe', 'lorem' => 'ipsum'];
        $whitelistedData = $resource->filterWhiteList($data);
        $this->assertEquals([], $whitelistedData);

        $data = ['foo' => 'bar', 'john' => 'doe', 'lorem' => 'ipsum', 'custom_reference_id' => 'id', 'department' => 'foo'];
        $whitelistedData = $resource->filterWhiteList($data);
        $this->assertEquals(['custom_reference_id' => 'id', 'department' => 'foo'], $whitelistedData);
    }
}
