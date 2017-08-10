<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Resources\Wallet;

/**
 * Class WalletTest
 */
class WalletTest extends TestCase
{
    /**
     * @return array
     */
    private function getResourceData()
    {
        return [
            'id' => '585caa6e-096a-11e7-9088-0a3392c1c947',
            'name' => 'Wallet1',
            'currency_code' => 'EUR',
            'available_amount' => 14782,
            'blocked_amount' => 24.18,
            'primary_user_type' => 'company',
            'visible' => true,
        ];
    }

    public function testConstructor()
    {
        $data = $this->getResourceData();
        $resource = new Wallet();
        foreach ($data as $key => $value) {
            $this->assertNull($resource->{$key});
        }

        $resource = new Wallet($data);
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $resource->{$key});
        }
    }

    public function testFill()
    {
        $data = $this->getResourceData();
        $resource = new Wallet();
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
        $resource = new Wallet();
        $resource->getRemotePath();
    }

    public function testGetRemotePath()
    {
        $resource = new Wallet(['id' => 1]);
        $remote_path = $resource->getRemotePath();
        $this->assertEquals('/wallets/1', $remote_path);

        $resource->id = 'm0cpGDu45S';
        $remote_path = $resource->getRemotePath();
        $this->assertEquals('/wallets/m0cpGDu45S', $remote_path);
    }

    public function testToArray()
    {
        $resource = new Wallet();
        $this->assertEquals([], $resource->toArray());

        $data = $this->getResourceData();
        $resource = new Wallet($data);
        $this->assertEquals($data, $resource->toArray());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBuildRelationshipNotMappedRelationship()
    {
        $resource = new Wallet();
        $resource->buildRelationship('invalid-resource-name', []);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetRelationshipRemotePathNotMappedRelationship()
    {
        $resource = new Wallet();
        $remotePath = $resource->getRelationshipRemotePath('resources');
    }


    public function testFilterWhiteList()
    {
        $resource = new Wallet();

        $data = ['foo' => 'bar', 'john' => 'doe', 'lorem' => 'ipsum'];
        $whitelistedData = $resource->filterWhiteList($data);
        $this->assertEquals([], $whitelistedData);

        $data = ['foo' => 'bar', 'john' => 'doe', 'lorem' => 'ipsum', 'custom_reference_id' => 'id', 'department' => 'foo'];
        $whitelistedData = $resource->filterWhiteList($data);
        $this->assertEquals([], $whitelistedData);
    }
}
