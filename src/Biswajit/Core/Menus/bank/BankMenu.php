<?php

namespace Biswajit\Core\Menus\bank;

use Biswajit\Core\Managers\BankManager;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;

class BankMenu extends MenuForm
{
    public function __construct(Player $player)
    {
        parent::__construct("§fBank Menu", "§r§eBalance: §r§6$ " . BankManager::getBankMoney($player), [
            new MenuOption("§l§bWITHDRAW MONEY\n§l§d» §r§8Click To Withdraw", new FormIcon("textures/icon/withdraw", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§l§bDEPOSIT MONEY\n§l§d» §r§8Click To Deposit", new FormIcon("textures/icon/deposit", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§l§bPARSONAL LOAN\n§l§d» §r§8Click To Loan", new FormIcon("textures/icon/loan", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§l§bTRANSACTIONS\n§l§d» §r§8Click To Transactions", new FormIcon("textures/icon/transaction", FormIcon::IMAGE_TYPE_PATH)),
        ], function (Player $sender, int $selected): void {
            switch ($selected) {
                case 0:
                    $sender->sendForm(new WithdrawalMenu($sender));
                    break;
                case 1:
                    $sender->sendForm(new DepositMenu($sender));
                    break;
                case 2:
                    $sender->sendForm(new LoanMenu($sender));
                    break;
                case 3:
                    $sender->sendForm(new TransactionMenu($sender));
                    break;
            }
        });
    }
}
