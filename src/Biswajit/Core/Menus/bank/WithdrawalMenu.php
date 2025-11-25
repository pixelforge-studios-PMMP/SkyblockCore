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

class WithdrawalMenu extends MenuForm
{
    public function __construct(Player $player)
    {
        parent::__construct("§l§cWITHDRAW", "§r§eBalance: §r§6$ " . BankManager::getBankMoney($player), [
            new MenuOption("§l§bWITHDRAW ALL\n§l§d» §r§8Click To Withdraw", new FormIcon("textures/icon/withdrawall", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§l§bWITHDRAW HALF\n§l§d» §r§8Click To Withdraw", new FormIcon("textures/icon/withdrawall", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§l§bWITHDRAW CUSTOM\n§l§d» §r§8Click To Open", new FormIcon("textures/icon/withdrawall", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§cBACK", new FormIcon("textures/ui/icon_import", FormIcon::IMAGE_TYPE_PATH)),
        ], function (Player $sender, int $selected): void {
            switch ($selected) {
                case 0:
                    if (BankManager::getBankMoney($sender) == 0) {
                        $sender->sendMessage(Skyblock::$prefix . API::getMessage("bank.nowithdral"));
                        return;
                    }

                    EconomyManager::addMoney($sender, BankManager::getBankMoney($sender));
                    $sender->sendMessage(Skyblock::$prefix . API::getMessage("bank.withdraw-seccess", ["{AMOUNT}" => (string)BankManager::getBankMoney($sender)]));
                    BankManager::reduceBankMoney($sender, BankManager::getBankMoney($sender));
                    break;
                case 1:
                    if (BankManager::getBankMoney($sender) === 0) {
                        $sender->sendMessage(Skyblock::$prefix . API::getMessage("bank.nowithdral"));
                        return;
                    }

                    EconomyManager::addMoney($sender, BankManager::getBankMoney($sender) / 2);
                    $sender->sendMessage(Skyblock::$prefix . API::getMessage("bank.withdraw-seccess", ["{AMOUNT}" => (string)(BankManager::getBankMoney($sender) / 2)]));
                    BankManager::reduceBankMoney($sender, BankManager::getBankMoney($sender) / 2);
                    break;
                case 2:
                    $sender->sendForm(new CustomWithdrawalMenu($sender));
                    break;
                case 3:
                    $sender->sendForm(new BankMenu($sender));
                    break;
            }
        });
    }
}
