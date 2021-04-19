<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Resources\Group;

/**
 * Class ExpenseCentreTest
 */
class GroupTest extends TestCase
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
            'note' => 'bar',
            'type' => 'TEAM',
        ];
    }

    public function testConstructor()
    {
        $data = $this->getResourceData();
        $resource = new Group();
        foreach ($data as $key => $value) {
            $this->assertNull($resource->{$key});
        }

        $resource = new Group($data);
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $resource->{$key});
        }
    }

    public function testFill()
    {
        $data = $this->getResourceData();
        $resource = new Group();
        foreach ($data as $key => $value) {
            $this->assertNull($resource->{$key});
        }

        $resource->fill($data);
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $resource->{$key});
        }
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidPathException
     */
    public function testGetRemotePathMissingId()
    {
        $resource = new Group();
        $resource->getRemotePath();
    }

    public function testGetRemotePath()
    {
        $resource = new Group(['id' => 1]);
        $remote_path = $resource->getRemotePath();
        $this->assertEquals('/groups/1', $remote_path);

        $resource->id = 'm0cpGDu45S';
        $remote_path = $resource->getRemotePath();
        $this->assertEquals('/groups/m0cpGDu45S', $remote_path);
    }

    public function testToArray()
    {
        $resource = new Group();
        $this->assertEquals([], $resource->toArray());

        $data = $this->getResourceData();
        $resource = new Group($data);
        $this->assertEquals($data, $resource->toArray());
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidRelationshipException
     */
    public function testBuildRelationshipNotMappedRelationship()
    {
        $resource = new Group();
        $resource->buildRelationship('invalid-resource-name', []);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidRelationshipException
     */
    public function testGetRelationshipRemotePathNotMappedRelationship()
    {
        $resource = new Group();
        $remotePath = $resource->getRelationshipRemotePath('resources');
    }
}
