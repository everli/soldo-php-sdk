<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Exceptions\SoldoInvalidRelationshipException;
use Soldo\Resources\Company;

/**
 * Class CompanyTest
 */
class CompanyTest extends TestCase
{
    /**
     * @return array
     */
    private function getResourceData()
    {
        return [
            'name' => 'Soldo',
            'vat_number' => '494920202',
            'company_account_id' => 'soldo',
        ];
    }

    public function testConstructor()
    {
        $data = $this->getResourceData();
        $resource = new Company();
        foreach ($data as $key => $value) {
            $this->assertNull($resource->{$key});
        }

        $resource = new Company($data);
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $resource->{$key});
        }
    }

    public function testFill()
    {
        $data = $this->getResourceData();
        $resource = new Company();
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
        $resource = new Company();
        $remote_path = $resource->getRemotePath();
        $this->assertEquals('/company', $remote_path);
    }

    public function testToArray()
    {
        $resource = new Company();
        $this->assertEquals([], $resource->toArray());

        $data = $this->getResourceData();
        $resource = new Company($data);
        $this->assertEquals($data, $resource->toArray());
    }

    public function testBuildRelationshipNotMappedRelationship()
    {
        $this->expectException(SoldoInvalidRelationshipException::class);

        $resource = new Company();
        $resource->buildRelationship('invalid-resource-name', []);
    }

    public function testGetRelationshipRemotePathNotMappedRelationship()
    {
        $this->expectException(SoldoInvalidRelationshipException::class);

        $resource = new Company();
        $remotePath = $resource->getRelationshipRemotePath('resources');
    }

    public function testFilterWhiteList()
    {
        $resource = new Company();

        $data = ['foo' => 'bar', 'john' => 'doe', 'lorem' => 'ipsum'];
        $whitelistedData = $resource->filterWhiteList($data);
        $this->assertEquals([], $whitelistedData);
    }
}
