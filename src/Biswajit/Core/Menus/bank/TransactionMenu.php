<?php

namespace Biswajit\Core\Menus\bank;

use Biswajit\Core\Managers\BankManager;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;

class TransactionMenu extends MenuForm
{
    public function __construct(Player $player)
    {
        parent::__construct("§fTransactions Menu", implode("\n", BankManager::getTransactions($player)), [
            new MenuOption("§cBack", new FormIcon("textures/ui/icon_import", FormIcon::IMAGE_TYPE_PATH)),
        ], function (Player $sender, int $selected): void {
            switch ($selected) {
                case 0:
                    $sender->sendForm(new BankMenu($sender));
                    break;
            }
        });
    }
}
