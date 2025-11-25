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

class CustomWithdrawalMenu extends CustomForm
{
    public function __construct(Player $player)
    {

        parent::__construct(
            "§l§cCUSTOM WITHDRAW",
            [
                new Input("amount", "§rEnter max", "100000"),
                new Label("label", "§r§eBalance: §r§6$ " . BankManager::getBankMoney($player))
            ],
            function (Player $player, CustomFormResponse $response): void {
                $input = $response->getString("amount");

                if (BankManager::getBankMoney($player) == 0) {
                    $player->sendMessage(Skyblock::$prefix . API::getMessage("bank.nowithdral"));
                    return;
                }

                if (BankManager::getBankMoney($player) < $input) {
                    $player->sendMessage(Skyblock::$prefix . API::getMessage("bank.nowithdral"));
                    return;
                }

                if (!is_numeric($input)) {
                    $player->sendMessage(Skyblock::$prefix . API::getMessage("bank.wrong-amount"));
                    return;
                }

                if ($input <= 0) {
                    $player->sendMessage(Skyblock::$prefix . API::getMessage("bank.vaild-amount"));
                    return;
                }

                EconomyManager::addMoney($player, (float)$input);
                $player->sendMessage(Skyblock::$prefix . API::getMessage("bank.withdraw-seccess", ["{AMOUNT}" => (string)$input]));
                BankManager::reduceBankMoney($player, (float)$input);
            }
        );
    }
}
