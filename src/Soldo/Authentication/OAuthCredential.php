<?php

namespace Soldo\Authentication;

use Soldo\Exceptions\SoldoAuthenticationException;
use Soldo\Resources\SoldoResource;
use Respect\Validation\Validator;

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
final class OAuthCredential extends SoldoResource
{

    /**
     * OAuthCredential constructor.
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct($clientId, $clientSecret)
    {
        parent::__construct();

        $this->client_id = $clientId;
        $this->client_secret = $clientSecret;
    }

    /**
     * Return true if token was generated before expires_in seconds
     * Add a buffer just to not risk
     *
     * TODO: ask Soldo how to generate a new token starting from refresh_token
     *
     * @return bool
     */
    public function isTokenExpired()
    {
        return false;
    }

    /**
     * @param $authData
     */
    public function updateAuthenticationData($authData)
    {
        $this->validateAuthData($authData);

        $this->access_token = $authData['access_token'];
        $this->refresh_token = $authData['refresh_token'];
        $this->token_type = $authData['token_type'];
        $this->expires_in = $authData['expires_in'];
    }

    /**
     * Manually validate data since this is a crucial point.
     *
     * @param $authData
     * @throws SoldoAuthenticationException
     * @return bool
     */
    private function validateAuthData($authData)
    {
        $validator = Validator::key('access_token', Validator::notEmpty())
            ->key('refresh_token', Validator::notEmpty())
            ->key('token_type', Validator::notEmpty())
            ->key('expires_in', Validator::intVal());

        // TODO: verify exception type and messages
        if ($validator->validate($authData) === false) {
            throw new SoldoAuthenticationException(
                'Unable to authenticate user. '
                . 'Check your credential'
            );
        }

        return true;
    }
}
