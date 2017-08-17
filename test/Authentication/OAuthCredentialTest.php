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
    private $credential;

    protected function setUp()
    {
        $this->credential = new OAuthCredential('client_id', 'client_secret');
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
            'expires_in' => 7200,
        ];

        return $data;
    }

    public function testIsTokenExpired()
    {
        $this->assertFalse($this->credential->isTokenExpired());
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateAuthenticationDataMissingAccessToken()
    {
        $ad = $this->getAuthenticationData();
        unset($ad['access_token']);
        $this->credential->updateAuthenticationData($ad);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateAuthenticationDataEmptyAccessToken()
    {
        $ad = $this->getAuthenticationData();
        $ad['access_token'] = null;
        $this->credential->updateAuthenticationData($ad);

        $ad['access_token'] = '';
        $this->credential->updateAuthenticationData($ad);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateAuthenticationDataMissingRefreshToken()
    {
        $ad = $this->getAuthenticationData();
        unset($ad['refresh_token']);
        $this->credential->updateAuthenticationData($ad);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateAuthenticationDataEmptyRefreshToken()
    {
        $ad = $this->getAuthenticationData();
        $ad['refresh_token'] = null;
        $this->credential->updateAuthenticationData($ad);

        $ad['refresh_token'] = '';
        $this->credential->updateAuthenticationData($ad);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateAuthenticationDataMissingTokenType()
    {
        $ad = $this->getAuthenticationData();
        unset($ad['token_type']);
        $this->credential->updateAuthenticationData($ad);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateAuthenticationDataEmptyTokenType()
    {
        $ad = $this->getAuthenticationData();
        $ad['token_type'] = null;
        $this->credential->updateAuthenticationData($ad);

        $ad['token_type'] = '';
        $this->credential->updateAuthenticationData($ad);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateAuthenticationDataMissingExpiresIn()
    {
        $ad = $this->getAuthenticationData();
        unset($ad['expires_in']);
        $this->credential->updateAuthenticationData($ad);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateAuthenticationDataEmptyExpiresIn()
    {
        $ad = $this->getAuthenticationData();
        $ad['expires_in'] = null;
        $this->credential->updateAuthenticationData($ad);

        $ad['expires_in'] = '';
        $this->credential->updateAuthenticationData($ad);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoAuthenticationException
     */
    public function testUpdateAuthenticationDataInvalidExpiresIn()
    {
        $ad = $this->getAuthenticationData();
        $ad['expires_in'] = 'FOO';
        $this->credential->updateAuthenticationData($ad);
    }

    public function testUpdateAuthenticationData()
    {
        $ad = $this->getAuthenticationData();
        $this->credential->updateAuthenticationData($ad);

        $this->assertNotNull($this->credential->access_token);
        $this->assertNotEmpty($this->credential->access_token);
        $this->assertEquals('randomaccesstoken', $this->credential->access_token);

        $this->assertNotNull($this->credential->refresh_token);
        $this->assertNotEmpty($this->credential->refresh_token);
        $this->assertEquals('randomrefreshtoken', $this->credential->refresh_token);

        $this->assertNotNull($this->credential->token_type);
        $this->assertNotEmpty($this->credential->token_type);
        $this->assertEquals('bearer', $this->credential->token_type);

        $this->assertNotNull($this->credential->expires_in);
        $this->assertNotEmpty($this->credential->expires_in);
        $this->assertInternalType('integer', $this->credential->expires_in);
        $this->assertEquals(7200, $this->credential->expires_in);
    }
}
