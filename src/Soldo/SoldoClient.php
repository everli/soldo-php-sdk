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
use Soldo\Resources\SoldoResource;


/**
 * Class SoldoClient
 *
 * @package Soldo
 */
class SoldoClient
{
    /**
     * Define Guzzle timeout in seconds
     */
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

        // do different stuff for each method
        // only support GET and POST
        switch ($method) {
            case 'GET':
                // pass params as query parameters
                if(is_array($data) && !empty($data)) {
                    $options['query'] = $data;
                }
                break;

            case 'POST':
                // build a json from $data and attach to the request
                if(is_array($data) && !empty($data)) {
                    $options['json'] = $data;
                }
                break;
        }


        // perform the request
        $response = $this->httpClient->request(
            $method,
            self::API_ENTRY_POINT . $path,
            $options
        );

        return $this->toArray($response->getBody());
    }


    /**
     * Throws an exception if class does not exist
     *
     * @param $className
     */
    private function validateClassName($className)
    {
        if(class_exists($className) === false) {
            throw new \InvalidArgumentException(
                'Error trying to access a not existing class '
                . $className . ' doesn\'t exist'
            );
        }
    }

    /**
     * Get an has many relationship
     *
     * @param $className
     * @param $id
     * @param $relationshipName
     * @return array
     * @throws \Exception
     */
    public function getRelationship($className, $id, $relationshipName)
    {
        try {
            // validate class name
            $this->validateClassName($className);

            /** @var SoldoResource $object */
            $object = new $className();
            $object->id = $id;

            // get relationship remote path
            $remote_path = $object->getRelationshipRemotePath($relationshipName);

            $data = $this->call('GET', $remote_path);
            return $object->buildRelationship($relationshipName, $data);

        } catch (\Exception $e) {

            throw $e;

        }
    }

    /**
     * Build and return a SoldoCollection starting from remote data
     *
     * @param $resourceType
     * @param array $queryParameters
     * @return SoldoCollection
     * @throws \Exception
     */
    public function getCollection($className, $queryParameters = [])
    {
        try {
            // validate class name
            $this->validateClassName($className);

            /** @var SoldoCollection $collection */
            $collection = new $className();

            // get collection remote path
            $remote_path = $collection->getRemotePath();

            // make request and fill collection
            $data = $this->call('GET', $remote_path, $queryParameters);
            return $collection->fill($data);

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
    public function getItem($className, $id = null)
    {

        try {

            $this->validateClassName($className);

            /** @var SoldoResource $object */
            $object = new $className();
            $object->id = $id;

            // get resource remote path
            $remote_path = $object->getRemotePath();

            // fetch data and fill object
            $data = $this->call('GET', $remote_path);
            return $object->fill($data);

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
    public function updateItem($className, $id, $data)
    {

        try {

            $this->validateClassName($className);

            /** @var SoldoResource $object */
            $object = new $className();
            $object->id = $id;

            // get remote path
            $remote_path = $object->getRemotePath();

            // keep only wanted data
            $update_data = $object->filterWhiteList($data);

            // fetch data and update object
            $updated_data = $this->call('POST', $remote_path, $update_data);
            return $object->fill($updated_data);

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

