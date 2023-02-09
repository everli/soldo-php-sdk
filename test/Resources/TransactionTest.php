<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Exceptions\SoldoInvalidPathException;
use Soldo\Exceptions\SoldoInvalidRelationshipException;
use Soldo\Resources\Transaction;

/**
 * Class TransactionTest
 */
class TransactionTest extends TestCase
{
    /**
     * @return array
     */
    private function getResourceData()
    {
        return [
            'transaction_id' => '562-182421003-1485958599394',
            'wallet_id' => '585d6a1c-096a-11e7-9088-0a3392c1c947',
            'wallet_name' => 'EURO',
            'status' => 'Settled',
            'category' => 'Payment',
            'transaction_sign' => 'Negative',
            'amount' => 10,
            'amount_currency' => 'EUR',
            'tx_amount' => 10,
            'tx_amount_currency' => 'EUR',
            'fee_amount' => 0,
            'fee_currency' => 'EUR',
            'date' => '2017-02-01T14:15:00',
            'settlement_date' => '2017-02-01T14:15:39Z',
            'merchant' =>
                [
                    'name' => 'Ishtar Restaurant',
                    'raw_name' => 'ISHTAR RESTAURANT      LONDON W1U    GBR',
                    'code' => 'apple',
                    'address' => '',
                ],
            'merchant_category' =>
                [
                    'description' => 'Services',
                    'mcc' => '5812',
                    'mcc_description' => 'CATERERS',
                    'code' => '5812',
                ],
            'tags' =>
                [
                    [
                        'tag' => 'Cancelleria',
                        'dictionary' => 'defaul',
                    ],
                ],
            'card_id' => '47a15e93-096a-11e7-9088-0a3392c1c947',
            'masked_pan' => '999999******6952',
            'owner_id' => 'soldo-000011',
            'owner_type' => 'company',
            'owner_name' => 'IT',
            'details' =>
                [
                    'de022' => '051',
                    'de061' => '0250260000010000800826W1U6AZ',
                    'tx_country' => 'GBR',
                    'is_card_present' => true,
                    'is_atm_transaction' => false,
                ],
        ];
    }

    public function testConstructor()
    {
        $data = $this->getResourceData();
        $resource = new Transaction();
        foreach ($data as $key => $value) {
            $this->assertNull($resource->{$key});
        }

        $resource = new Transaction($data);
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $resource->{$key});
        }
    }

    public function testFill()
    {
        $data = $this->getResourceData();
        $resource = new Transaction();
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

        $resource = new Transaction();
        $resource->getRemotePath();
    }

    public function testGetRemotePath()
    {
        $resource = new Transaction(['id' => 1]);
        $remote_path = $resource->getRemotePath();
        $this->assertEquals('/transactions/1', $remote_path);

        $resource->id = 'm0cpGDu45S';
        $remote_path = $resource->getRemotePath();
        $this->assertEquals('/transactions/m0cpGDu45S', $remote_path);
    }

    public function testToArray()
    {
        $resource = new Transaction();
        $this->assertEquals([], $resource->toArray());

        $data = $this->getResourceData();
        $resource = new Transaction($data);
        $this->assertEquals($data, $resource->toArray());
    }

    public function testBuildRelationshipNotMappedRelationship()
    {
        $this->expectException(SoldoInvalidRelationshipException::class);

        $resource = new Transaction();
        $resource->buildRelationship('invalid-resource-name', []);
    }

    public function testGetRelationshipRemotePathNotMappedRelationship()
    {
        $this->expectException(SoldoInvalidRelationshipException::class);

        $resource = new Transaction();
        $resource->getRelationshipRemotePath('resources');
    }

    public function testFilterWhiteList()
    {
        $resource = new Transaction();

        $data = ['foo' => 'bar', 'john' => 'doe', 'lorem' => 'ipsum'];
        $whitelistedData = $resource->filterWhiteList($data);
        $this->assertEquals([], $whitelistedData);

        $data = ['foo' => 'bar', 'john' => 'doe', 'lorem' => 'ipsum', 'custom_reference_id' => 'id', 'department' => 'foo'];
        $whitelistedData = $resource->filterWhiteList($data);
        $this->assertEquals([], $whitelistedData);
    }
}
