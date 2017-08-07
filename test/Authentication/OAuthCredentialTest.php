<?php

namespace Soldo\Tests\Authentication;

use PHPUnit\Framework\TestCase;
use Soldo\Authentication\OAuthCredential;

/**
 * Class OAuthCredentialTest
 */
class OAuthCredentialTest extends TestCase
{
    /** @var OAuthCredential */
    private static $credential;

    public static function setUpBeforeClass()
    {
        $credential = new OAuthCredential(
            'client_id',
            'client_secret'
        );
        self::$credential = $credential;
    }

    /**
     * @return array
     */
    private function getAuthenticationData()
    {
        $data = [
            'access_token' => 'randomaccesstoken',
            'refresh_token' => 'randomrefreshtoken',
            'token_type' => 'bearer',
            'expires_in' => 7200
        ];
        return $data;
    }


    public function testIsTokenExpired()
    {
        $this->assertFalse(self::$credential->isTokenExpired());
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateAuthenticationDataMissingAccessToken()
    {
        $ad = $this->getAuthenticationData();
        unset($ad['access_token']);
        self::$credential->updateAuthenticationData($ad);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateAuthenticationDataEmptyAccessToken()
    {
        $ad = $this->getAuthenticationData();
        $ad['access_token'] = null;
        self::$credential->updateAuthenticationData($ad);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateAuthenticationDataMissingRefreshToken()
    {
        $ad = $this->getAuthenticationData();
        unset($ad['refresh_token']);
        self::$credential->updateAuthenticationData($ad);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateAuthenticationDataEmptyRefreshToken()
    {
        $ad = $this->getAuthenticationData();
        $ad['refresh_token'] = null;
        self::$credential->updateAuthenticationData($ad);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateAuthenticationDataMissingTokenType()
    {
        $ad = $this->getAuthenticationData();
        unset($ad['token_type']);
        self::$credential->updateAuthenticationData($ad);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateAuthenticationDataEmptyTokenType()
    {
        $ad = $this->getAuthenticationData();
        $ad['token_type'] = null;
        self::$credential->updateAuthenticationData($ad);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateAuthenticationDataMissingExpiresIn()
    {
        $ad = $this->getAuthenticationData();
        unset($ad['expires_in']);
        self::$credential->updateAuthenticationData($ad);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateAuthenticationDataEmptyExpiresIn()
    {
        $ad = $this->getAuthenticationData();
        $ad['expires_in'] = null;
        self::$credential->updateAuthenticationData($ad);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateAuthenticationDataInvalidExpiresIn()
    {
        $ad = $this->getAuthenticationData();
        $ad['expires_in'] = 'FOO';
        self::$credential->updateAuthenticationData($ad);
    }

    public function testUpdateAuthenticationData()
    {
        $ad = $this->getAuthenticationData();
        self::$credential->updateAuthenticationData($ad);

        $this->assertNotNull(self::$credential->access_token);
        $this->assertNotEmpty(self::$credential->access_token);
        $this->assertEquals('randomaccesstoken', self::$credential->access_token);

        $this->assertNotNull(self::$credential->refresh_token);
        $this->assertNotEmpty(self::$credential->refresh_token);
        $this->assertEquals('randomrefreshtoken', self::$credential->refresh_token);

        $this->assertNotNull(self::$credential->token_type);
        $this->assertNotEmpty(self::$credential->token_type);
        $this->assertEquals('bearer', self::$credential->token_type);

        $this->assertNotNull(self::$credential->expires_in);
        $this->assertNotEmpty(self::$credential->expires_in);
        $this->assertInternalType('integer', self::$credential->expires_in);
        $this->assertEquals(7200, self::$credential->expires_in);
    }






}
