<?php

namespace Biswajit\Core\Menus\size;

use Biswajit\Core\Skyblock;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use dktapps\pmforms\MenuOption;

class SizeForm extends MenuForm
{
    public function __construct(Player $player)
    {
        $size = $player->getScale();
        parent::__construct(
            "§l§bSizeUI",
            "§6Your size: §3" . $size,
            [
                new MenuOption("§a0.5\n§eSets your size to 0.5!", new FormIcon("textures/ui/down_arrow", FormIcon::IMAGE_TYPE_PATH)),
                new MenuOption("§a0.75\n§eSets your size to 0.75!", new FormIcon("textures/ui/down_arrow", FormIcon::IMAGE_TYPE_PATH)),
                new MenuOption("§a1.25\n§eSets your size to 1.25!", new FormIcon("textures/ui/up_arrow", FormIcon::IMAGE_TYPE_PATH)),
                new MenuOption("§a1.5\n§eSets your size to 1.5!", new FormIcon("textures/ui/up_arrow", FormIcon::IMAGE_TYPE_PATH)),
                new MenuOption("§gReset%\n§eSets your size to 1", new FormIcon("textures/ui/refresh_light", FormIcon::IMAGE_TYPE_PATH)),
                new MenuOption("§4Close", new FormIcon("textures/ui/realms_red_x", FormIcon::IMAGE_TYPE_PATH))
            ],
            function (Player $player, int $selected): void {
                switch ($selected) {
                    case 0:
                        $player->setScale(0.5);
                        $player->sendMessage(Skyblock::$prefix . "§r§bYour Size Is Now Set To 0.5!");
                        break;
                    case 1:
                        $player->setScale(0.75);
                        $player->sendMessage(Skyblock::$prefix . "§r§bYour Size Is Now Set To 0.75!");
                        break;
                    case 2:
                        $player->setScale(1.25);
                        $player->sendMessage(Skyblock::$prefix . "§r§bYour Size Is Now Set To 1.25!");
                        break;
                    case 3:
                        $player->setScale(1.50);
                        $player->sendMessage(Skyblock::$prefix . "§r§bYour Size Is Now Set To 1.50!");
                        break;
                    case 4:
                        $player->setScale(1.0);
                        $player->sendMessage(Skyblock::$prefix . "§r§bYour Size Is Now Has Been Reset!");
                        break;
                    case 5:

                        break;
                }
            }
        );
    }
}
