<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Resources\Transaction;
use Soldo\Resources\Transactions;
use Soldo\Resources\Wallet;
use Soldo\Resources\Wallets;

/**
 * Class WalletsTest
 */
class WalletsTest extends TestCase
{
    /**
     * @return array
     */
    protected function getCollectionData()
    {
        return [
            'total' => 2,
            'pages' => 1,
            'page_size' => 25,
            'current_page' => 0,
            'results_size' => 2,
            'results' =>
                [
                    [
                        'id' => '585caa6e-096a-11e7-9088-0a3392c1c947',
                        'name' => 'Wallet1',
                        'currency_code' => 'EUR',
                        'available_amount' => 14782,
                        'blocked_amount' => 24.18,
                        'primary_user_type' => 'company',
                        'visible' => true,
                    ],
                    [
                        'id' => '585ccf5d-096a-11e7-9088-0a3392c1c947',
                        'name' => 'GBP',
                        'currency_code' => 'GBP',
                        'available_amount' => 1165.5999999999999,
                        'blocked_amount' => 0,
                        'primary_user_type' => 'employee',
                        'primary_user_public_id' => '12621231',
                        'visible' => true,
                    ],
                ],
        ];
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidCollectionException
     */
    public function testFillInvalidData()
    {
        $collection = new Wallets();
        $collection->fill([]);
    }

    public function testFill()
    {
        $collectionData = $this->getCollectionData();
        $collection = new Wallets();
        $items = $collection->fill($collectionData)->get();

        foreach ($items as $key => $item) {
            /** @var  $item  Transaction */
            $this->assertInstanceOf(Wallet::class, $item);
            $this->assertEquals($collectionData['results'][$key], $item->toArray());
        }
    }

    public function testGetRemotePath()
    {
        $collection = new Wallets();
        $this->assertEquals('/wallets', $collection->getRemotePath());
    }
}
