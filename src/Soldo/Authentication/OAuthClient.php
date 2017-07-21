<?php

namespace Soldo\Authentication;

use GuzzleHttp\Exception\RequestException;
use Soldo\Exceptions\SoldoAuthenticationException;
use Soldo\SoldoClient;

/**
 * Class OAuthClient
 * @package Soldo\Authentication
 */
class OAuthClient extends SoldoClient
{

    /**
     * Define authorize URL
     */
    const AUTHORIZE_URL = '/oauth/authorize';


    /**
     * OAuthClient constructor.
     * @param string $environment
     */
    public function __construct($environment = 'demo')
    {
        parent::__construct($environment);
    }

    /**
     * @param $clientId
     * @param $clientSecret
     * @return mixed
     * @throws SoldoAuthenticationException
     */
    public function authorize($clientId, $clientSecret)
    {
        try{

            $response = $this->client->request(
                'POST',
                self::AUTHORIZE_URL,
                [
                    'form_params' => [
                        'client_id' => $clientId,
                        'client_secret' => $clientSecret
                    ]
                ]
            );

            return json_decode($response->getBody(), true);

        } catch (RequestException $e) {

            // TODO log stuff

            throw new SoldoAuthenticationException(
                'Unable to authenticate user. '
                .'Check your credential'
            );
        }
    }


}
