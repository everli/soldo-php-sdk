<?php

namespace Soldo\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Soldo\Authentication\OAuthCredential;
use Soldo\Exceptions\SoldoAuthenticationException;
use Soldo\Exceptions\SoldoInternalTransferException;
use Soldo\Exceptions\SoldoInvalidRelationshipException;
use Soldo\Exceptions\SoldoInvalidResourceException;
use Soldo\Exceptions\SoldoModelNotFoundException;
use Soldo\Resources\Collection;
use Soldo\Resources\Employee;
use Soldo\Resources\Card;
use Soldo\Resources\Rule;
use Soldo\SoldoClient;

/**
 * Class SoldoClientTest
 */
class SoldoClientTest extends TestCase
{
    /** @var SoldoClient */
    private $soldoClient;

    public function setUp(): void
    {
        // Demo credentials
        $credential = new OAuthCredential(
            getenv('CLIENT_ID'),
            getenv('CLIENT_SECRET')
        );
        $environment = 'demo';
        $this->soldoClient = new SoldoClient($credential, $environment);
    }

    /**
     * @return SoldoClient
     */
    private function getClientWithInvalidCredentials()
    {
        return new SoldoClient(
            new OAuthCredential(
                'wrong_client_id',
                'wrong_client_secret'
            ),
            'demo'
        );
    }

    /**
     * @return mixed
     */
    private function getItemId()
    {
        $collection = $this->soldoClient->getCollection(Employee::class);

        return $collection->get()[0]->id;
    }

    public function testGetAccessTokenInvalidCredentials()
    {
        $this->expectException(SoldoAuthenticationException::class);

        $sc = $this->getClientWithInvalidCredentials();
        $sc->getAccessToken();
    }

    public function testGetAccessToken()
    {
        $access_token = $this->soldoClient->getAccessToken();
        $this->assertNotNull($access_token);
        $this->assertIsString('string', $access_token);
    }

    public function testGetCollectionInvalidCredentials()
    {
        $this->expectException(SoldoAuthenticationException::class);

        $sc = $this->getClientWithInvalidCredentials();
        $sc->getCollection(Employee::class);
    }

    public function testGetCollectionInvalidClass()
    {
        $this->expectException(SoldoInvalidResourceException::class);

        $this->soldoClient->getCollection('INVALID_CLASS_NAME');
    }

    public function testGetCollection()
    {
        $collection = $this->soldoClient->getCollection(Employee::class);
        $this->assertInstanceOf(Collection::class, $collection);
        foreach ($collection->get() as $item) {
            $this->assertInstanceOf(Employee::class, $item);
        }
    }


    public function testGetItemInvalidCredentials()
    {
        $this->expectException(SoldoAuthenticationException::class);

        $sc = $this->getClientWithInvalidCredentials();
        $sc->getItem(Employee::class, 'a-valid-id');
    }


    public function testGetItemInvalidClass()
    {
        $this->expectException(SoldoInvalidResourceException::class);

        $this->soldoClient->getItem('INVALID_CLASS_NAME');
    }

    public function testGetItemNotFound()
    {
        $this->expectException(SoldoModelNotFoundException::class);

        $this->soldoClient->getItem(Employee::class, 'NOT_EXISTING_ID');
    }

    public function testGetItem()
    {
        $itemId = $this->getItemId();
        $item = $this->soldoClient->getItem(Employee::class, $itemId);
        $this->assertNotNull($item);
        $this->assertInstanceOf(Employee::class, $item);
    }

    public function testUpdateItemInvalidCredentials()
    {
        $this->expectException(SoldoAuthenticationException::class);

        $itemId = $this->getItemId();
        $sc = $this->getClientWithInvalidCredentials();
        $sc->updateItem(Employee::class, $itemId, ['department' => 'A Depertament']);
    }

    public function testUpdateItemInvalidClass()
    {
        $this->expectException(SoldoInvalidResourceException::class);

        $itemId = $this->getItemId();
        $this->soldoClient->updateItem('INVALID_CLASS_NAME', $itemId, ['department' => 'A Depertament']);
    }

    public function testUpdateItemNotFound()
    {
        $this->expectException(SoldoModelNotFoundException::class);
        $this->soldoClient->updateItem(Employee::class, 'A_NOT_EXISTING_ID', ['department' => 'A Depertament']);
    }

    public function testUpdateItemEmptyData()
    {
        $this->expectException(InvalidArgumentException::class);

        $itemId = $this->getItemId();
        $this->soldoClient->updateItem(Employee::class, $itemId, []);
    }

    public function testUpdateItemNotWhitelisted()
    {
        $this->expectException(InvalidArgumentException::class);

        $itemId = $this->getItemId();
        $this->soldoClient->updateItem(Employee::class, $itemId, ['random_key' => 'Random Value']);
    }

    public function testUpdateItem()
    {
        $itemId = $this->getItemId();
        /** @var Employee $item */
        $item = $this->soldoClient->updateItem(Employee::class, $itemId, ['department' => 'A Department']);
        $this->assertInstanceOf(Employee::class, $item);
        $this->assertEquals($itemId, $item->id);
        $this->assertEquals('A Department', $item->department);
    }


    public function testGetRelationshipInvalidCredentials()
    {
        $this->expectException(SoldoAuthenticationException::class);

        $sc = $this->getClientWithInvalidCredentials();
        $sc->getRelationship(Card::class, 'fake-id', 'rules');
    }

    public function testGetRelationshipInvalidClass()
    {
        $this->expectException(SoldoInvalidResourceException::class);

        $this->soldoClient->getRelationship('INVALID_CLASS_NAME', 'fake-id', 'rules');
    }

    public function testGetRelationshipInvalidRelationship()
    {
        $this->expectException(SoldoInvalidRelationshipException::class);

        $this->soldoClient->getRelationship(Card::class, 'fake-id', 'not-mapped-relationship');
    }

    public function testGetRelationshipNotFound()
    {
        $this->expectException(SoldoModelNotFoundException::class);
        $this->soldoClient->getRelationship(Card::class, 'fake-id', 'rules');
    }

    public function testGetRelationship()
    {
        $cards = $this->soldoClient->getCollection(Card::class);
        $card_id = $cards->get()[0]->id;

        $relationship = $this->soldoClient->getRelationship(Card::class, $card_id, 'rules');
        $this->assertIsArray($relationship);
        foreach ($relationship as $r) {
            $this->assertInstanceOf(Rule::class, $r);
        }
    }

    public function testPerformTransferInvalidCredentials()
    {
        $this->expectException(SoldoAuthenticationException::class);

        $sc = $this->getClientWithInvalidCredentials();
        $sc->performTransfer('from-wallet', 'to-wallet', 50, 'EUR', '123456');
    }

    public function testPerformTransferInvalidParams()
    {
        $this->expectException(SoldoInternalTransferException::class);

        $this->soldoClient->performTransfer('from-wallet', 'to-wallet', 50, 'EUR', '123456');
    }
}
