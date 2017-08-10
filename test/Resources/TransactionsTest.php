<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Resources\Transaction;
use Soldo\Resources\Transactions;

/**
 * Class TransactionsTest
 */
class TransactionsTest extends TestCase
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
                        'transaction_id' => '420-c7661b73-d801-4228-88bc-a42471b71454',
                        'wallet_id' => '585caa6e-096a-11e7-9088-0a3392c1c947',
                        'status' => 'Authorised',
                        'category' => 'Wiretransfer',
                        'transaction_sign' => 'Negative',
                        'amount' => 10,
                        'amount_currency' => 'EUR',
                        'tx_amount' => 10,
                        'tx_amount_currency' => 'EUR',
                        'date' => '2017-03-08T11:20:15',
                        'settlement_date' => '2017-03-08T11:20:15Z',
                        'merchant_category' => [],
                        'tags' => [],
                        'owner_id' => '2d9b3d6d-6108-4df8-a44e-ceb7ae8f86fa',
                        'owner_type' => 'company',
                    ],
                    [
                        'transaction_id' => '420-aa8f0eb5-ee18-4cd2-b0d1-d826ab58e638',
                        'wallet_id' => '585caa6e-096a-11e7-9088-0a3392c1c947',
                        'status' => 'Authorised',
                        'category' => 'Wiretransfer',
                        'transaction_sign' => 'Negative',
                        'amount' => 12,
                        'amount_currency' => 'EUR',
                        'tx_amount' => 12,
                        'tx_amount_currency' => 'EUR',
                        'date' => '2017-03-08T11:31:45',
                        'settlement_date' => '2017-03-08T11:31:45Z',
                        'merchant_category' => [],
                        'tags' => [],
                        'owner_id' => '2d9b3d6d-6108-4df8-a44e-ceb7ae8f86fa',
                        'owner_type' => 'company',
                    ],
                ],
        ];
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidCollectionException
     */
    public function testFillInvalidData()
    {
        $collection = new Transactions();
        $collection->fill([]);
    }

    public function testFill()
    {
        $collectionData = $this->getCollectionData();
        $collection = new Transactions();
        $items = $collection->fill($collectionData)->get();

        foreach ($items as $key => $item) {
            /** @var  $item  Transaction */
            $this->assertInstanceOf(Transaction::class, $item);
            $this->assertEquals($collectionData['results'][$key], $item->toArray());
        }
    }

    public function testGetRemotePath()
    {
        $collection = new Transactions();
        $this->assertEquals('/transactions', $collection->getRemotePath());
    }
}
