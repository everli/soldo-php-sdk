<?php

namespace Soldo\Authentication;

use Soldo\Resources\SoldoResource;

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
class OAuthCredential extends SoldoResource
{

    /**
     * OAuthCredential constructor.
     * @param $client_id
     * @param $client_secret
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







}
