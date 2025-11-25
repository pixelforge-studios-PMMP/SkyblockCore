<?php

namespace Biswajit\Core\Menus\warp;

use Biswajit\Core\API;
use pocketmine\Server;
use pocketmine\world\World;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use pocketmine\world\Position;
use dktapps\pmforms\MenuOption;

class WarpForm extends MenuForm
{
    public function __construct()
    {
        parent::__construct(
            "§6§lWARPS",
            "",
            [
                new MenuOption("§l§bPVP\n§l§9»» §r§oTap to open", new FormIcon("https://cdn-icons-png.flaticon.com/128/1496/1496125.png", FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§l§bFOREST\n§l§9»» §r§oTap to open", new FormIcon("https://cdn-icons-png.flaticon.com/128/3277/3277595.png", FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§l§bMINE\n§l§9»» §r§oTap to open", new FormIcon("https://cdn-icons-png.flaticon.com/128/4080/4080723.png", FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§l§bFARM\n§l§9»» §r§oTap to open", new FormIcon("https://cdn-icons-png.flaticon.com/128/2921/2921855.png", FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§l§bGRAVEYARD\n§l§9»» §r§oTap to open", new FormIcon("https://cdn-icons-png.flaticon.com/128/4321/4321459.png", FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§l§bLIFT UI\n§l§9»» §r§oTap to open", new FormIcon("https://cdn-icons-png.flaticon.com/128/3321/3321009.png", FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§l§bISLAND\n§l§9»» §r§oTap to open", new FormIcon("https://cdn-icons-png.flaticon.com/128/4617/4617270.png", FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§l§bHUB\n§l§9»» §r§oTap to open", new FormIcon("https://cdn-icons-png.flaticon.com/128/602/602182.png", FormIcon::IMAGE_TYPE_URL))
            ],
            function (Player $player, int $selected): void {
                $world = Server::getInstance()->getWorldManager()->getWorldByName(API::getHub());
                switch ($selected) {
                    case 0:
                        $player->sendMessage(" §eComing Soon!");
                        break;
                    case 1:
                        $player->teleport(new Position(-27, 57, -163, $world));
                        $player->sendTitle("§e§lFOREST");
                        break;
                    case 2:
                        $player->teleport(new Position(91, 62, 158, $world));
                        $player->sendTitle("§e§lMINE");
                        break;
                    case 3:
                        $player->teleport(new Position(-59, 58, -51, $world));
                        $player->sendTitle("§e§lFARM");
                        break;
                    case 4:
                        $player->teleport(new Position(110, 58, -52, $world));
                        $player->sendTitle("§e§lGRAVEYARD");
                        break;
                    case 5:
                        $player->sendForm(new LiftUiForm());
                        break;
                    case 6:
                        Server::getInstance()->dispatchCommand($player, "join");
                        $player->sendTitle("§e§lISLAND");
                        break;
                    case 7:
                        Server::getInstance()->dispatchCommand($player, "hub");
                        break;
                }
            }
        );
    }
}
