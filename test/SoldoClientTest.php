<?php

use PHPUnit\Framework\TestCase;
use Soldo\Authentication\OAuthCredential;
use Soldo\SoldoClient;
use Soldo\Tests\SoldoTestCredentials;

/**
 * Class SoldoClientTest
 */
class SoldoClientTest extends TestCase
{
    /** @var SoldoClient */
    private static $soldoClient;

    /** @var string */
    private static $itemId;

    public static function setUpBeforeClass()
    {
        $credential = new OAuthCredential(
            SoldoTestCredentials::CLIENT_ID,
            SoldoTestCredentials::CLIENT_SECRET
        );
        $environment = 'demo';
        self::$soldoClient = new SoldoClient($credential, $environment);
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
            )
        );
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
        $access_token = self::$soldoClient->getAccessToken();
        $this->assertNotNull($access_token);
        $this->assertInternalType('string', $access_token);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testGetCollectionInvalidCredentials()
    {
        $sc = $this->getClientWithInvalidCredentials();
        $sc->getCollection(\Soldo\Resources\Employee::class);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetCollectionInvalidClass()
    {
        self::$soldoClient->getCollection('INVALID_CLASS_NAME');
    }


    public function testGetCollection()
    {
        $collection = self::$soldoClient->getCollection(\Soldo\Resources\Employees::class);
        $this->assertInstanceOf(\Soldo\Resources\Employees::class, $collection);
        foreach ($collection->get() as $item) {
            $this->assertInstanceOf(\Soldo\Resources\Employee::class, $item);
        }
        self::$itemId = $collection->get()[0]->id;
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testGetItemInvalidCredentials()
    {
        $sc = $this->getClientWithInvalidCredentials();
        $item = $sc->getItem(\Soldo\Resources\Employee::class);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetItemInvalidClass()
    {
        $item = self::$soldoClient->getItem('INVALID_CLASS_NAME');
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoModelNotFoundException
     */
    public function testGetItemNotFound()
    {
        $item = self::$soldoClient->getItem(\Soldo\Resources\Employee::class, 'NOT_EXISTING_ID');
    }

    public function testGetItem()
    {
        $item = self::$soldoClient->getItem(\Soldo\Resources\Employee::class, self::$itemId);
        $this->assertNotNull($item);
        $this->assertInstanceOf(\Soldo\Resources\Employee::class, $item);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateItemInvalidCredentials()
    {
        $sc = $this->getClientWithInvalidCredentials();
        $item = $sc->updateItem(\Soldo\Resources\Employee::class, self::$itemId, ['department' => 'A Depertament']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUpdateItemInvalidClass()
    {
        $item = self::$soldoClient->updateItem('INVALID_CLASS_NAME', self::$itemId, ['department' => 'A Depertament']);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoModelNotFoundException
     */
    public function testUpdateItemNotFound()
    {
        $item = self::$soldoClient->updateItem(\Soldo\Resources\Employee::class, 'A_NOT_EXISTING_ID', ['department' => 'A Depertament']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUpdateItemEmptyData()
    {
        $item = self::$soldoClient->updateItem(\Soldo\Resources\Employee::class,  self::$itemId, []);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUpdateItemNotWhitelisted()
    {
        $item = self::$soldoClient->updateItem(\Soldo\Resources\Employee::class,  self::$itemId, ['random_key' => 'Random Value']);
    }

    public function testUpdateItem()
    {
        $item = self::$soldoClient->updateItem(\Soldo\Resources\Employee::class, self::$itemId, ['department' => 'A Department']);
        $this->assertInstanceOf(\Soldo\Resources\Employee::class, $item);
        $this->assertEquals(self::$itemId, $item->id);
        $this->assertEquals('A Department', $item->department);
    }


    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testGetRelationshipInvalidCredentials()
    {
        $sc = $this->getClientWithInvalidCredentials();
        $relationship = $sc->getRelationship(\Soldo\Resources\Card::class, 'fake-id', 'rules');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetRelationshipInvalidClass()
    {
        $relationship = self::$soldoClient->getRelationship('INVALID_CLASS_NAME', 'fake-id', 'rules');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetRelationshipInvalidRelationship()
    {
        $relationship = self::$soldoClient->getRelationship(\Soldo\Resources\Card::class, 'fake-id', 'not-mapped-relationship');
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoModelNotFoundException
     */
    public function testGetRelationshipNotFound()
    {
        $relationship = self::$soldoClient->getRelationship(\Soldo\Resources\Card::class, 'fake-id', 'rules');
    }


    public function testGetRelationship()
    {
        $cards = self::$soldoClient->getCollection(\Soldo\Resources\Cards::class);
        $card_id = $cards->get()[0]->id;

        $relationship = self::$soldoClient->getRelationship(\Soldo\Resources\Card::class, $card_id, 'rules');
        $this->assertInternalType('array', $relationship);
        foreach ($relationship as $r) {
            $this->assertInstanceOf(\Soldo\Resources\Rule::class, $r);
        }
    }





}
