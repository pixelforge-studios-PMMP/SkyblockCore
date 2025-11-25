<?php

declare(strict_types=1);

namespace Biswajit\Core\Menus\bank;

use Biswajit\Core\API;
use Biswajit\Core\Managers\BankManager;
use Biswajit\Core\Managers\EconomyManager;
use Biswajit\Core\Skyblock;
use pocketmine\player\Player;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;

class PayLoanMenu extends CustomForm
{
    public function __construct(Player $player)
    {
        $loan = BankManager::getLoan($player);
        parent::__construct(
            "§bPay §3Loan",
            [
                new Input("amount", "Please Enter A Numric Value", "", "$loan"),
                new Label("label", "§eTotal Loan§7: §b$loan")
            ],
            function (Player $player, CustomFormResponse $response) use ($loan): void {
                $result = $response->getString("amount");

                if (!is_numeric($result)) {
                    $player->sendMessage(Skyblock::$prefix . API::getMessage("loan-error-number"));
                    return;
                }

                if ($result < 1) {
                    $player->sendMessage(Skyblock::$prefix . API::getMessage("loan-error-pay", ["{amount}" => (string)$result]));
                    return;
                }

                if ($loan < 1) {
                    $player->sendMessage(Skyblock::$prefix . API::getMessage("loan-no-loan"));
                    return;
                }

                if ($result > $loan) {
                    $player->sendMessage(Skyblock::$prefix . API::getMessage("loan-error-unable", ["{amount}" => (string)$result]));
                    return;
                }

                if (EconomyManager::getMoney($player) < $result) {
                    $player->sendMessage(Skyblock::$prefix . API::getMessage("loan-no-money", ["{amount}" => (string)$result]));
                    return;
                }

                BankManager::reduceLoan($player, (float)$result);
                EconomyManager::subtractMoney($player, (float)$result);
                $player->sendMessage(Skyblock::$prefix . API::getMessage("loan-success", ["{amount}" => (string)$result]));

                if (BankManager::getLoan($player) < 1) {
                    BankManager::setLoanTime($player, 0);
                }
            }
        );
    }
}
