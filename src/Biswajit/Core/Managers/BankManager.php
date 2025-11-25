<?php

declare(strict_types=1);

namespace Biswajit\Core\Managers;

use Biswajit\Core\Player;
use Biswajit\Core\Skyblock;
use Biswajit\Core\Tasks\InterestTask;

class BankManager
{
    use ManagerBase;

    public static array $interest = [];

    public static function getBankMoney(Player $player): float
    {
        return $player->getEconomy("bank-money") ?? 0;
    }

    public static function addBankMoney(Player $player, float $amount): void
    {
        $player->setEconomy("bank-money", self::getBankMoney($player) + $amount);
        self::setTransactions($player, "Deposit " . $amount);

        if (!array_key_exists($player->getName(), self::$interest)) {
            self::$interest[$player->getName()] =  Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new InterestTask(Skyblock::getInstance(), $player), 72000);
        }
    }

    public static function reduceBankMoney(Player $player, float $amount): void
    {
        $player->setEconomy("bank-money", self::getBankMoney($player) - $amount);
        self::setTransactions($player, "Withdraw " . $amount);

        if (self::getBankMoney($player) - $amount <= 0) {
            if (array_key_exists($player->getName(), self::$interest)) {
                self::$interest[$player->getName()]->cancel();
            }
        }
    }

    public static function getLoanMerit(Player $player): int
    {
        return $player->getEconomy("bank-merit") ?? 100;
    }

    public static function addLoanMerit(Player $player, int $amount): void
    {
        $player->setEconomy("bank-merit", self::getLoanMerit($player) + $amount);
    }

    public static function getLoan(Player $player): float
    {
        return $player->getEconomy("bank-loan") ?? 0;
    }

    public static function addLoan(Player $player, float $amount): void
    {
        $player->setEconomy("bank-loan", self::getLoan($player) + $amount);
        self::setTransactions($player, "Loan Add " . $amount);
    }

    public static function reduceLoan(Player $player, float $amount): void
    {
        $player->setEconomy("bank-loan", self::getLoan($player) - $amount);
        self::setTransactions($player, "Pay Loan " . $amount);
    }

    public static function setLoanTime(Player $player, int $time): void
    {
        $player->setEconomy("bank-time", $time);
    }

    public static function getLoanTime(Player $player): int
    {
        return $player->getEconomy("bank-time") ?? 0;
    }

    public static function recoverLoan(Player $player): void
    {
        $player->setEconomy("bank-money", 0);
        EconomyManager::setMoney($player, 0);
        $player->setEconomy("bank-merit", 0);
        //todo punish the player
    }

    public static function getTransactions(Player $player): array
    {
        $data = $player->getEconomy("bank-transaction") ?? json_encode(["§cYou have not made any transactions yet!"]);
        return json_decode($data, true);
    }

    public static function setTransactions(Player $player, $transaction): void
    {
        $transactions = self::getTransactions($player);
        $transactions[] = date("§b[d/m/y]") . "§e - " . $transaction;
        $player->setEconomy("bank-transaction", json_encode($transactions));
    }
}
