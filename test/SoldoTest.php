<?php

namespace Soldo\Tests;

use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Soldo\Exceptions\SoldoModelNotFoundException;
use Soldo\Resources\ExpenseCentre;
use Soldo\Resources\Wallet;
use Soldo\Soldo;

class SoldoTest extends TestCase
{

    /** @var Soldo $soldo */
    private static $soldo;

    /** @var string $walletId */
    private static $walletId;

    /** @var  string $expenseCentreId  */
    private static $expenseCentreId;

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
     * @expectedException \Soldo\Exceptions\SoldoModelNotFoundException
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


    public function testGetExpenseCentres()
    {
        $expenseCentres = self::$soldo->getExpenseCentres();
        $this->assertInternalType('array', $expenseCentres);
        $this->assertTrue(count($expenseCentres) > 0, 'There should be at least one Expense Centre');

        /** @var ExpenseCentre $expenseCentre*/
        $expenseCentre = $expenseCentres[0];
        $this->assertInternalType('string', $expenseCentre->id);
        self::$expenseCentreId = $expenseCentre->id;

        foreach ($expenseCentres as $expenseCentre) {
            /** @var ExpenseCentre $expenseCentre*/
            $this->assertInstanceOf(ExpenseCentre::class, $expenseCentre);
        }
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoModelNotFoundException
     */
    public function testGetExpenseCentreNotFound()
    {
        $expenseCentre = self::$soldo->getExpenseCentre('A_NOT_EXISTING_EXPENSE_CENTRE_ID');
    }

    public function testGetExpenseCentre()
    {
        $expenseCentre = self::$soldo->getExpenseCentre(self::$expenseCentreId);
        $this->assertInstanceOf(ExpenseCentre::class, $expenseCentre);
        $this->assertEquals(self::$expenseCentreId, $expenseCentre->id);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInternalServerErrorException
     */
    public function testUpdateExpenseCentreEmptyData()
    {
        $expenseCentre = self::$soldo->updateExpenseCentre(self::$expenseCentreId, []);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInternalServerErrorException
     */
    public function testUpdateExpenseCentreBlacklistedData()
    {
        $expenseCentre = self::$soldo->updateExpenseCentre(self::$expenseCentreId, ['a_not_whitelisted_key' => 'Random Value']);
    }

    public function testUpdateExpenseCentre()
    {
        $expenseCentre = self::$soldo->updateExpenseCentre(self::$expenseCentreId, ['assignee' => 'Random Assignee']);
        $this->assertInstanceOf(ExpenseCentre::class, $expenseCentre);
        $this->assertEquals('Random Assignee', $expenseCentre->assignee);
    }

}
