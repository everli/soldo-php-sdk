<?php

namespace Soldo;


use function GuzzleHttp\Promise\all;
use Soldo\Authentication\OAuthCredential;
use Soldo\Exceptions\SoldoSDKException;
use Soldo\Resources\ExpenseCentre;
use Soldo\Resources\SoldoCollection;
use Soldo\Resources\SoldoResource;

/**
 * Class Soldo
 * @package Soldo
 */
class Soldo
{

    /**
     * @var OAuthCredential
     */
    private $credential;

    /**
     * @var SoldoClient
     */
    private $client;

    /**
     * @var string
     */
    private $env;

    /**
     * Soldo constructor.
     * @param array $config
     * @throws SoldoSDKException
     */
    public function __construct(array $config = [])
    {
        $config = array_merge(
            [
                'environment' => 'demo', //live
                'log.enabled' => false,
                'log.file' => null,
                'log.level' => 'WARNING',
            ],
            $config
        );

        if(!array_key_exists('client_id', $config)) {
            throw new SoldoSDKException('Required "client_id" key is missing in config');
        }

        if(!array_key_exists('client_secret', $config)) {
            throw new SoldoSDKException('Required "client_secret" key is missing in config');
        }

        $this->env = $config['environment'];
        $this->client = new SoldoClient($config['environment']);
        $this->credential = new OAuthCredential($config['client_id'], $config['client_secret']);

    }


    public function getExpenseCentres()
    {
        $data = $this->client->get(
            ExpenseCentre::RESOURCE_PATH,
            $this->credential->getAccessToken()
        );
        $collection = new SoldoCollection($data, 'Soldo\Resources\ExpenseCentre');
        return $collection->get();
    }



}
