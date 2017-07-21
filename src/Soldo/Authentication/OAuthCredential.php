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
     * @param $client_id//
     * @param $client_secret
     */
    public function __construct($clientId, $clientSecret)
    {
        parent::__construct();

        $this->client_id = $clientId;
        $this->client_secret = $clientSecret;
    }


    /**
     * @param string $environment
     * @return string
     */
    public function getAccessToken($environment = 'demo')
    {
        // TODO: handle token expiration

        // return access_token if already exists
        if($this->access_token !== null) {
            return $this->access_token;
        }

        // authenticate otherwise
        $oAuthClient = new OAuthClient($environment);
        $response = $oAuthClient->authorize(
            $this->client_id,
            $this->client_secret
        );

        $this->update($response);
        return $this->access_token;
    }





}
