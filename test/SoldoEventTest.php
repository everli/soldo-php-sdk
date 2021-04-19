<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Resources\Transaction;
use Soldo\SoldoEvent;
use Soldo\Tests\SoldoTestCredentials;

/**
 * Class SoldoEventTest
 */
class SoldoEventTest extends TestCase
{

    /**
     * @return array
     */
    private function getEventData()
    {
        $data = [
            'event_type' => 'Transaction',
            'event_name' => 'card_authorization',
            'data' =>
                [
                    'id' => '1309-189704842-1503070696138',
                    'wallet_id' => 'f086f47f-1526-11e7-9287-0a89c8769141',
                    'status' => 'Settled',
                    'category' => 'Refund',
                    'transaction_sign' => 'Positive',
                    'amount' => 56,
                    'amount_currency' => 'EUR',
                    'tx_amount' => 50,
                    'tx_amount_currency' => 'GBP',
                    'fee_amount' => 0,
                    'fee_currency' => 'EUR',
                    'auth_exchange_rate' => 1,
                    'date' => '2017-08-18T14:36:00',
                    'settlement_date' => '2017-08-18T14:36:31Z',
                    'merchant' =>
                        [
                            'name' => 'PRET A MANGER LONDON GBR',
                            'raw_name' => 'PRET A MANGER LONDON GBR',
                        ],
                    'merchant_category' =>
                        [
                            'mcc' => '5812',
                        ],
                    'tags' =>
                        [],
                    'card_id' => 'f275d49c-1526-11e7-9287-0a89c8769141',
                    'masked_pan' => '999999******3706',
                    'owner_id' => 'SDMD7784-000002',
                    'custom_reference_id' => 'sdfgsfgsdfg',
                    'owner_type' => 'company',
                ]
        ];
        return $data;
    }

    /**
     * @var string
     */
    private $expectedFingerprintOrder = 'id,wallet_id,status,transaction_sign,token';

    /**
     * @var string
     */
    private $expectedFingerprint;

    public function setUp()
    {
        $data = $this->getEventData()['data'];

        $this->expectedFingerprint = hash('sha512', implode('', [
            $data['id'],
            $data['wallet_id'],
            $data['status'],
            $data['transaction_sign'],
            SoldoTestCredentials::INTERNAL_TOKEN,
        ]));

        parent::setUp();
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidEvent
     */
    public function testConstructorMissingEventType()
    {
        $data = $this->getEventData();
        unset($data['event_type']);
        $e = new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            $this->expectedFingerprintOrder,
            SoldoTestCredentials::INTERNAL_TOKEN
        );
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidEvent
     */
    public function testConstructorMissingEventName()
    {
        $data = $this->getEventData();
        unset($data['event_name']);
        $e = new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            $this->expectedFingerprintOrder,
            SoldoTestCredentials::INTERNAL_TOKEN
        );
    }


    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidEvent
     */
    public function testConstructorMissingData()
    {
        $data = $this->getEventData();
        unset($data['data']);
        $e = new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            $this->expectedFingerprintOrder,
            SoldoTestCredentials::INTERNAL_TOKEN
        );
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidEvent
     */
    public function testConstructorInvalidData()
    {
        $data = $this->getEventData();
        $data['data'] = 'string';
        $e = new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            $this->expectedFingerprintOrder,
            SoldoTestCredentials::INTERNAL_TOKEN
        );
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidEvent
     */
    public function testConstructorInvalidEventType()
    {
        $data = $this->getEventData();
        $data['event_type'] = 'ResourceNotSupported';
        $e = new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            $this->expectedFingerprintOrder,
            SoldoTestCredentials::INTERNAL_TOKEN
        );
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidFingerprintException
     */
    public function testConstructorInvalidFingerPrintOrder()
    {
        $data = $this->getEventData();
        $e = new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            '',
            SoldoTestCredentials::INTERNAL_TOKEN
        );
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidFingerprintException
     */
    public function testConstructorInvalidFingerPrintOrderMissingToken()
    {
        $data = $this->getEventData();
        $e = new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            'id,wallet_id,status,transaction_sign',
            SoldoTestCredentials::INTERNAL_TOKEN
        );
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidEvent
     */
    public function testConstructorInvalidFingerPrint()
    {
        $data = $this->getEventData();
        $e = new SoldoEvent(
            $data,
            'invalid-fingerprint',
            $this->expectedFingerprintOrder,
            SoldoTestCredentials::INTERNAL_TOKEN
        );
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidEvent
     */
    public function testConstructorInvalidInternalToken()
    {
        $data = $this->getEventData();
        $e = new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            $this->expectedFingerprintOrder,
            SoldoTestCredentials::INTERNAL_TOKEN . '0' //append a char to invalidate token
        );
    }

    public function testGet()
    {
        $data = $this->getEventData();
        $e = new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            $this->expectedFingerprintOrder,
            SoldoTestCredentials::INTERNAL_TOKEN
        );
        $resource = $e->get();
        $this->assertInstanceOf(Transaction::class, $resource);
        $this->assertEquals($data['data'], $resource->toArray());
    }

    public function testType()
    {
        $data = $this->getEventData();
        $e = new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            $this->expectedFingerprintOrder,
            SoldoTestCredentials::INTERNAL_TOKEN
        );
        $this->assertEquals('transaction.refund_settled', $e->type());
    }
}
