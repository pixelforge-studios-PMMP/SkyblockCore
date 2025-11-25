<?php

namespace Biswajit\Core\Menus\warp;

use Biswajit\Core\API;
use pocketmine\world\World;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use pocketmine\world\Position;
use dktapps\pmforms\MenuOption;
use pocketmine\Server;

class LiftUiForm extends MenuForm
{
    public function __construct()
    {
        parent::__construct("§l§eMagic Skyblock Lift Operator", "§6Please Select The Cave You Want To Telport", [
            new MenuOption("§bIron Mine\n§9»» §r§oTap to Teleport", new FormIcon("textures/items/iron_ingot", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§bGold Mine\n§9»» §r§oTap to Teleport", new FormIcon("textures/items/gold_ingot", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§bRedstone Mine\n§9»» §r§oTap to Teleport", new FormIcon("textures/items/redstone_dust", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§bLapis Mine\n§9»» §r§oTap to Teleport", new FormIcon("textures/items/dye_powder_blue", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§bEmerald Mine\n§9»» §r§oTap to Teleport", new FormIcon("textures/items/emerald", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§bDiamond Mine\n§9»» §r§oTap to Teleport", new FormIcon("textures/items/diamond", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§bSantuary\n§9»» §r§oTap to Teleport", new FormIcon("textures/blocks/obsidian", FormIcon::IMAGE_TYPE_PATH))
        ], function (Player $sender, int $selected): void {
            $world = Server::getInstance()->getWorldManager()->getWorldByName(API::getHub());
            if (!$world instanceof World) {
                return;
            }

            switch ($selected) {
                case 0:
                    $sender->teleport(new Position(161, 99, 390, $world));
                    $sender->sendTitle("§lIRON MINE");
                    break;
                case 1:
                    $sender->teleport(new Position(161, 99, 390, $world));
                    $sender->sendTitle("§6§lGOLD MINE");
                    break;
                case 2:
                    $sender->teleport(new Position(192, 69, 418, $world));
                    $sender->sendTitle("§c§lREDSTONE MINE");
                    break;
                case 3:
                    $sender->teleport(new Position(190, 83, 414, $world));
                    $sender->sendTitle("§9§lLAPIS MINE");
                    break;
                case 4:
                    $sender->teleport(new Position(193, 53, 415, $world));
                    $sender->sendTitle("§a§lEMERALD MINE");
                    break;
                case 5:
                    $sender->teleport(new Position(191, 31, 417, $world));
                    $sender->sendTitle("§l§bDIAMOND MINE");
                    break;
                case 6:
                    $sender->teleport(new Position(191, 14, 417, $world));
                    $sender->sendTitle("§e§lSANTUARY");
                    break;
            }
        });
    }
}
