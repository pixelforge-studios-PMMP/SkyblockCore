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

class CustomDepositMenu extends CustomForm
{
    public function __construct(Player $player)
    {

        parent::__construct(
            "§l§cCUSTOM DEPOSIT",
            [
                new Input("amount", "§rEnter max", "100000"),
                new Label("label", "§r§eBalance: §r§6$ " . EconomyManager::getMoney($player))
            ],
            function (Player $player, CustomFormResponse $response): void {
                $input = $response->getString("amount");

                if (EconomyManager::getMoney($player) == 0) {
                    $player->sendMessage(Skyblock::$prefix . API::getMessage("bank.nodeposit"));
                    return;
                }

                if (EconomyManager::getMoney($player) < $input) {
                    $player->sendMessage(Skyblock::$prefix . API::getMessage("bank.nodeposit"));
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

                BankManager::addBankMoney($player, (float) $input);
                $player->sendMessage(Skyblock::$prefix . API::getMessage("bank.deposit-seccess", ["{AMOUNT}" => (string)$input]));
                EconomyManager::subtractMoney($player, (float) $input);
            }
        );
    }
}
