<?php

namespace Soldo\Tests;

use PHPUnit\Framework\TestCase;
use Soldo\Authentication\OAuthCredential;
use Soldo\Exceptions\SoldoInvalidResourceException;
use Soldo\Resources\Collection;
use Soldo\Resources\Employee;
use Soldo\Resources\Card;
use Soldo\SoldoClient;

/**
 * Class SoldoClientTest
 */
class SoldoClientTest extends TestCase
{
    /** @var SoldoClient */
    private $soldoClient;

    public function setUp()
    {
        $credential = new OAuthCredential(
            SoldoTestCredentials::CLIENT_ID,
            SoldoTestCredentials::CLIENT_SECRET
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
                'client_id',
                'client_secret'
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

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testGetAccessTokenInvalidCredentials()
    {
        $sc = $this->getClientWithInvalidCredentials();
        $access_token = $sc->getAccessToken();
    }

    public function testGetAccessToken()
    {
        $access_token = $this->soldoClient->getAccessToken();
        $this->assertNotNull($access_token);
        $this->assertInternalType('string', $access_token);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testGetCollectionInvalidCredentials()
    {
        $sc = $this->getClientWithInvalidCredentials();
        $sc->getCollection(Employee::class);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidResourceException
     */
    public function testGetCollectionInvalidClass()
    {
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

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testGetItemInvalidCredentials()
    {
        $sc = $this->getClientWithInvalidCredentials();
        $item = $sc->getItem(Employee::class, 'a-valid-id');
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidResourceException
     */
    public function testGetItemInvalidClass()
    {
        $item = $this->soldoClient->getItem('INVALID_CLASS_NAME');
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoModelNotFoundException
     */
    public function testGetItemNotFound()
    {
        $item = $this->soldoClient->getItem(Employee::class, 'NOT_EXISTING_ID');
    }

    public function testGetItem()
    {
        $itemId = $this->getItemId();
        $item = $this->soldoClient->getItem(Employee::class, $itemId);
        $this->assertNotNull($item);
        $this->assertInstanceOf(Employee::class, $item);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateItemInvalidCredentials()
    {
        $itemId = $this->getItemId();
        $sc = $this->getClientWithInvalidCredentials();
        $item = $sc->updateItem(Employee::class, $itemId, ['department' => 'A Depertament']);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidResourceException
     */
    public function testUpdateItemInvalidClass()
    {
        $itemId = $this->getItemId();
        $item = $this->soldoClient->updateItem('INVALID_CLASS_NAME', $itemId, ['department' => 'A Depertament']);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoModelNotFoundException
     */
    public function testUpdateItemNotFound()
    {
        $item = $this->soldoClient->updateItem(Employee::class, 'A_NOT_EXISTING_ID', ['department' => 'A Depertament']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateItemEmptyData()
    {
        $itemId = $this->getItemId();
        $item = $this->soldoClient->updateItem(Employee::class, $itemId, []);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateItemNotWhitelisted()
    {
        $itemId = $this->getItemId();
        $item = $this->soldoClient->updateItem(Employee::class, $itemId, ['random_key' => 'Random Value']);
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

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testGetRelationshipInvalidCredentials()
    {
        $sc = $this->getClientWithInvalidCredentials();
        $relationship = $sc->getRelationship(Card::class, 'fake-id', 'rules');
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidResourceException
     */
    public function testGetRelationshipInvalidClass()
    {
        $relationship = $this->soldoClient->getRelationship('INVALID_CLASS_NAME', 'fake-id', 'rules');
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInvalidRelationshipException
     */
    public function testGetRelationshipInvalidRelationship()
    {
        $relationship = $this->soldoClient->getRelationship(Card::class, 'fake-id', 'not-mapped-relationship');
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoModelNotFoundException
     */
    public function testGetRelationshipNotFound()
    {
        $relationship = $this->soldoClient->getRelationship(Card::class, 'fake-id', 'rules');
    }

    public function testGetRelationship()
    {
        $cards = $this->soldoClient->getCollection(Card::class);
        $card_id = $cards->get()[0]->id;

        $relationship = $this->soldoClient->getRelationship(Card::class, $card_id, 'rules');
        $this->assertInternalType('array', $relationship);
        foreach ($relationship as $r) {
            $this->assertInstanceOf(\Soldo\Resources\Rule::class, $r);
        }
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testPerformTransferInvalidCredentials()
    {
        $sc = $this->getClientWithInvalidCredentials();
        $access_token = $sc->performTransfer('from-wallet', 'to-wallet', 50, 'EUR', '123456');
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInternalTransferException
     *
     * I think this is enough since the real transfer is tested in SoldoTest
     */
    public function testPerformTransferInvalidParams()
    {
        $access_token = $this->soldoClient->performTransfer('from-wallet', 'to-wallet', 50, 'EUR', '123456');
    }
}
