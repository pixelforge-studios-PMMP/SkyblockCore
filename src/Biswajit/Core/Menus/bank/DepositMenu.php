<?php

namespace Biswajit\Core\Menus\bank;

use Biswajit\Core\API;
use Biswajit\Core\Managers\BankManager;
use Biswajit\Core\Managers\EconomyManager;
use Biswajit\Core\Skyblock;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;

class DepositMenu extends MenuForm
{
    public function __construct(Player $player)
    {
        parent::__construct("§l§cDEPOSIT", "§r§eBalance: §r§6$ " . EconomyManager::getMoney($player), [
            new MenuOption("§l§bDEPOSIT ALL\n§l§d» §r§8Click To Deposit", new FormIcon("textures/icon/depositall", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§l§bDEPOSIT HALF\n§l§d» §r§8Click To Deposit", new FormIcon("textures/icon/depositall", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§l§bDEPOSIT CUSTOM\n§l§d» §r§8Click To Deposit", new FormIcon("textures/icon/depositall", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§cBACK", new FormIcon("textures/ui/icon_import", FormIcon::IMAGE_TYPE_PATH)),
        ], function (Player $sender, int $selected): void {
            switch ($selected) {
                case 0:
                    if (EconomyManager::getMoney($sender) == 0) {
                        $sender->sendMessage(Skyblock::$prefix . API::getMessage("bank.nodeposit"));
                        return;
                    }

                    $sender->sendMessage(Skyblock::$prefix . API::getMessage("bank.deposit-seccess", ["{AMOUNT}" => (string)EconomyManager::getMoney($sender)]));
                    BankManager::addBankMoney($sender, EconomyManager::getMoney($sender));
                    EconomyManager::subtractMoney($sender, EconomyManager::getMoney($sender));
                    break;
                case 1:
                    if (EconomyManager::getMoney($sender) == 0) {
                        $sender->sendMessage(Skyblock::$prefix . API::getMessage("bank.nodeposit"));
                        return;
                    }

                    $sender->sendMessage(Skyblock::$prefix . API::getMessage("bank.deposit-seccess", ["{AMOUNT}" => (string)(EconomyManager::getMoney($sender) / 2)]));
                    BankManager::addBankMoney($sender, EconomyManager::getMoney($sender) / 2);
                    EconomyManager::subtractMoney($sender, EconomyManager::getMoney($sender) / 2);
                    break;
                case 2:
                    $sender->sendForm(new CustomDepositMenu($sender));
                    break;
                case 3:
                    $sender->sendForm(new BankMenu($sender));
                    break;
            }
        });
    }
}
