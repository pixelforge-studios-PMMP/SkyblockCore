<?php

declare(strict_types=1);

namespace Biswajit\Core\Menus\island;

use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use Biswajit\Core\Skyblock;
use dktapps\pmforms\MenuOption;

class IslandTypeForm extends MenuForm
{
    public function __construct()
    {
        parent::__construct(
            "§eCreate an Island",
            "§7Choose the type of island you want to create.\n",
            [
                new MenuOption("§eBasic Island", new FormIcon('https://i.postimg.cc/DfxfRRTz/Basic-Island.png', FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§eBeach", new FormIcon('https://i.postimg.cc/hvfkkyLJ/Beach.png', FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§eResort", new FormIcon('https://i.postimg.cc/D0H9nr6x/Resort.png', FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§eVilla", new FormIcon('https://i.postimg.cc/cJhPryTg/Villa.png', FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§eFantasty", new FormIcon('https://i.postimg.cc/zXRmpQD6/Fantasty.png', FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§eMusroom", new FormIcon('https://i.postimg.cc/0j4srRkd/Musroom.png', FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§eNetherIsland", new FormIcon('https://i.postimg.cc/ZK9m2RHf/Nether.png', FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§eDesert", new FormIcon('https://i.postimg.cc/dQNv5d2k/Desert.png', FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("header_button", new FormIcon("textures/ui/icon_back", FormIcon::IMAGE_TYPE_PATH))
            ],
            function (Player $player, int $option): void {
                switch ($option) {
                    case 0:
                        $player->sendForm(new IslandCreateConfirmForm("BasicIsland"));
                        break;
                    case 1:
                        $player->sendForm(new IslandCreateConfirmForm("Beach"));
                        break;
                    case 2:
                        $player->sendForm(new IslandCreateConfirmForm("Resort"));
                        break;
                    case 3:
                        $player->sendForm(new IslandCreateConfirmForm("Villa"));
                        break;
                    case 4:
                        $player->sendForm(new IslandCreateConfirmForm("Fantasty"));
                        break;
                    case 5:
                        $player->sendForm(new IslandCreateConfirmForm("Javanese"));
                        break;
                    case 6:
                        $player->sendForm(new IslandCreateConfirmForm("Musroom"));
                        break;
                    case 7:
                        $player->sendForm(new IslandCreateConfirmForm("NetherIsland"));
                        break;
                    case 8:
                        $player->sendForm(new IslandCreateConfirmForm("Desert"));
                        break;
                    case 9:
                        break;
                }
            }
        );
    }
}
