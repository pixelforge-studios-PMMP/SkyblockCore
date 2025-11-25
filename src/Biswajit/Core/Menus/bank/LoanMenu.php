<?php

namespace Biswajit\Core\Menus\bank;

use Biswajit\Core\Managers\BankManager;
use Biswajit\Core\Utils\Utils;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;

class LoanMenu extends MenuForm
{
    public function __construct(Player $player)
    {
        $merit = BankManager::getLoanMerit($player);
        $loan = BankManager::getLoan($player);
        $time = BankManager::getLoanTime($player) !== 0 ? Utils::changeNumericFormat(BankManager::getLoanTime($player) - time(), "time") : 0;
        parent::__construct("§fLoan Menu", "§eMerit§7: §a$merit\n§eCurrent Loan§7: §a$loan\n§eRemaining Time§7: §a$time", [
            new MenuOption("§aAcquire Loan", new FormIcon("textures/items/dye_powder_blue", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§aPay Loan", new FormIcon("textures/items/gold_ingot", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§cBack", new FormIcon("textures/items/arrow", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§cExit", new FormIcon("textures/items/redstone_dust", FormIcon::IMAGE_TYPE_PATH)),
        ], function (Player $sender, int $selected): void {
            switch ($selected) {
                case 0:
                    $sender->sendForm(new AquireLoanMenu($sender));
                    break;
                case 1:
                    $sender->sendForm(new PayLoanMenu($sender));
                    break;
                case 2:
                    $sender->sendForm(new BankMenu($sender));
                    break;
                case 3:
                    // nathing
                    break;
            }
        });
    }
}
