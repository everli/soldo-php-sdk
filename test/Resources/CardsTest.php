<?php

namespace Soldo\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Soldo\Resources\Card;
use Soldo\Resources\Cards;
use Soldo\Tests\Fixtures\MockCollection;
use Soldo\Tests\Fixtures\MockResource;


/**
 * Class CardsTest
 */
class CardsTest extends TestCase
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
                        'id' => '47a09081-096a-11e7-9088-0a3392c1c947',
                        'name' => 'Main Card',
                        'masked_pan' => '999999******2662',
                        'card_holder' => 'Blake D Ferguson',
                        'expiration_date' => '2019-10-31T23:59:59Z',
                        'type' => 'PLASTIC',
                        'status' => 'Cardholder to contact the issuer',
                        'owner_type' => 'employee',
                        'owner_public_id' => '12621231',
                        'wallet_id' => '585cce33-096a-11e7-9088-0a3392c1c947',
                        'currency_code' => 'EUR',
                        'emboss_line4' => 'EUR',
                        'active' => true,
                    ],
                    [
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
                    ],
                ],
            ];
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidCollectionException
     */
    public function testFillInvalidData()
    {
        $collection = new Cards();
        $collection->fill([]);
    }

    public function testFill()
    {
        $collectionData = $this->getCollectionData();
        $collection = new Cards();
        $items = $collection->fill($collectionData)->get();

        foreach ($items as $key => $item) {
            /** @var  $item  Card */
            $this->assertInstanceOf(Card::class, $item);
            $this->assertEquals($collectionData['results'][$key], $item->toArray());
        }
    }

    public function testGetRemotePath()
    {
        $collection = new Cards();
        $this->assertEquals('/cards', $collection->getRemotePath());
    }


}
