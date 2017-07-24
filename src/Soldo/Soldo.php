<?php

namespace Soldo;

use Soldo\Authentication\OAuthCredential;
use Soldo\Exceptions\SoldoSDKException;
use Soldo\Resources\ExpenseCentre;
use Soldo\Resources\SoldoCollection;

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

        if (!array_key_exists('client_id', $config)) {
            throw new SoldoSDKException('Required "client_id" key is missing in config');
        }

        if (!array_key_exists('client_secret', $config)) {
            throw new SoldoSDKException('Required "client_secret" key is missing in config');
        }

        $this->client = new SoldoClient(
            new OAuthCredential($config['client_id'], $config['client_secret']),
            $config['environment']
        );

    }


    /**
     * Return an array containing a list of ExpenseCentre
     *
     * @return array
     */
    public function getExpenseCentres()
    {
        $collection = $this->client->getCollection('ExpenseCentre');
        return $collection->get();
    }

    public function getExpenseCentre($id)
    {
        $expense_center = $this->client->getItem('ExpenseCentre', $id);
        return $expense_center;
    }


}
