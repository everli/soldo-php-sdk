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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Soldo\Authentication\OAuthCredential;
use Soldo\Exceptions\SoldoAuthenticationException;
use Soldo\Exceptions\SoldoBadRequestException;
use Soldo\Exceptions\SoldoInternalServerErrorException;
use Soldo\Exceptions\SoldoInternalTransferException;
use Soldo\Exceptions\SoldoMethodNotAllowedException;
use Soldo\Exceptions\SoldoModelNotFoundException;
use Soldo\Exceptions\SoldoSDKException;
use Soldo\Exceptions\SoldoUnauthorizedException;
use Soldo\Resources\InternalTransfer;
use Soldo\Utils\Paginator;
use Soldo\Resources\Collection;
use Soldo\Resources\Resource;

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
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SoldoClient constructor.
     * @param OAuthCredential $credential
     * @param string $environment
     * @param LoggerInterface|null $logger
     */
    public function __construct(OAuthCredential $credential, $environment = 'demo', LoggerInterface $logger = null)
    {
        $this->credential = $credential;
        $this->logger = $logger;

        $base_uri = $environment === 'live' ?
            self::API_LIVE_URL :
            self::API_TEST_URL;

        // instantiate new guzzle client
        $this->httpClient = new Client(
            [
                'base_uri' => $base_uri,
                'timeout' => self::TIMEOUT,
                'verify' => true,
            ]
        );
    }

    /**
     * Log stuff if a logger is provided
     *
     * @param string $level
     * @param string $message
     * @param array $context
     */
    private function log($level, $message, $context = [])
    {
        if ($this->logger !== null) {
            $this->logger->log($level, $message, $context);
        }
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
            'json' => [],
        ];

        // append pagination params to query
        if ($paginator !== null) {
            $options['query'] = array_merge($options['query'], $paginator->toArray());
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

        // log
        $this->log(
            LogLevel::INFO,
            'SoldoClient call',
            [$method, $path, $options['query'], $options['json']]
        );

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
     * Throw exception and log error
     *
     * @param TransferException $e
     * @param array $data
     * @throws SoldoAuthenticationException
     * @throws SoldoBadRequestException
     * @throws SoldoInternalServerErrorException
     * @throws SoldoMethodNotAllowedException
     * @throws SoldoModelNotFoundException
     * @throws SoldoSDKException
     */
    private function handleGuzzleException(TransferException $e, $data = [])
    {
        $code = $e->getCode();
        $message = $e->getMessage();

        // log
        $this->log(
            LogLevel::ERROR,
            $message,
            $data
        );

        switch ($code) {
            case 400:
                throw new SoldoBadRequestException(
                    'Your request is invalid'
                );
                break;

            case 401:
                throw new SoldoUnauthorizedException(
                    'API key is wrong'
                );
                break;

            case 404:
                throw new SoldoModelNotFoundException(
                    'The specified resource could not be found'
                );
                break;

            case 405:
                throw new SoldoMethodNotAllowedException(
                    'You don’t have the grant required to use the method'
                );
                break;

            case 500:
                throw new SoldoInternalServerErrorException(
                    'Soldo had a problem with its server. Try again later'
                );
                break;

            default:
                throw new SoldoSDKException($message);
                break;
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

            // log
            $this->log(
                LogLevel::INFO,
                'Start authorizing user'
            );

            return $this->toArray($response->getBody());
        } catch (\Exception $e) {
            // log
            $this->log(
                LogLevel::ERROR,
                'Error authorizing user [' . $e->getMessage() . ']'
            );

            throw new SoldoAuthenticationException(
                'Unable to authenticate user. '
                . 'Check your credential'
            );
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

        return $this->credential->access_token;
    }

    /**
     * Build and return a Collection starting from remote data
     *
     * @param string $className
     * @param Paginator $paginator
     * @param array $queryParameters
     * @throws \Exception
     * @return Collection
     */
    public function getCollection($className, Paginator $paginator = null, $queryParameters = [])
    {
        // validate class name
        $this->validateClassName($className);

        /** @var Collection $collection */
        $collection = new $className();

        // get collection remote path
        $remote_path = $collection->getRemotePath();

        try {
            // make request and fill collection
            $data = $this->call('GET', $remote_path, $queryParameters, $paginator);

            return $collection->fill($data);
        } catch (TransferException $e) {
            $this->handleGuzzleException($e, ['class' => $className, 'data' => $queryParameters]);
        }
    }

    /**
     * Build the resource starting from remote data
     *
     * @param string $className
     * @param string $id
     * @param array $queryParameters
     * @throws \Exception
     * @return Resource
     */
    public function getItem($className, $id = null, $queryParameters = [])
    {
        $this->validateClassName($className);

        /** @var Resource $object */
        $object = new $className();
        $object->id = $id;

        // get resource remote path
        $remote_path = $object->getRemotePath();

        try {
            // fetch data and fill object
            $data = $this->call('GET', $remote_path, $queryParameters);

            return $object->fill($data);
        } catch (TransferException $e) {
            $this->handleGuzzleException($e, ['className' => $className, 'id' => $id, 'data' => $queryParameters]);
        }
    }

    /**
     * Update the remote resource and return it
     *
     * @param string $className
     * @param string $id
     * @param array $data
     * @throws \Exception
     * @return Resource
     */
    public function updateItem($className, $id, $data)
    {
        $this->validateClassName($className);

        /** @var Resource $object */
        $object = new $className();
        $object->id = $id;

        // get remote path
        $remote_path = $object->getRemotePath();

        // keep only wanted data
        $update_data = $object->filterWhiteList($data);
        if (empty($update_data)) {
            throw new \InvalidArgumentException(
                '$data cannot be empty or filled '
                . 'only with not whitelisted fields'
            );
        }

        try {
            // fetch data and update object
            $updated_data = $this->call('POST', $remote_path, $update_data);

            return $object->fill($updated_data);
        } catch (TransferException $e) {
            $this->handleGuzzleException($e, ['class' => $className, 'id' => $id, 'data' => $data]);
        }
    }

    /**
     * Get an has many relationship
     *
     * @param $className
     * @param $id
     * @param $relationshipName
     * @return array
     */
    public function getRelationship($className, $id, $relationshipName)
    {
        // validate class name
        $this->validateClassName($className);

        /** @var Resource $object */
        $object = new $className();
        $object->id = $id;

        // get relationship remote path
        $remote_path = $object->getRelationshipRemotePath($relationshipName);

        try {
            $data = $this->call('GET', $remote_path);

            return $object->buildRelationship($relationshipName, $data);
        } catch (TransferException $e) {
            $this->handleGuzzleException($e, ['class' => $className, 'id' => $id, 'relationship' => $relationshipName]);
        }
    }

    /**
     * Transfer money from a wallet to another.
     *
     * @param $fromWalletId
     * @param $toWalletId
     * @param $amount
     * @param $currencyCode
     * @param $internalToken
     * @throws \Exception
     * @return InternalTransfer
     */
    public function performTransfer($fromWalletId, $toWalletId, $amount, $currencyCode, $internalToken)
    {
        try {
            // get token
            $access_token = $this->getAccessToken();

            $it = new InternalTransfer();
            $it->fromWalletId = $fromWalletId;
            $it->toWalletId = $toWalletId;
            $it->amount = $amount;
            $it->currency = $currencyCode;

            // get fingerprint and remote path
            $fingerprint = $it->generateFingerPrint($internalToken);
            $path = $it->getRemotePath();

            // build guzzle options
            $options = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $access_token,
                    'X-Soldo-Fingerprint' => $fingerprint,
                ],
                'form_params' => [
                    'amount' => $it->amount,
                    'currencyCode' => $it->currency,
                ],
            ];

            // log
            $this->log(
                LogLevel::INFO,
                'Start internal transfer',
                $it->toArray()
            );

            // remote call
            $response = $this->httpClient->request(
                'POST',
                self::API_ENTRY_POINT . $path,
                $options
            );

            $data = $this->toArray($response->getBody());

            return $it->fill($data);
        } catch (TransferException $e) {
            // log
            $this->log(
                LogLevel::ERROR,
                'Error transferring money [' . $e->getMessage() . ']',
                [
                    'fromWalletId' => $fromWalletId,
                    'toWalletId' => $toWalletId,
                    'amount' => $amount,
                    'currencyCode' => $currencyCode,
                ]
            );

            throw new SoldoInternalTransferException(
                'Unable to transferring money (' . $amount . ' ' . $currencyCode . ') from  '
                . $fromWalletId . ' to '
                . $toWalletId
            );
        }
    }
}
