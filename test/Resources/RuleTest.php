<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Resources\Rule;

/**
 * Class RuleTest
 */
class RuleTest extends TestCase
{
    /**
     * @return array
     */
    private function getResourceData()
    {
        return [
            'name' => 'MaxPerTx',
            'enabled' => false,
            'amount' => 0,
        ];
    }

    public function testConstructor()
    {
        $data = $this->getResourceData();
        $resource = new Rule();
        foreach ($data as $key => $value) {
            $this->assertNull($resource->{$key});
        }

        $resource = new Rule($data);
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $resource->{$key});
        }
    }

    public function testFill()
    {
        $data = $this->getResourceData();
        $resource = new Rule();
        foreach ($data as $key => $value) {
            $this->assertNull($resource->{$key});
        }

        $resource->fill($data);
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $resource->{$key});
        }
    }


    public function testGetRemotePath()
    {
        $resource = new Rule();
        $this->assertEquals('/rules', $resource->getRemotePath());
    }

    public function testToArray()
    {
        $resource = new Rule();
        $this->assertEquals([], $resource->toArray());

        $data = $this->getResourceData();
        $resource = new Rule($data);
        $this->assertEquals($data, $resource->toArray());
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidRelationshipException
     */
    public function testBuildRelationshipNotMappedRelationship()
    {
        $resource = new Rule();
        $resource->buildRelationship('invalid-resource-name', []);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidRelationshipException
     */
    public function testGetRelationshipRemotePathNotMappedRelationship()
    {
        $resource = new Rule();
        $remotePath = $resource->getRelationshipRemotePath('resources');
    }

    public function testFilterWhiteList()
    {
        $resource = new Rule();

        $data = ['foo' => 'bar', 'john' => 'doe', 'lorem' => 'ipsum'];
        $whitelistedData = $resource->filterWhiteList($data);
        $this->assertEquals([], $whitelistedData);
    }
}
