<?php

namespace Soldo;

use Psr\Log\LoggerInterface;
use Soldo\Authentication\OAuthCredential;
use Soldo\Exceptions\SoldoException;
use Soldo\Resources\InternalTransfer;
use Soldo\Resources\Resource;
use Soldo\Resources\Transaction;
use Soldo\Resources\Transactions;
use Soldo\Utils\Paginator;
use Soldo\Resources\Card;
use Soldo\Resources\Cards;
use Soldo\Resources\Company;
use Soldo\Resources\Employee;
use Soldo\Resources\Employees;
use Soldo\Resources\ExpenseCentre;
use Soldo\Resources\ExpenseCentres;
use Soldo\Resources\Wallet;
use Soldo\Resources\Wallets;

/**
 * Class Soldo
 * @package Soldo
 */
class Soldo
{

    /**
     * @var SoldoClient
     */
    private $client;

    /**
     * Soldo constructor.
     * @param array $config
     * @param LoggerInterface $logger
     * @throws SoldoException
     */
    public function __construct(array $config = [], LoggerInterface $logger = null)
    {
        $config = array_merge(
            [
                'environment' => 'demo', //live
            ],
            $config
        );

        if (!array_key_exists('client_id', $config)) {
            throw new SoldoException('Required "client_id" key is missing in config');
        }

        if (!array_key_exists('client_secret', $config)) {
            throw new SoldoException('Required "client_secret" key is missing in config');
        }

        $this->client = new SoldoClient(
            new OAuthCredential($config['client_id'], $config['client_secret']),
            $config['environment'],
            $logger
        );
    }

    /**
     * Return an array containing a list of Wallet
     *
     * @param int $page
     * @param int $perPage
     * @param array $searchFields
     * @return array
     */
    public function getWallets($page = 0, $perPage = Paginator::MAX_ALLOWED_ITEMS_PER_PAGE, $searchFields = [])
    {
        $paginator = new Paginator($page, $perPage);
        $collection = $this->client->getCollection(Wallet::class, $paginator, $searchFields);

        return $collection->get();
    }

    /**
     * Return a single Wallet resource
     *
     * @param string $id
     * @return \Soldo\Resources\Resource
     */
    public function getWallet($id)
    {
        $wallet = $this->client->getItem(Wallet::class, $id);

        return $wallet;
    }

    /**
     * Return an array containing a list of ExpenseCentre
     *
     * @param int $page
     * @param int $perPage
     * @param array $searchFields
     * @return array
     */
    public function getExpenseCentres($page = 0, $perPage = Paginator::MAX_ALLOWED_ITEMS_PER_PAGE, $searchFields = [])
    {
        $paginator = new Paginator($page, $perPage);
        $collection = $this->client->getCollection(ExpenseCentre::class, $paginator, $searchFields);

        return $collection->get();
    }

    /**
     * Return a single ExpenseCentre resource
     *
     * @param $id
     * @return \Soldo\Resources\Resource
     */
    public function getExpenseCentre($id)
    {
        $expense_center = $this->client->getItem(ExpenseCentre::class, $id);

        return $expense_center;
    }

    /**
     * Update the ExpenseCentre by id and return the resource up to date
     *
     * @param $id
     * @param $data
     * @return \Soldo\Resources\Resource
     */
    public function updateExpenseCentre($id, $data)
    {
        $expense_center = $this->client->updateItem(ExpenseCentre::class, $id, $data);

        return $expense_center;
    }

    /**
     * Return an array containing a list of Employee
     *
     * @param int $page
     * @param int $perPage
     * @param array $searchFields
     * @return array
     */
    public function getEmployees($page = 0, $perPage = Paginator::MAX_ALLOWED_ITEMS_PER_PAGE, $searchFields = [])
    {
        $paginator = new Paginator($page, $perPage);
        $collection = $this->client->getCollection(Employee::class, $paginator, $searchFields);

        return $collection->get();
    }

    /**
     * Return a single Employee resource
     *
     * @param $id
     * @return \Soldo\Resources\Resource
     */
    public function getEmployee($id)
    {
        $employee = $this->client->getItem(Employee::class, $id);

        return $employee;
    }

    /**
     * Update the Employee by id and return the resource up to date
     *
     * @param $id
     * @param $data
     * @return \Soldo\Resources\Resource
     */
    public function updateEmployee($id, $data)
    {
        $employee = $this->client->updateItem(Employee::class, $id, $data);

        return $employee;
    }

    /**
     * Return an array containing a list of Transaction
     *
     * @param int $page
     * @param int $perPage
     * @param array $searchFields
     * @return array
     */
    public function getTransactions($page = 0, $perPage = Paginator::MAX_ALLOWED_ITEMS_PER_PAGE, $searchFields = [])
    {
        $paginator = new Paginator($page, $perPage);
        $collection = $this->client->getCollection(Transaction::class, $paginator, $searchFields);

        return $collection->get();
    }

    /**
     * Return a single Transaction resource
     *
     * @param $id
     * @param bool $withDetails
     * @return \Soldo\Resources\Resource
     */
    public function getTransaction($id, $withDetails = false)
    {
        $queryParameters = [];
        if (true === $withDetails) {
            $queryParameters['showDetails'] = 'true';
        }
        $transaction = $this->client->getItem(Transaction::class, $id, $queryParameters);

        return $transaction;
    }

    /**
     * Return an array containing a list of Card
     *
     * @param int $page
     * @param int $perPage
     * @param array $searchFields
     * @return array
     */
    public function getCards($page = 0, $perPage = Paginator::MAX_ALLOWED_ITEMS_PER_PAGE, $searchFields = [])
    {
        $paginator = new Paginator($page, $perPage);
        $collection = $this->client->getCollection(Card::class, $paginator, $searchFields);

        return $collection->get();
    }

    /**
     * Return a single Card resource
     *
     * @param $id
     * @return \Soldo\Resources\Resource
     */
    public function getCard($id)
    {
        $card = $this->client->getItem(Card::class, $id);

        return $card;
    }

    /**
     * Return an array containing the list of Rule for the card.
     *
     * @param $id
     * @return array
     */
    public function getCardRules($id)
    {
        $rules = $this->client->getRelationship(Card::class, $id, 'rules');

        return $rules;
    }

    /**
     * Return a single Company resource
     *
     * @return \Soldo\Resources\Resource
     */
    public function getCompany()
    {
        $company = $this->client->getItem(Company::class);

        return $company;
    }

    /**
     * Transfer an amount of money from one wallet to another
     *
     * @param string $fromWalletId
     * @param string $toWalletId
     * @param float $amount
     * @param string $internalToken
     * @param string $currencyCode
     * @return InternalTransfer
     */
    public function transferMoney($fromWalletId, $toWalletId, $amount, $currencyCode, $internalToken)
    {
        $internal_transfer = $this->client->performTransfer($fromWalletId, $toWalletId, $amount, $currencyCode, $internalToken);

        return $internal_transfer;
    }
}
