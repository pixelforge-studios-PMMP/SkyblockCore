<?php

declare(strict_types=1);

namespace Biswajit\Core\Menus\island;

use Biswajit\Core\Menus\island\partner\PartnerTeleportForm;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use Biswajit\Core\Skyblock;
use dktapps\pmforms\MenuOption;

class NoIslandForm extends MenuForm
{
    public function __construct()
    {
        parent::__construct(
            Skyblock::$prefix . "SkyBlock",
            "\n",
            [
                new MenuOption("§e» §bIsland Types and Creation \n§8Click To Select", new FormIcon('https://cdn-icons-png.flaticon.com/128/619/619005.png', FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§e» §bYour Partner Islands \n§8Click To Select", new FormIcon('https://cdn-icons-png.flaticon.com/128/2010/2010261.png', FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§e» §bVisitable Islands \n§8Click To Select", new FormIcon('https://cdn-icons-png.flaticon.com/128/854/854878.png', FormIcon::IMAGE_TYPE_URL))
            ],
            function (Player $player, int $option): void {
                switch ($option) {
                    case 0:
                        $player->sendForm(new IslandTypeForm());
                        break;
                    case 1:
                        $player->sendForm(new PartnerTeleportForm($player));
                        break;
                    case 2:
                        $player->sendForm(new IslandVisitAllOpenForm());
                        break;
                }
            }
        );
    }
}
