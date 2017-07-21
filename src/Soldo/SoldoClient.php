<?php
/**
 * Copyright 2017 Supermercato24.
 *
 * Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur blandit tempus porttitor. Maecenas sed diam
 * eget risus varius blandit sit amet non magna. Donec sed odio dui. Vivamus sagittis lacus vel augue laoreet rutrum
 * faucibus dolor auctor.
 *
 */

namespace Soldo;

use \GuzzleHttp\Client;
use \Psr\Http\Message\StreamInterface;


/**
 * Class SoldoClient
 *
 * @package Soldo
 */
class SoldoClient
{

    const TIMEOUT = 0;

    /**
     * Define live api base URL
     */
    const API_LIVE_URL = 'https://api.soldo.com/';

    /**
     * Define test api base URL
     */
    const API_TEST_URL = 'https://api-demo.soldocloud.net';

    /**
     *
     */
    const API_ENTRY_POINT = '/business/v1';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * SoldoClient constructor.
     * @param string $environment
     */
    public function __construct($environment = 'demo')
    {
        $base_uri = $environment == 'live' ?
            self::API_LIVE_URL :
            self::API_TEST_URL;

        // instantiate new guzzle client
        $this->client = new Client([
                'base_uri' => $base_uri,
                'timeout' => self::TIMEOUT,
                'verify' => false
            ]
        );
    }


    /**
     * @param StreamInterface $body
     * @return array|mixed
     */
    protected function toArray(StreamInterface $body)
    {
        // try decoding the body
        $array = json_decode($body, true);

        // check if it is a valid json
        if(json_last_error() !== JSON_ERROR_NONE) {
            // return an empty array if the body is empty or null
            if($array === '' || $array === null) {
                return [];
            }
            throw new \InvalidArgumentException('Invalid JSON string');
        }
        // return associative array
        return json_decode($body, true);
    }


    /**
     * @param $resourcePath
     * @param $accessToken
     * @return array|mixed
     */
    public function get($resourcePath, $accessToken)
    {
        try{
            $response = $this->client->request(
                'GET',
                self::API_ENTRY_POINT . $resourcePath,
                [
                    'headers' => [
                        'Authorization' => 'Bearer '.$accessToken
                    ]
                ]
            );
            return $this->toArray($response->getBody());
        } catch (\Exception $e) {

            //TODO: handle Guzzle exceptions

        }
    }


}

