<?php

namespace Biswajit\Core\Menus\grindstone;

use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use dktapps\pmforms\MenuOption;

class GrindStoneForm extends MenuForm
{
    public function __construct()
    {
        parent::__construct(
            "§l§eGRINDSTONE",
            "§dTap To Remove Enchantment Of Item Hold In Your Hand",
            [
                new MenuOption("§l§eREMOVE ENCHANTMENT\n§9»» §r§6Tap To Remove", new FormIcon("https://cdn-icons-png.flaticon.com/128/2466/2466942.png", FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§cBack\n§9»» §r§cTap To Go Back", new FormIcon("textures/ui/redX1", FormIcon::IMAGE_TYPE_PATH))
            ],
            function (Player $player, int $selected): void {
                switch ($selected) {
                    case 0:
                        $item = $player->getInventory()->getItemInHand();
                        $item->removeEnchantments();
                        $player->sendMessage(" §7Item Enchantment Removed!");
                        break;

                    case 1:
                        break;
                }
            }
        );
    }
}
