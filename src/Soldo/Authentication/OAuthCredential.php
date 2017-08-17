<?php

namespace Soldo\Authentication;

use Soldo\Exceptions\SoldoAuthenticationException;
use Soldo\Resources\Resource;
use Soldo\Validators\ValidatorTrait;

/**
 * Class OAuthCredential
 * @package Soldo\Authentication
 *
 * @property string client_id
 * @property string client_secret
 * @property string access_token
 * @property string refresh_token
 * @property string token_type
 * @property int expires_in
 */
class OAuthCredential extends Resource
{
    use ValidatorTrait;

    /**
     * OAuthCredential constructor.
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct($clientId, $clientSecret)
    {
        parent::__construct();

        // Using snake_case here to remain consistent with other resource attributes
        // All attributes name are keep equals to those retrieved by the Soldo API
        $this->client_id = $clientId;
        $this->client_secret = $clientSecret;
    }

    /**
     * Return true if token was generated before expires_in seconds
     * Add a buffer just to not risk
     *
     * @return bool
     */
    public function isTokenExpired()
    {
        return false;
    }

    /**
     * Validate raw data end fill resources with array provided
     *
     * @param array $data
     * @throws SoldoAuthenticationException
     */
    public function updateAuthenticationData($data)
    {
        $rules = [
            'access_token' => 'required',
            'refresh_token' => 'required',
            'token_type' => 'required',
            'expires_in' => 'integer',
        ];

        if (!$this->validateRawData($data, $rules)) {
            throw new SoldoAuthenticationException(
                'Unable to authenticate user'
            );
        }

        $this->fill($data);
    }

}
