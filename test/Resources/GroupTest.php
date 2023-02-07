<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Exceptions\SoldoInvalidPathException;
use Soldo\Exceptions\SoldoInvalidRelationshipException;
use Soldo\Resources\Group;

/**
 * Class GroupTest
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

    public function testGetRemotePathMissingId()
    {
        $this->expectException(SoldoInvalidPathException::class);

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

    public function testBuildRelationshipNotMappedRelationship()
    {
        $this->expectException(SoldoInvalidRelationshipException::class);

        $resource = new Group();
        $resource->buildRelationship('invalid-resource-name', []);
    }

    public function testGetRelationshipRemotePathNotMappedRelationship()
    {
        $this->expectException(SoldoInvalidRelationshipException::class);

        $resource = new Group();
        $resource->getRelationshipRemotePath('resources');
    }
}
