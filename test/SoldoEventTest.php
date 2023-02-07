<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Exceptions\SoldoInvalidEvent;
use Soldo\Exceptions\SoldoInvalidFingerprintException;
use Soldo\Resources\Transaction;
use Soldo\SoldoEvent;
use Soldo\Tests\SoldoTestCredentials;

/**
 * Class SoldoEventTest
 */
class SoldoEventTest extends TestCase
{
    private const INTERNAL_TOKEN_TEST = 'CNGZ0YEB2ZPFBRHMA';

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

    public function setUp(): void
    {
        $data = $this->getEventData()['data'];

        $this->expectedFingerprint = hash('sha512', implode('', [
            $data['id'],
            $data['wallet_id'],
            $data['status'],
            $data['transaction_sign'],
            self::INTERNAL_TOKEN_TEST,
        ]));

        parent::setUp();
    }

    public function testConstructorMissingEventType()
    {
        $this->expectException(SoldoInvalidEvent::class);

        $data = $this->getEventData();
        unset($data['event_type']);
        new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            $this->expectedFingerprintOrder,
            self::INTERNAL_TOKEN_TEST
        );
    }

    public function testConstructorMissingEventName()
    {
        $this->expectException(SoldoInvalidEvent::class);

        $data = $this->getEventData();
        unset($data['event_name']);
        $e = new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            $this->expectedFingerprintOrder,
            self::INTERNAL_TOKEN_TEST
        );
    }


    public function testConstructorMissingData()
    {
        $this->expectException(SoldoInvalidEvent::class);

        $data = $this->getEventData();
        unset($data['data']);
        $e = new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            $this->expectedFingerprintOrder,
            self::INTERNAL_TOKEN_TEST
        );
    }

    public function testConstructorInvalidData()
    {
        $this->expectException(SoldoInvalidEvent::class);

        $data = $this->getEventData();
        $data['data'] = 'string';
        $e = new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            $this->expectedFingerprintOrder,
            self::INTERNAL_TOKEN_TEST
        );
    }

    public function testConstructorInvalidEventType()
    {
        $this->expectException(SoldoInvalidEvent::class);

        $data = $this->getEventData();
        $data['event_type'] = 'ResourceNotSupported';
        $e = new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            $this->expectedFingerprintOrder,
            self::INTERNAL_TOKEN_TEST
        );
    }

    public function testConstructorInvalidFingerPrintOrder()
    {
        $this->expectException(SoldoInvalidFingerprintException::class);

        $data = $this->getEventData();
        $e = new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            '',
            self::INTERNAL_TOKEN_TEST
        );
    }

    public function testConstructorInvalidFingerPrintOrderMissingToken()
    {
        $this->expectException(SoldoInvalidFingerprintException::class);

        $data = $this->getEventData();
        $e = new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            'id,wallet_id,status,transaction_sign',
            self::INTERNAL_TOKEN_TEST
        );
    }

    public function testConstructorInvalidFingerPrint()
    {
        $this->expectException(SoldoInvalidEvent::class);

        $data = $this->getEventData();
        $e = new SoldoEvent(
            $data,
            'invalid-fingerprint',
            $this->expectedFingerprintOrder,
            '123'
        );
    }

    public function testConstructorInvalidInternalToken()
    {
        $this->expectException(SoldoInvalidEvent::class);

        $data = $this->getEventData();
        $e = new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            $this->expectedFingerprintOrder,
            '1230'
        );
    }

    public function testGet()
    {
        $data = $this->getEventData();
        $e = new SoldoEvent(
            $data,
            $this->expectedFingerprint,
            $this->expectedFingerprintOrder,
            self::INTERNAL_TOKEN_TEST
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
            self::INTERNAL_TOKEN_TEST
        );
        $this->assertEquals('transaction.refund_settled', $e->type());
    }
}
