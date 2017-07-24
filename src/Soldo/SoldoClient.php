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
use \Soldo\Authentication\OAuthCredential;
use \Soldo\Exceptions\SoldoAuthenticationException;
use Soldo\Resources\SoldoCollection;


/**
 * Class SoldoClient
 *
 * @package Soldo
 */
class SoldoClient
{

    const TIMEOUT = 60;

    /**
     * Define live api base URL
     */
    const API_LIVE_URL = 'https://api.soldo.com/';

    /**
     * Define test api base URL
     */
    const API_TEST_URL = 'https://api-demo.soldocloud.net';

    /**
     * Define the entry point for each request (except the authorize one)
     */
    const API_ENTRY_POINT = '/business/v1';

    /**
     * Define authorize URL
     */
    const AUTHORIZE_URL = '/oauth/authorize';

    /**
     * Define resource namespace
     */
    const RESOURCE_NAMESPACE = '\Soldo\Resources\\';

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var OAuthCredential
     */
    protected $credential;


    /**
     * SoldoClient constructor.
     * @param string $environment
     */
    public function __construct(OAuthCredential $credential, $environment = 'demo')
    {
        $this->credential = $credential;

        $base_uri = $environment == 'live' ?
            self::API_LIVE_URL :
            self::API_TEST_URL;

        // instantiate new guzzle client
        $this->httpClient = new Client([
                'base_uri' => $base_uri,
                'timeout' => self::TIMEOUT,
                'verify' => false
            ]
        );
    }


    /**
     * Validate and build a json starting from request body
     *
     * @param StreamInterface $body
     * @throws \InvalidArgumentException
     * @return array|mixed
     */
    protected function toArray(StreamInterface $body)
    {
        // try decoding the body
        $array = json_decode($body, true);

        // check if it is a valid json
        if (json_last_error() !== JSON_ERROR_NONE) {
            // return an empty array if the body is empty or null
            if ($array === '' || $array === null) {
                return [];
            }
            throw new \InvalidArgumentException('Invalid JSON string');
        }
        // return associative array
        return json_decode($body, true);
    }


    private function getParsedData($class, $data)
    {
        $class_constant = $class . '::EDITABLE';
        $editable = @constant($class_constant);

        if ($editable === null) {
            $editable = [];
        }

        foreach ($data as $key => $value) {
            if (!in_array($key, $editable)) {
                unset($data[$key]);
            }
        }

        return $data;

    }

    /**
     * Build the resource remote URL
     *
     * @param $class
     * @throws \InvalidArgumentException
     * @return string
     */
    private function getRemoteResourceURL($class, $id = null)
    {
        $class_constant = $class . '::RESOURCE_PATH';
        $resource_path = @constant($class_constant);

        if ($resource_path === null) {
            throw new \InvalidArgumentException(
                'Error trying access constant '
                . $class . '::RESOURCE_PATH is not defined'
            );
        }

        $url = self::API_ENTRY_POINT . $resource_path;

        // if it a single resource and not a collection append the id to the url
        if ($id !== null) {
            $url .= "/" . $id;
        }

        return $url;
    }

    /**
     * Build resource class
     *
     * @param $resourceType
     * @throws \InvalidArgumentException
     * @return string
     */
    private function getResourceClass($resourceType)
    {
        // get full class location
        $class = self::RESOURCE_NAMESPACE . $resourceType;

        // check that class exists, throws exception otherwise
        if (class_exists($class) === false) {
            throw new \InvalidArgumentException(
                'Error trying to access a not existing class '
                . $class . 'doesn\'t exist'
            );
        }

        return $class;
    }

    /**
     * Perform a remote call with Guzzle client
     *
     * @param $method
     * @param $path
     * @return array|mixed
     */
    private function call($method, $path, $data = [])
    {
        // get access token
        $access_token = $this->getAccessToken();

        // build authorization header
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
            ],
        ];

        // only populate data if method is not GET and data is not empty
        if ($method !== 'GET' &&
            !empty($data) && is_array($data)
        ) {
            $options['json'] = $data;
        }

        // perform the request
        $response = $this->httpClient->request(
            $method,
            $path,
            $options
        );

        return $this->toArray($response->getBody());
    }


    /**
     * Build and return a SoldoCollection starting from remote data
     *
     * @param $resource
     * @throws \InvalidArgumentException
     * @return array|mixed
     */
    public function getCollection($resourceType)
    {
        try {
            // get full class name
            $class = $this->getResourceClass($resourceType);

            // get remote resource url
            $resource_path = $this->getRemoteResourceURL($class);

            // fetch remote data
            $data = $this->call('GET', $resource_path);

            //n build collection
            $collection = new SoldoCollection($data, $class);
            return $collection;

        } catch (\Exception $e) {

            throw $e;

        }

    }

    /**
     * Build the resource starting from remote data
     *
     * @param $resourceType
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getItem($resourceType, $id)
    {
        try {

            // get full class name
            $class = $this->getResourceClass($resourceType);

            // get remote resource url
            $resource_path = $this->getRemoteResourceURL($class, $id);

            // fetch remote data
            $resource = $this->call('GET', $resource_path);
            return new $class($resource);

        } catch (\Exception $e) {

            throw $e;

        }
    }

    /**
     * Update the remote resource and return it
     *
     * @param $resourceType
     * @param $id
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function updateItem($resourceType, $id, $data)
    {
        try {

            // get full class name
            $class = $this->getResourceClass($resourceType);

            // keep only editable data according to class::EDITABLE const
            $data = $this->getParsedData($class, $data);

            // get remote resource url
            $resource_path = $this->getRemoteResourceURL($class, $id);

            // update remote data and return the updated resource
            $resource = $this->call('POST', $resource_path, $data);
            return new $class($resource);

        } catch (\Exception $e) {

            throw $e;

        }
    }


    /**
     * Get access token for authenticated request
     *
     * @return string
     */
    public function getAccessToken()
    {
        if ($this->credential->access_token === null) {
            $auth_data = $this->authorize();
            $this->credential->updateAuthenticationData($auth_data);
        }

        //TODO: handle token expiration
        if ($this->credential->isTokenExpired()) {

        }

        return $this->credential->access_token;
    }


    /**
     * Perform a request to the /authorize endpoint
     *
     * @return array|mixed
     * @throws SoldoAuthenticationException
     */
    private function authorize()
    {
        try {

            $response = $this->httpClient->request(
                'POST',
                self::AUTHORIZE_URL,
                [
                    'form_params' => [
                        'client_id' => $this->credential->client_id,
                        'client_secret' => $this->credential->client_secret
                    ]
                ]
            );
            return $this->toArray($response->getBody());

        } catch (\Exception $e) {

            // TODO: log stuff

            throw new SoldoAuthenticationException(
                'Unable to authenticate user. '
                . 'Check your credential'
            );
        }
    }


}

