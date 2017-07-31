<?php

namespace Soldo\Tests;

use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Soldo\Resources\Wallet;
use Soldo\Soldo;

class SoldoTest extends TestCase {

    /** @var Soldo $soldo */
    private static $soldo;

    /** @var string $walletId */
    private static $walletId;

    public static function setUpBeforeClass()
    {
        self::$soldo = new Soldo(
            [
                'client_id' => SoldoTestCredentials::CLIENT_ID,
                'client_secret' => SoldoTestCredentials::CLIENT_SECRET,
                'environment' => 'demo',
            ]
        );
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoSDKException
     * @expectedExceptionMessage Required "client_id" key is missing in config
     */
    public function testConstructorWithoutConfig()
    {
        $s = new Soldo([]);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoSDKException
     * @expectedExceptionMessage Required "client_id" key is missing in config
     */
    public function testConstructorWithoutClientIdParam()
    {
        $s = new Soldo(['client_secret' => 'FOO']);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoSDKException
     * @expectedExceptionMessage Required "client_secret" key is missing in config
     */
    public function testConstructorWithoutClientSecretParam()
    {
        $s = new Soldo(['client_id' => 'FOO']);
    }


    public function testConstructor()
    {
        $s = new Soldo(['client_id' => 'FOO', 'client_secret' => 'BAR']);
        $this->assertInstanceOf(Soldo::class, $s);
    }


    public function testGetWallets()
    {
        $wallets = self::$soldo->getWallets();
        $this->assertInternalType('array', $wallets);
        $this->assertTrue(count($wallets) > 0, 'There should be at least one Wallet'); //TODO: verify that this assertion is true

        /** @var Wallet $wallet */
        $wallet = $wallets[0];
        $this->assertInternalType('string', $wallet->id);
        self::$walletId = $wallet->id;

        foreach ($wallets as $wallet) {
            /** @var Wallet $wallet  */
            $this->assertInstanceOf(Wallet::class, $wallet);
        }
    }

    /**
     * @expectedException \GuzzleHttp\Exception\ClientException
     * @expectedExceptionCode 404
     */
    public function testGetWalletsNotFound()
    {
        $wallet = self::$soldo->getWallet('A_NOT_EXISTING_WALLET_ID');
    }

    public function testGetWallet()
    {
        $wallet = self::$soldo->getWallet(self::$walletId);
        $this->assertInstanceOf(Wallet::class, $wallet);
        $this->assertEquals(self::$walletId, $wallet->id);
    }



}
