<?php

namespace Biswajit\Core\Menus\anvil;

use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use dktapps\pmforms\MenuOption;

class AnvilMainForm extends MenuForm
{
    public function __construct()
    {
        parent::__construct(
            "§l§eAnivilUI",
            "§eRepair/Rename\n",
            [
                new MenuOption("§l§3REPAIR\n§l§9»» §r§oTap to open", new FormIcon("https://cdn-icons-png.flaticon.com/128/3014/3014275.png", FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§l§3RENAME\n§l§9»» §r§oTap to open", new FormIcon("https://cdn-icons-png.flaticon.com/128/5136/5136887.png", FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§l§3EXIT\n§l§9»» §r§oTap to exit", new FormIcon("https://cdn-icons-png.flaticon.com/128/3094/3094700.png", FormIcon::IMAGE_TYPE_URL))
            ],
            function (Player $player, int $selected): void {
                switch ($selected) {
                    case 0:
                        $player->sendForm(new AnvilRepairForm($player));
                        break;
                    case 1:
                        $player->sendForm(new AnvilRenameForm($player));
                        break;
                    case 2:
                        //nothing
                        break;
                }
            }
        );
    }
}
