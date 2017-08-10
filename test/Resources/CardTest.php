<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Authentication\OAuthCredential;
use Soldo\Resources\Card;
use Soldo\Resources\Rule;

/**
 * Class CardTest
 */
class CardTest extends TestCase
{

    /**
     * @return array
     */
    private function getRulesData()
    {
        return [
            'rules' =>
                [
                    [
                        'name' => 'OpenCloseMasterLock',
                        'enabled' => true,
                    ],
                    [
                        'name' => 'OpenClose',
                        'enabled' => false,
                    ],
                    [
                        'name' => 'OpenCloseAfterOneTx',
                        'enabled' => false,
                    ],
                    [
                        'name' => 'Online',
                        'enabled' => false,
                    ],
                    [
                        'name' => 'Abroad',
                        'enabled' => false,
                    ],
                    [
                        'name' => 'CashPoint',
                        'enabled' => false,
                    ],
                    [
                        'name' => 'MaxPerTx',
                        'enabled' => false,
                        'amount' => 0,
                    ],
                ],
        ];
    }

    /**
     * @return array
     */
    private function getResourceData()
    {
        return [
            'id' => '47a09396-096a-11e7-9088-0a3392c1c947',
            'name' => 'Plastic',
            'masked_pan' => '999999******8470',
            'card_holder' => 'Boris Smith',
            'expiration_date' => '2019-10-31T23:59:59Z',
            'type' => 'PLASTIC',
            'status' => 'Normal',
            'owner_type' => 'employee',
            'owner_public_id' => '53675864',
            'wallet_id' => '585cceca-096a-11e7-9088-0a3392c1c947',
            'currency_code' => 'EUR',
            'emboss_line4' => 'EUR',
            'active' => true,
        ];
    }

    public function testConstructor()
    {
        $data = $this->getResourceData();
        $resource = new Card();
        foreach ($data as $key => $value) {
            $this->assertNull($resource->{$key});
        }

        $resource = new Card($data);
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $resource->{$key});
        }
    }

    public function testFill()
    {
        $data = $this->getResourceData();
        $resource = new Card();
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
     * @expectedExceptionMessage Cannot retrieve remote path for Soldo\Resources\Card. "id" attribute is not defined.
     */
    public function testGetRemotePathMissingId()
    {
        $resource = new Card();
        $resource->getRemotePath();
    }

    public function testGetRemotePath()
    {
        $resource = new Card(['id' => 1]);
        $remote_path = $resource->getRemotePath();
        $this->assertEquals('/cards/1', $remote_path);

        $resource->id = 'm0cpGDu45S';
        $remote_path = $resource->getRemotePath();
        $this->assertEquals('/cards/m0cpGDu45S', $remote_path);
    }

    public function testToArray()
    {
        $resource = new Card();
        $this->assertEquals([], $resource->toArray());

        $data = $this->getResourceData();
        $resource = new Card($data);
        $this->assertEquals($data, $resource->toArray());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBuildRelationshipNotMappedRelationship()
    {
        $resource = new Card();
        $resource->buildRelationship('invalid-resource-name', []);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidRelationshipException
     */
    public function testBuildRelationshipInvalidData()
    {
        $resource = new Card();
        $resources = $resource->buildRelationship('rules', 'not-an-array');
        $resources = $resource->buildRelationship('rules', []);
    }

    public function testBuildRelationship()
    {
        $relationshipData = $this->getRulesData();
        $resource = new Card();
        $resources = $resource->buildRelationship('rules', $relationshipData);
        $this->assertCount(7, $resources);
        foreach ($resources as $key => $r) {
            /** @var Rule $r */
            $this->assertInstanceOf(Rule::class, $r);
            $this->assertEquals($relationshipData['rules'][$key], $r->toArray());
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetRelationshipRemotePathNotMappedRelationship()
    {
        $resource = new Card();
        $remotePath = $resource->getRelationshipRemotePath('resources');
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Cannot retrieve remote path for Soldo\Resources\Card. "id" attribute is not defined.
     */
    public function testGetRelationshipRemotePathMissingId()
    {
        $resource = new Card();
        $remotePath = $resource->getRelationshipRemotePath('rules');
    }

    public function testGetRelationshipRemotePath()
    {
        $resource = new Card($this->getResourceData());
        $remotePath = $resource->getRelationshipRemotePath('rules');
        $this->assertEquals('/cards/47a09396-096a-11e7-9088-0a3392c1c947/rules', $remotePath);
    }

    public function testFilterWhiteList()
    {
        $resource = new Card();

        $data = ['foo' => 'bar', 'john' => 'doe', 'lorem' => 'ipsum'];
        $whitelistedData = $resource->filterWhiteList($data);
        $this->assertEquals([], $whitelistedData);

    }






}
