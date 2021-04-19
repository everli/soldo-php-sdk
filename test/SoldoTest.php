<?php

namespace Soldo\Tests;

use PHPUnit\Framework\TestCase;
use Soldo\Resources\Card;
use Soldo\Resources\Company;
use Soldo\Resources\Employee;
use Soldo\Resources\Group;
use Soldo\Resources\Rule;
use Soldo\Resources\Transaction;
use Soldo\Resources\Wallet;
use Soldo\Soldo;

/**
 * Class SoldoTest
 * @package Soldo\Tests
 */
class SoldoTest extends TestCase
{

    /** @var Soldo */
    private $soldo;

    public function setUp()
    {
        $this->soldo = new Soldo(
            [
                'client_id' => SoldoTestCredentials::CLIENT_ID,
                'client_secret' => SoldoTestCredentials::CLIENT_SECRET,
                'environment' => 'demo',
            ]
        );
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoException
     * @expectedExceptionMessage Required "client_id" key is missing in config
     */
    public function testConstructorWithoutConfig()
    {
        $s = new Soldo([]);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoException
     * @expectedExceptionMessage Required "client_id" key is missing in config
     */
    public function testConstructorWithoutClientIdParam()
    {
        $s = new Soldo(['client_secret' => 'FOO']);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoException
     * @expectedExceptionMessage Required "client_secret" key is missing in config
     */
    public function testConstructorWithoutClientSecretParam()
    {
        $s = new Soldo(['client_id' => 'FOO']);
    }

    public function testConstructor()
    {
        $s = new Soldo(['client_id' => 'FOO', 'client_secret' => 'BAR']);
        $this->assertInstanceOf(Soldo::class, $s);
    }

    /**
     * @return array
     */
    private function getWalletsId()
    {
        $walletIds = ['id' => null, 'id_with_available_amount' => null];
        $wallets = $this->soldo->getWallets();
        $walletIds['id'] = $wallets[0]->id;

        foreach ($wallets as $w) {
            if ($w->available_amount > 0 && $w->id != $walletIds['id']) {
                $walletIds['id_with_available_amount'] = $w->id;
            }
        }

        return $walletIds;
    }

    /**
     * @return mixed
     */
    private function getGroupId()
    {
        $groups = $this->soldo->getGroups();

        return $groups[0]->id;
    }

    /**
     * @return mixed
     */
    private function getEmployeeId()
    {
        $employees = $this->soldo->getEmployees();

        return $employees[0]->id;
    }

    /**
     * @return mixed
     */
    private function getTransactionId()
    {
        $transactions = $this->soldo->getTransactions();

        return $transactions[0]->id;
    }

    /**
     * @return mixed
     */
    private function getCardId()
    {
        $cards = $this->soldo->getCards();

        return $cards[0]->id;
    }

    public function testGetWallets()
    {
        $wallets = $this->soldo->getWallets();
        $this->assertInternalType('array', $wallets);
        $this->assertTrue(count($wallets) > 1, 'There should be at least two Wallet');

        /** @var Wallet $wallet */
        $wallet = $wallets[0];
        $this->assertInternalType('string', $wallet->id);

        foreach ($wallets as $wallet) {
            /** @var Wallet $wallet  */
            $this->assertInstanceOf(Wallet::class, $wallet);
        }
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoModelNotFoundException
     */
    public function testGetWalletNotFound()
    {
        $wallet = $this->soldo->getWallet('A_NOT_EXISTING_WALLET_ID');
    }

    public function testGetWallet()
    {
        $ids = $this->getWalletsId();
        $wallet = $this->soldo->getWallet($ids['id']);
        $this->assertInstanceOf(Wallet::class, $wallet);
        $this->assertEquals($ids['id'], $wallet->id);
    }

    public function testGetGroups()
    {
        $groups = $this->soldo->getGroups();
        $this->assertInternalType('array', $groups);
        $this->assertTrue(count($groups) > 0, 'There should be at least one Expense Centre');

        foreach ($groups as $group) {
            /** @var Group $groups*/
            $this->assertInstanceOf(Group::class, $group);
        }
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoBadRequestException
     */
    public function testGetGroupNotFound()
    {
        $expenseCentre = $this->soldo->getGroup('A_NOT_EXISTING_GROUP_ID');
    }

    public function testGetGroup()
    {
        $groupId = $this->getGroupId();
        /** @var Group $group */
        $group = $this->soldo->getGroup($groupId);
        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals($groupId, $group->id);
    }

    public function testGetEmployees()
    {
        $employees = $this->soldo->getEmployees();
        $this->assertInternalType('array', $employees);
        $this->assertTrue(count($employees) > 0, 'There should be at least one Employee');

        foreach ($employees as $employee) {
            /** @var Employee $employ  */
            $this->assertInstanceOf(Employee::class, $employee);
        }
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoModelNotFoundException
     */
    public function testGetEmployeeNotFound()
    {
        $employee = $this->soldo->getEmployee('A_NOT_EXISTING_EMPLOYEE_ID');
    }

    public function testGetEmployee()
    {
        $employeeId = $this->getEmployeeId();
        $employee = $this->soldo->getEmployee($employeeId);
        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals($employeeId, $employee->id);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateEmployeeeEmptyData()
    {
        $employeeId = $this->getEmployeeId();
        $employee = $this->soldo->updateEmployee($employeeId, []);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateEmployBlacklistedData()
    {
        $employeeId = $this->getEmployeeId();
        $employee = $this->soldo->updateEmployee($employeeId, ['a_not_whitelisted_key' => 'Random Value']);
    }

    public function testUpdateEmployee()
    {
        $employeeId = $this->getEmployeeId();
        $employee = $this->soldo->updateEmployee($employeeId, ['department' => 'Random Department']);
        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals('Random Department', $employee->department);
    }

    public function testUpdateEmployeeWithBlacklistedData()
    {
        $employeeId = $this->getEmployeeId();
        $employee = $this->soldo->updateEmployee($employeeId, ['department' => 'Another Random Department', 'a_not_whitelisted_key' => 'Random Value']);
        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals('Another Random Department', $employee->department);
    }

    public function testGetTransactions()
    {
        $transactions = $this->soldo->getTransactions();
        $this->assertInternalType('array', $transactions);
        $this->assertTrue(count($transactions) > 0, 'There should be at least one Transaction');

        foreach ($transactions as $transaction) {
            /** @var Transaction $transaction */
            $this->assertInstanceOf(Transaction::class, $transaction);
        }
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoModelNotFoundException
     */
    public function testGetTransactionNotFound()
    {
        $transaction = $this->soldo->getTransaction('A_NOT_EXISTING_TRANSACTION_ID');
    }

    public function testGetTransaction()
    {
        $transactionId = $this->getTransactionId();
        $transaction = $this->soldo->getTransaction($transactionId);
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals($transactionId, $transaction->id);
        $this->assertNull($transaction->details);
    }

    public function testGetTransactionWithDetails()
    {
        $transactionId = $this->getTransactionId();
        $transaction = $this->soldo->getTransaction($transactionId, true);
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals($transactionId, $transaction->id);
        $this->assertNotNull($transaction->details);
        $this->assertInternalType('array', $transaction->details);
    }

    public function testGetCards()
    {
        $cards = $this->soldo->getCards();
        $this->assertInternalType('array', $cards);
        $this->assertTrue(count($cards) > 0, 'There should be at least one Card');

        foreach ($cards as $card) {
            /** @var Card $card */
            $this->assertInstanceOf(Card::class, $card);
        }
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoModelNotFoundException
     */
    public function testGetCardNotFound()
    {
        $card = $this->soldo->getCard('A_NOT_EXISTING_CARD_ID');
    }

    public function testGetCard()
    {
        $cardId = $this->getCardId();
        $card = $this->soldo->getCard($cardId);
        $this->assertInstanceOf(Card::class, $card);
        $this->assertEquals($cardId, $card->id);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoModelNotFoundException
     */
    public function testGetCardRulesNotFound()
    {
        $rules = $this->soldo->getCardRules('A_NOT_EXISTING_CARD_ID');
    }

    public function testGetCardRules()
    {
        $cardId = $this->getCardId();
        $rules = $this->soldo->getCardRules($cardId);
        $this->assertInternalType('array', $rules);
        $this->assertTrue(count($rules) > 0, 'There should be at least one Rule for the required card');

        foreach ($rules as $rule) {
            /** @var Rule $rule */
            $this->assertInstanceOf(Rule::class, $rule);
        }
    }

    public function testGetCompany()
    {
        $company = $this->soldo->getCompany();
        $this->assertInstanceOf(Company::class, $company);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInternalTransferException
     */
    public function testPerformTransferToNotExistingWallet()
    {
        $walletIds = $this->getWalletsId();
        $transfer = $this->soldo->transferMoney(
            $walletIds['id_with_available_amount'],
            'ANOTHER_NOT_EXISTING_WALLET',
            1,
            'EUR',
            SoldoTestCredentials::INTERNAL_TOKEN
        );
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInternalTransferException
     */
    public function testPerformTransferFromNotExistingWallet()
    {
        $walletIds = $this->getWalletsId();
        $transfer = $this->soldo->transferMoney(
            'ANOTHER_NOT_EXISTING_WALLET',
            $walletIds['id'],
            1,
            'EUR',
            SoldoTestCredentials::INTERNAL_TOKEN
        );
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInternalTransferException
     */
    public function testPerformTransferWithInvalidAmount()
    {
        $walletIds = $this->getWalletsId();
        $transfer = $this->soldo->transferMoney(
            $walletIds['id_with_available_amount'],
            $walletIds['id'],
            9999999,
            'EUR',
            SoldoTestCredentials::INTERNAL_TOKEN
        );
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInternalTransferException
     */
    public function testPerformTransferWithInvalidCurrencyCode()
    {
        $walletIds = $this->getWalletsId();
        $transfer = $this->soldo->transferMoney(
            $walletIds['id_with_available_amount'],
            $walletIds['id'],
            1,
            'NOT_EXISTING_CURRENCY_CODE',
            SoldoTestCredentials::INTERNAL_TOKEN
        );
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInternalTransferException
     */
    public function testPerformTransferWithInvalidInternalToken()
    {
        $walletIds = $this->getWalletsId();
        $transfer = $this->soldo->transferMoney(
            $walletIds['id_with_available_amount'],
            $walletIds['id'],
            1,
            'EUR',
            '12345678910QAZWSXEDCRFVTGBYHNUJMIKOLP'
        );
    }

    public function testPerformTransfer()
    {
        $amountToTransfer = 1;
        $walletIds = $this->getWalletsId();

        $fromWallet = $this->soldo->getWallet($walletIds['id_with_available_amount']);
        $toWallet = $this->soldo->getWallet($walletIds['id']);

        $availableAmountOfFromWallet = $fromWallet->available_amount;
        $availableAmountOfToWallet = $toWallet->available_amount;

        $transfer = $this->soldo->transferMoney(
            $walletIds['id_with_available_amount'],
            $walletIds['id'],
            $amountToTransfer,
            'EUR',
            SoldoTestCredentials::INTERNAL_TOKEN
        );

        $availableAmountOfFromWalletAfter = $transfer->from_wallet->available_amount;
        $availableAmountOfToWalletAfter = $transfer->to_wallet->available_amount;

        $this->assertEquals($availableAmountOfFromWallet - $amountToTransfer, $availableAmountOfFromWalletAfter);
        $this->assertEquals($availableAmountOfToWallet + $amountToTransfer, $availableAmountOfToWalletAfter);

        // double check just to be sure
        $fromWallet = $this->soldo->getWallet($walletIds['id_with_available_amount']);
        $toWallet = $this->soldo->getWallet($walletIds['id']);

        $this->assertEquals($availableAmountOfFromWallet - $amountToTransfer, $fromWallet->available_amount);
        $this->assertEquals($availableAmountOfToWallet + $amountToTransfer, $toWallet->available_amount);
    }
}
