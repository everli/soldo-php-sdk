<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Exceptions\SoldoInvalidFingerprintException;
use Soldo\Exceptions\SoldoInvalidPathException;
use Soldo\Exceptions\SoldoInvalidRelationshipException;
use Soldo\Resources\InternalTransfer;
use Soldo\Resources\Wallet;

/**
 * Class InternalTransferTest
 */
class InternalTransferTest extends TestCase
{
    /**
     * @return array
     */
    private function getResourceData()
    {
        return [
            'amount' => 10,
            'currency' => 'EUR',
            'datetime' => '2017-03-29T07:25:17.968Z',
            'from_wallet' =>
                [
                    'id' => '585caa6e-096a-11e7-9088-0a3392c1c947',
                    'name' => 'Wallet1',
                    'currency_code' => 'EUR',
                    'available_amount' => 14772,
                    'blocked_amount' => 24.18,
                    'primary_user_type' => 'company',
                    'visible' => true,
                ],
            'to_wallet' =>
                [
                    'id' => '585cab95-096a-11e7-9088-0a3392c1c947',
                    'name' => 'EURO',
                    'currency_code' => 'EUR',
                    'available_amount' => 4601,
                    'blocked_amount' => 0,
                    'primary_user_type' => 'employee',
                    'primary_user_public_id' => '62464771',
                    'visible' => true,
                ],
        ];
    }

    public function testConstructor()
    {
        $data = $this->getResourceData();
        $resource = new InternalTransfer();
        foreach ($data as $key => $value) {
            $this->assertNull($resource->{$key});
        }
        $resource = new InternalTransfer($data);
        $this->assertEquals($data['amount'], $resource->amount);
        $this->assertEquals($data['currency'], $resource->currency);
        $this->assertEquals($data['datetime'], $resource->datetime);

        $this->assertInstanceOf(Wallet::class, $resource->from_wallet);
        $this->assertEquals($data['from_wallet'], $resource->from_wallet->toArray());

        $this->assertInstanceOf(Wallet::class, $resource->to_wallet);
        $this->assertEquals($data['to_wallet'], $resource->to_wallet->toArray());
    }

    public function testFill()
    {
        $data = $this->getResourceData();
        $resource = new InternalTransfer();
        foreach ($data as $key => $value) {
            $this->assertNull($resource->{$key});
        }

        $resource->fill($data);
        $this->assertEquals($data['amount'], $resource->amount);
        $this->assertEquals($data['currency'], $resource->currency);
        $this->assertEquals($data['datetime'], $resource->datetime);

        $this->assertInstanceOf(Wallet::class, $resource->from_wallet);
        $this->assertEquals($data['from_wallet'], $resource->from_wallet->toArray());

        $this->assertInstanceOf(Wallet::class, $resource->to_wallet);
        $this->assertEquals($data['to_wallet'], $resource->to_wallet->toArray());
    }

    public function testGetRemotePathMissingFromWalletId()
    {
        $this->expectException(SoldoInvalidPathException::class);

        $resource = new InternalTransfer();
        $remote_path = $resource->getRemotePath();
    }

    public function testGetRemotePathMissingToWalletId()
    {
        $this->expectException(SoldoInvalidPathException::class);
        $this->expectExceptionMessage("Soldo\Resources\InternalTransfer toWalletId is not defined");

        $resource = new InternalTransfer();
        $resource->fromWalletId = 'from-wallet-id';
        $remote_path = $resource->getRemotePath();
    }

    public function testGetRemotePath()
    {
        $resource = new InternalTransfer();
        $resource->fromWalletId = 'from-wallet-id';
        $resource->toWalletId = 'to-wallet-id';
        $remote_path = $resource->getRemotePath();
        $this->assertEquals('/wallets/internalTransfer/from-wallet-id/to-wallet-id', $remote_path);
    }

    public function testToArray()
    {
        $resource = new InternalTransfer();
        $this->assertEquals([], $resource->toArray());

        $data = $this->getResourceData();
        $resource = new InternalTransfer($data);
        $this->assertEquals($data, $resource->toArray());
    }

    public function testBuildRelationshipNotMappedRelationship()
    {
        $this->expectException(SoldoInvalidRelationshipException::class);

        $resource = new InternalTransfer();
        $resource->buildRelationship('invalid-resource-name', []);
    }

    public function testGetRelationshipRemotePathNotMappedRelationship()
    {
        $this->expectException(SoldoInvalidRelationshipException::class);

        $resource = new InternalTransfer();
        $resource->getRelationshipRemotePath('resources');
    }

    public function testFilterWhiteList()
    {
        $resource = new InternalTransfer();

        $data = ['foo' => 'bar', 'john' => 'doe', 'lorem' => 'ipsum'];
        $whitelistedData = $resource->filterWhiteList($data);
        $this->assertEquals([], $whitelistedData);
    }

    public function testGenerateFingerPrintMissingParams()
    {
        $this->expectException(SoldoInvalidFingerprintException::class);

        $resource = new InternalTransfer();
        $resource->generateFingerPrint('123456');
    }

    public function testGenerateFingerPrint()
    {
        $resource = new InternalTransfer();
        $resource->amount = 100;
        $resource->currency = 'EUR';
        $resource->fromWalletId = 'FROM-ID';
        $resource->toWalletId = 'TO-ID';

        $fp = $resource->generateFingerPrint('123456');
        $hash_ref = hash('sha512', '100EURFROM-IDTO-ID123456');

        $this->assertEquals($hash_ref, $fp);
    }
}
