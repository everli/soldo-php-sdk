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
use Soldo\Exceptions\SoldoTransferException;
use Soldo\Resources\InternalTransfer;
use Soldo\Utils\Paginator;
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
     * Define an internal token to authenticate transfer and webhook
     */
    const INTERNAL_TOKEN = '3BCABDC115ED11E79287';

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
     * @param OAuthCredential $credential
     */
    public function __construct(OAuthCredential $credential, $environment = 'demo')
    {
        $this->credential = $credential;

        $base_uri = $environment === 'live' ?
            self::API_LIVE_URL :
            self::API_TEST_URL;

        // instantiate new guzzle client
        $this->httpClient = new Client(
            [
                'base_uri' => $base_uri,
                'timeout' => self::TIMEOUT,
                'verify' => false,
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
     * @param string $method
     * @param string $path
     * @param array $data
     * @param Paginator $paginator
     * @return array|mixed
     */
    private function call($method, $path, $data = [], Paginator $paginator = null)
    {

        // get access token
        $access_token = $this->getAccessToken();

        // build authorization header
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
            ],
            'query' => [],
            'json' =>[],
        ];

        // append pagination params to query
        if ($paginator !== null) {
            $options['query'] = array_merge($options['query'], $paginator->getQueryParameters());
        }

        // do different stuff for each method
        // only support GET and POST
        switch ($method) {
            case 'GET':
                // pass params as query parameters
                if (is_array($data) && !empty($data)) {
                    $options['query'] = array_merge($options['query'], $data);
                }
                break;

            case 'POST':
                // build a json from $data and attach to the request
                if (is_array($data) && !empty($data)) {
                    $options['json'] = array_merge($options['json'], $data);
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
        if (class_exists($className) === false) {
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
     * @throws \Exception
     * @return array
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
     * @param string $className
     * @param Paginator $paginator
     * @param array $queryParameters
     * @throws \Exception
     * @return SoldoCollection
     */
    public function getCollection($className, Paginator $paginator = null, $queryParameters = [])
    {
        try {
            // validate class name
            $this->validateClassName($className);

            /** @var SoldoCollection $collection */
            $collection = new $className();

            // get collection remote path
            $remote_path = $collection->getRemotePath();

            // make request and fill collection
            $data = $this->call('GET', $remote_path, $queryParameters, $paginator);

            return $collection->fill($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Build the resource starting from remote data
     *
     * @param string $className
     * @param string $id
     * @param array $queryParameters
     * @throws \Exception
     * @return SoldoResource
     */
    public function getItem($className, $id = null, $queryParameters = [])
    {
        try {
            $this->validateClassName($className);

            /** @var SoldoResource $object */
            $object = new $className();
            $object->id = $id;

            // get resource remote path
            $remote_path = $object->getRemotePath();

            // fetch data and fill object
            $data = $this->call('GET', $remote_path, $queryParameters);

            return $object->fill($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update the remote resource and return it
     *
     * @param string $className
     * @param string $id
     * @param array $data
     * @throws \Exception
     * @return SoldoResource
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

    public function performTransfer($fromWalletId, $toWalletId, $amount, $currencyCode)
    {
        try {
            $transfer = new InternalTransfer();
            $transfer->fromWalletId = $fromWalletId;
            $transfer->toWalletId = $toWalletId;
            $transfer->amount = $amount;
            $transfer->currency = $currencyCode;

            $data = $this->transfer($transfer);

            return $transfer->fill($data);
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
     * @param InternalTransfer $internalTransfer
     * @throws SoldoAuthenticationException
     * @return array
     */
    private function transfer(InternalTransfer $internalTransfer)
    {
        try {

            // get token, fingerprint and path
            $access_token = $this->getAccessToken();
            $fingerprint = $internalTransfer->generateFingerPrint(self::INTERNAL_TOKEN);
            $path = $internalTransfer->getRemotePath();

            // build authorization header
            $options = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $access_token,
                    'X-Soldo-Fingerprint' => $fingerprint,
                ],
                'form_params' => [
                    'amount' => $internalTransfer->amount,
                    'currencyCode' => $internalTransfer->currency,
                ],
            ];

            // Soldo call
            $response = $this->httpClient->request(
                'POST',
                self::API_ENTRY_POINT . $path,
                $options
            );

            return $this->toArray($response->getBody());
        } catch (\Exception $e) {
            // TODO: log stuff

            throw new SoldoTransferException(
                'Unable to transfer money. '
                . $e->getMessage()
            );
        }
    }

    /**
     * Perform a request to the /authorize endpoint
     *
     * @throws SoldoAuthenticationException
     * @return array|mixed
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
                        'client_secret' => $this->credential->client_secret,
                    ],
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
