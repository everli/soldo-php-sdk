<?php

namespace Soldo\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Soldo\Exceptions\SoldoBadRequestException;
use Soldo\Exceptions\SoldoException;
use Soldo\Exceptions\SoldoInternalTransferException;
use Soldo\Exceptions\SoldoModelNotFoundException;
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

    public function setUp(): void
    {
        $this->soldo = new Soldo(
            [
                'client_id' => getenv('CLIENT_ID'),
                'client_secret' => getenv('CLIENT_SECRET'),
                'environment' => 'demo',
            ]
        );
    }

    public function testConstructorWithoutConfig()
    {
        $this->expectException(SoldoException::class);
        $this->expectExceptionMessage("Required \"client_id\" key is missing in config");

        new Soldo([]);
    }

    public function testConstructorWithoutClientIdParam()
    {
        $this->expectException(SoldoException::class);
        $this->expectExceptionMessage("Required \"client_id\" key is missing in config");

        new Soldo(['client_secret' => 'FOO']);
    }


    public function testConstructorWithoutClientSecretParam()
    {
        $this->expectException(SoldoException::class);
        $this->expectExceptionMessage("Required \"client_secret\" key is missing in config");

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
        $this->assertIsArray($wallets);
        $this->assertTrue(count($wallets) > 1, 'There should be at least two Wallet');

        /** @var Wallet $wallet */
        $wallet = $wallets[0];
        $this->assertIsString($wallet->id);

        foreach ($wallets as $wallet) {
            /** @var Wallet $wallet  */
            $this->assertInstanceOf(Wallet::class, $wallet);
        }
    }

    public function testGetWalletNotFound()
    {
        $this->expectException(SoldoModelNotFoundException::class);

        $this->soldo->getWallet('A_NOT_EXISTING_WALLET_ID');
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
        $this->assertIsArray($groups);
        $this->assertTrue(count($groups) > 0, 'There should be at least one Group');

        foreach ($groups as $group) {
            /** @var Group $groups*/
            $this->assertInstanceOf(Group::class, $group);
        }
    }

    public function testGetGroupNotFound()
    {
        $this->expectException(SoldoBadRequestException::class);

        $this->soldo->getGroup('A_NOT_EXISTING_GROUP_ID');
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
        $this->assertIsArray($employees);
        $this->assertTrue(count($employees) > 0, 'There should be at least one Employee');

        foreach ($employees as $employee) {
            /** @var Employee $employ  */
            $this->assertInstanceOf(Employee::class, $employee);
        }
    }

    public function testGetEmployeeNotFound()
    {
        $this->expectException(SoldoModelNotFoundException::class);

        $this->soldo->getEmployee('A_NOT_EXISTING_EMPLOYEE_ID');
    }

    public function testGetEmployee()
    {
        $employeeId = $this->getEmployeeId();
        $employee = $this->soldo->getEmployee($employeeId);
        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals($employeeId, $employee->id);
    }

    public function testUpdateEmployeeeEmptyData()
    {
        $this->expectException(InvalidArgumentException::class);

        $employeeId = $this->getEmployeeId();

        $this->soldo->updateEmployee($employeeId, []);
    }

    public function testUpdateEmployBlacklistedData()
    {
        $this->expectException(InvalidArgumentException::class);

        $employeeId = $this->getEmployeeId();
        $this->soldo->updateEmployee($employeeId, ['a_not_whitelisted_key' => 'Random Value']);
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
        $this->assertIsArray($transactions);
        $this->assertTrue(count($transactions) > 0, 'There should be at least one Transaction');

        foreach ($transactions as $transaction) {
            /** @var Transaction $transaction */
            $this->assertInstanceOf(Transaction::class, $transaction);
        }
    }

    public function testGetTransactionNotFound()
    {
        $this->expectException(SoldoBadRequestException::class);

        $this->soldo->getTransaction('A_NOT_EXISTING_TRANSACTION_ID');
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
        $this->assertIsArray($transaction->details);
    }

    public function testGetCards()
    {
        $cards = $this->soldo->getCards();
        $this->assertIsArray($cards);
        $this->assertTrue(count($cards) > 0, 'There should be at least one Card');

        foreach ($cards as $card) {
            /** @var Card $card */
            $this->assertInstanceOf(Card::class, $card);
        }
    }

    public function testGetCardNotFound()
    {
        $this->expectException(SoldoModelNotFoundException::class);

        $this->soldo->getCard('A_NOT_EXISTING_CARD_ID');
    }

    public function testGetCard()
    {
        $cardId = $this->getCardId();
        $card = $this->soldo->getCard($cardId);
        $this->assertInstanceOf(Card::class, $card);
        $this->assertEquals($cardId, $card->id);
    }

    public function testGetCardRulesNotFound()
    {
        $this->expectException(SoldoModelNotFoundException::class);

        $this->soldo->getCardRules('A_NOT_EXISTING_CARD_ID');
    }

    public function testGetCardRules()
    {
        $cardId = $this->getCardId();
        $rules = $this->soldo->getCardRules($cardId);
        $this->assertIsArray($rules);
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

    public function testPerformTransferToNotExistingWallet()
    {
        $this->expectException(SoldoInternalTransferException::class);

        $walletIds = $this->getWalletsId();
        $this->soldo->transferMoney(
            $walletIds['id_with_available_amount'],
            'ANOTHER_NOT_EXISTING_WALLET',
            1,
            'EUR',
            'token'
        );
    }

    public function testPerformTransferFromNotExistingWallet()
    {
        $this->expectException(SoldoInternalTransferException::class);

        $walletIds = $this->getWalletsId();
        $this->soldo->transferMoney(
            'ANOTHER_NOT_EXISTING_WALLET',
            $walletIds['id'],
            1,
            'EUR',
            'token'
        );
    }

    public function testPerformTransferWithInvalidAmount()
    {
        $this->expectException(SoldoInternalTransferException::class);

        $walletIds = $this->getWalletsId();
        $this->soldo->transferMoney(
            $walletIds['id_with_available_amount'],
            $walletIds['id'],
            9999999,
            'EUR',
            'token'
        );
    }

    public function testPerformTransferWithInvalidCurrencyCode()
    {
        $this->expectException(SoldoInternalTransferException::class);

        $walletIds = $this->getWalletsId();
        $this->soldo->transferMoney(
            $walletIds['id_with_available_amount'],
            $walletIds['id'],
            1,
            'NOT_EXISTING_CURRENCY_CODE',
            'token'
        );
    }

    public function testPerformTransferWithInvalidInternalToken()
    {
        $this->expectException(SoldoInternalTransferException::class);

        $walletIds = $this->getWalletsId();
        $this->soldo->transferMoney(
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
            getenv('INTERNAL_TOKEN')
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
