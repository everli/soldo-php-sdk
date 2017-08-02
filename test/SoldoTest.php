<?php

namespace Soldo\Tests;

use PHPUnit\Framework\TestCase;
use Soldo\Resources\Card;
use Soldo\Resources\Company;
use Soldo\Resources\Employee;
use Soldo\Resources\ExpenseCentre;
use Soldo\Resources\Rule;
use Soldo\Resources\Transaction;
use Soldo\Resources\Wallet;
use Soldo\Soldo;

class SoldoTest extends TestCase
{

    /** @var Soldo $soldo */
    private static $soldo;

    /** @var string $walletId */
    private static $walletId;

    /** @var string $walletIdWithAvailableAmount */
    private static $walletIdWithAvailableAmount;

    /** @var  string $expenseCentreId  */
    private static $expenseCentreId;

    /** @var string $employeeId */
    private static $employeeId;

    /** @var string $transactionId */
    private static $transactionId;

    /** @var string $cardId */
    private static $cardId;

    public static function setUpBeforeClass()
    {
        self::$soldo = new Soldo(
            [
                'client_id' => SoldoTestCredentials::CLIENT_ID,
                'client_secret' => SoldoTestCredentials::CLIENT_SECRET,
                'environment' => 'demo',
            ]
        );
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoSDKException
     * @expectedExceptionMessage Required "client_id" key is missing in config
     */
    public function testConstructorWithoutConfig()
    {
        $s = new Soldo([]);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoSDKException
     * @expectedExceptionMessage Required "client_id" key is missing in config
     */
    public function testConstructorWithoutClientIdParam()
    {
        $s = new Soldo(['client_secret' => 'FOO']);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoSDKException
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

    public function testGetWallets()
    {
        $wallets = self::$soldo->getWallets();
        $this->assertInternalType('array', $wallets);
        $this->assertTrue(count($wallets) > 1, 'There should be at least two Wallet'); //TODO: verify that this assertion is true

        /** @var Wallet $wallet */
        $wallet = $wallets[0];
        $this->assertInternalType('string', $wallet->id);
        self::$walletId = $wallet->id;

        foreach ($wallets as $wallet) {
            /** @var Wallet $wallet  */
            $this->assertInstanceOf(Wallet::class, $wallet);

            // store the id of a wallet with available amount greater than 0
            // so later I can test the transfer method
            if ($wallet->available_amount > 0 && $wallet->id != self::$walletId) {
                self::$walletIdWithAvailableAmount = $wallet->id;
            }
        }
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoModelNotFoundException
     */
    public function testGetWalletNotFound()
    {
        $wallet = self::$soldo->getWallet('A_NOT_EXISTING_WALLET_ID');
    }

    public function testGetWallet()
    {
        $wallet = self::$soldo->getWallet(self::$walletId);
        $this->assertInstanceOf(Wallet::class, $wallet);
        $this->assertEquals(self::$walletId, $wallet->id);
    }

    public function testGetExpenseCentres()
    {
        $expenseCentres = self::$soldo->getExpenseCentres();
        $this->assertInternalType('array', $expenseCentres);
        $this->assertTrue(count($expenseCentres) > 0, 'There should be at least one Expense Centre');

        /** @var ExpenseCentre $expenseCentre*/
        $expenseCentre = $expenseCentres[0];
        $this->assertInternalType('string', $expenseCentre->id);
        self::$expenseCentreId = $expenseCentre->id;

        foreach ($expenseCentres as $expenseCentre) {
            /** @var ExpenseCentre $expenseCentre*/
            $this->assertInstanceOf(ExpenseCentre::class, $expenseCentre);
        }
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoModelNotFoundException
     */
    public function testGetExpenseCentreNotFound()
    {
        $expenseCentre = self::$soldo->getExpenseCentre('A_NOT_EXISTING_EXPENSE_CENTRE_ID');
    }

    public function testGetExpenseCentre()
    {
        $expenseCentre = self::$soldo->getExpenseCentre(self::$expenseCentreId);
        $this->assertInstanceOf(ExpenseCentre::class, $expenseCentre);
        $this->assertEquals(self::$expenseCentreId, $expenseCentre->id);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateExpenseCentreEmptyData()
    {
        $expenseCentre = self::$soldo->updateExpenseCentre(self::$expenseCentreId, []);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateExpenseCentreBlacklistedData()
    {
        $expenseCentre = self::$soldo->updateExpenseCentre(self::$expenseCentreId, ['a_not_whitelisted_key' => 'Random Value']);
    }

    public function testUpdateExpenseCentre()
    {
        $expenseCentre = self::$soldo->updateExpenseCentre(self::$expenseCentreId, ['assignee' => 'Random Assignee']);
        $this->assertInstanceOf(ExpenseCentre::class, $expenseCentre);
        $this->assertEquals('Random Assignee', $expenseCentre->assignee);
    }

    public function testUpdateExpenseCentreWithBlacklistedData()
    {
        $expenseCentre = self::$soldo->updateExpenseCentre(self::$expenseCentreId, ['assignee' => 'Another Random Assignee', 'a_not_whitelisted_key' => 'Random Value']);
        $this->assertInstanceOf(ExpenseCentre::class, $expenseCentre);
        $this->assertEquals('Another Random Assignee', $expenseCentre->assignee);
    }

    public function testGetEmployees()
    {
        $employees = self::$soldo->getEmployees();
        $this->assertInternalType('array', $employees);
        $this->assertTrue(count($employees) > 0, 'There should be at least one Employee'); //TODO: verify that this assertion is true

        /** @var Employee $employee */
        $employee = $employees[0];
        $this->assertInternalType('string', $employee->id);
        self::$employeeId = $employee->id;

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
        $employee = self::$soldo->getEmployee('A_NOT_EXISTING_EMPLOYEE_ID');
    }

    public function testGetEmployee()
    {
        $employee = self::$soldo->getEmployee(self::$employeeId);
        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals(self::$employeeId, $employee->id);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateEmployeeeEmptyData()
    {
        $employee = self::$soldo->updateEmployee(self::$employeeId, []);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateEmployBlacklistedData()
    {
        $employee = self::$soldo->updateEmployee(self::$employeeId, ['a_not_whitelisted_key' => 'Random Value']);
    }

    public function testUpdateEmployee()
    {
        $employee = self::$soldo->updateEmployee(self::$employeeId, ['department' => 'Random Department']);
        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals('Random Department', $employee->department);
    }

    public function testUpdateEmployeeWithBlacklistedData()
    {
        $employee = self::$soldo->updateEmployee(self::$employeeId, ['department' => 'Another Random Department', 'a_not_whitelisted_key' => 'Random Value']);
        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals('Another Random Department', $employee->department);
    }

    public function testGetTransactions()
    {
        $transactions = self::$soldo->getTransactions();
        $this->assertInternalType('array', $transactions);
        $this->assertTrue(count($transactions) > 0, 'There should be at least one Transaction'); //TODO: verify that this assertion is true

        /** @var Employee $employee */
        $transaction = $transactions[0];
        $this->assertInternalType('string', $transaction->id);
        self::$transactionId = $transaction->id;

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
        $transaction = self::$soldo->getTransaction('A_NOT_EXISTING_TRANSACTION_ID');
    }

    public function testGetTransaction()
    {
        $transaction = self::$soldo->getTransaction(self::$transactionId);
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals(self::$transactionId, $transaction->id);
        $this->assertNull($transaction->details);
    }

    public function testGetTransactionWithDetails()
    {
        $transaction = self::$soldo->getTransaction(self::$transactionId, true);
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals(self::$transactionId, $transaction->id);
        $this->assertNotNull($transaction->details);
        $this->assertInternalType('array', $transaction->details);
    }

    public function testGetCards()
    {
        $cards = self::$soldo->getCards();
        $this->assertInternalType('array', $cards);
        $this->assertTrue(count($cards) > 0, 'There should be at least one Card'); //TODO: verify that this assertion is true

        /** @var Card $card */
        $card = $cards[0];
        $this->assertInternalType('string', $card->id);
        self::$cardId = $card->id;

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
        $card = self::$soldo->getCard('A_NOT_EXISTING_CARD_ID');
    }

    public function testGetCard()
    {
        $card = self::$soldo->getCard(self::$cardId);
        $this->assertInstanceOf(Card::class, $card);
        $this->assertEquals(self::$cardId, $card->id);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoModelNotFoundException
     */
    public function testGetCardRulesNotFound()
    {
        $rules = self::$soldo->getCardRules('A_NOT_EXISTING_CARD_ID');
    }

    public function testGetCardRules()
    {
        $rules = self::$soldo->getCardRules(self::$cardId);
        $this->assertInternalType('array', $rules);
        $this->assertTrue(count($rules) > 0, 'There should be at least one Rule for the required card'); //TODO: verify that this assertion is true

        foreach ($rules as $rule) {
            /** @var Rule $rule */
            $this->assertInstanceOf(Rule::class, $rule);
        }
    }

    public function testGetCompany()
    {
        $company = self::$soldo->getCompany();
        $this->assertInstanceOf(Company::class, $company);
    }

    /**
     * @expectedException \Soldo\Exceptions\SoldoInternalTransferException
     */
    public function testPerformTransferToNotExistingWallet()
    {
        $transfer = self::$soldo->transferMoney(
            self::$walletIdWithAvailableAmount,
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
        $transfer = self::$soldo->transferMoney(
            self::$walletIdWithAvailableAmount,
            'ANOTHER_NOT_EXISTING_WALLET',
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
        $transfer = self::$soldo->transferMoney(
            self::$walletIdWithAvailableAmount,
            self::$walletId,
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
        $transfer = self::$soldo->transferMoney(
            self::$walletIdWithAvailableAmount,
            self::$walletId,
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
        $transfer = self::$soldo->transferMoney(
            self::$walletIdWithAvailableAmount,
            self::$walletId,
            1,
            'EUR',
            '12345678910QAZWSXEDCRFVTGBYHNUJMIKOLP'
        );
    }

    public function testPerformTransfer()
    {
        $amountToTransfer = 1;

        $fromWallet = self::$soldo->getWallet(self::$walletIdWithAvailableAmount);
        $toWallet = self::$soldo->getWallet(self::$walletId);

        $availableAmountOfFromWallet = $fromWallet->available_amount;
        $availableAmountOfToWallet = $toWallet->available_amount;

        $transfer = self::$soldo->transferMoney(
            self::$walletIdWithAvailableAmount,
            self::$walletId,
            $amountToTransfer,
            'EUR',
            SoldoTestCredentials::INTERNAL_TOKEN
        );

        $availableAmountOfFromWalletAfter = $transfer->from_wallet->available_amount;
        $availableAmountOfToWalletAfter = $transfer->to_wallet->available_amount;

        $this->assertEquals($availableAmountOfFromWallet - $amountToTransfer, $availableAmountOfFromWalletAfter);
        $this->assertEquals($availableAmountOfToWallet + $amountToTransfer, $availableAmountOfToWalletAfter);

        // double check just to be sure
        $fromWallet = self::$soldo->getWallet(self::$walletIdWithAvailableAmount);
        $toWallet = self::$soldo->getWallet(self::$walletId);

        $this->assertEquals($availableAmountOfFromWallet - $amountToTransfer, $fromWallet->available_amount);
        $this->assertEquals($availableAmountOfToWallet + $amountToTransfer, $toWallet->available_amount);
    }
}
