<?php

namespace Soldo\Tests\Authentication;

use PHPUnit\Framework\TestCase;
use Soldo\Authentication\OAuthCredential;
use Soldo\Exceptions\SoldoAuthenticationException;

/**
 * Class OAuthCredentialTest
 */
class OAuthCredentialTest extends TestCase
{
    /** @var OAuthCredential */
    private $credential;

    protected function setUp(): void
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

    public function testUpdateAuthenticationDataMissingAccessToken()
    {
        $ad = $this->getAuthenticationData();
        unset($ad['access_token']);

        $this->expectException(SoldoAuthenticationException::class);
        $this->credential->updateAuthenticationData($ad);
    }

    public function testUpdateAuthenticationDataEmptyAccessToken()
    {
        $ad = $this->getAuthenticationData();
        $ad['access_token'] = null;

        $this->expectException(SoldoAuthenticationException::class);
        $this->credential->updateAuthenticationData($ad);

        $this->expectException(SoldoAuthenticationException::class);
        $ad['access_token'] = '';
        $this->credential->updateAuthenticationData($ad);
    }

    public function testUpdateAuthenticationDataMissingRefreshToken()
    {
        $ad = $this->getAuthenticationData();
        unset($ad['refresh_token']);

        $this->expectException(SoldoAuthenticationException::class);
        $this->credential->updateAuthenticationData($ad);
    }

    public function testUpdateAuthenticationDataEmptyRefreshToken()
    {
        $ad = $this->getAuthenticationData();
        $ad['refresh_token'] = null;

        $this->expectException(SoldoAuthenticationException::class);
        $this->credential->updateAuthenticationData($ad);

        $ad['refresh_token'] = '';

        $this->expectException(SoldoAuthenticationException::class);
        $this->credential->updateAuthenticationData($ad);
    }

    public function testUpdateAuthenticationDataMissingTokenType()
    {
        $ad = $this->getAuthenticationData();
        unset($ad['token_type']);

        $this->expectException(SoldoAuthenticationException::class);
        $this->credential->updateAuthenticationData($ad);
    }

    public function testUpdateAuthenticationDataEmptyTokenType()
    {
        $ad = $this->getAuthenticationData();
        $ad['token_type'] = null;

        $this->expectException(SoldoAuthenticationException::class);
        $this->credential->updateAuthenticationData($ad);

        $ad['token_type'] = '';

        $this->expectException(SoldoAuthenticationException::class);
        $this->credential->updateAuthenticationData($ad);
    }

    public function testUpdateAuthenticationDataMissingExpiresIn()
    {
        $ad = $this->getAuthenticationData();
        unset($ad['expires_in']);

        $this->expectException(SoldoAuthenticationException::class);
        $this->credential->updateAuthenticationData($ad);
    }

    public function testUpdateAuthenticationDataEmptyExpiresIn()
    {
        $ad = $this->getAuthenticationData();
        $ad['expires_in'] = null;

        $this->expectException(SoldoAuthenticationException::class);
        $this->credential->updateAuthenticationData($ad);

        $ad['expires_in'] = '';

        $this->expectException(SoldoAuthenticationException::class);
        $this->credential->updateAuthenticationData($ad);
    }

    public function testUpdateAuthenticationDataInvalidExpiresIn()
    {
        $ad = $this->getAuthenticationData();
        $ad['expires_in'] = 'FOO';

        $this->expectException(SoldoAuthenticationException::class);
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
        $this->assertIsInt($this->credential->expires_in);
        $this->assertEquals(7200, $this->credential->expires_in);

        $this->assertFalse($this->credential->isTokenExpired());
    }

    public function testIsTokenExpired()
    {
        $ad = $this->getAuthenticationData();
        $ad['expires_in'] = OAuthCredential::EXPIRY_BUFFER_TIME + 1;
        $this->credential->updateAuthenticationData($ad);
        $this->assertFalse($this->credential->isTokenExpired());

        $ad['expires_in'] = OAuthCredential::EXPIRY_BUFFER_TIME;
        $this->credential->updateAuthenticationData($ad);
        $this->assertFalse($this->credential->isTokenExpired());

        $ad['expires_in'] = OAuthCredential::EXPIRY_BUFFER_TIME - 1;
        $this->credential->updateAuthenticationData($ad);
        $this->assertTrue($this->credential->isTokenExpired());
    }
}
