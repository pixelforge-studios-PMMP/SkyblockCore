<?php

declare(strict_types=1);

namespace Biswajit\Core\Menus\island\partner;

use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use Biswajit\Core\Skyblock;
use dktapps\pmforms\MenuOption;

class PartnerOptionsForm extends MenuForm
{
    public function __construct()
    {
        parent::__construct(
            Skyblock::$prefix . "Partner Options",
            "\n",
            [
                new MenuOption("Partner Settings\n§d§l»§r §7Tap to select", new FormIcon('https://cdn-icons-png.flaticon.com/128/675/675729.png', FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("Add Partner\n§d§l»§r §7Tap to select", new FormIcon('https://cdn-icons-png.flaticon.com/128/929/929409.png', FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("Remove Partner\n§d§l»§r §7Tap to select", new FormIcon('https://cdn-icons-png.flaticon.com/128/929/929430.png', FormIcon::IMAGE_TYPE_URL))
            ],
            function (Player $player, int $option): void {
                switch ($option) {
                    case 0:
                        $player->sendForm(new PartnerSettingsForm($player));
                        break;
                    case 1:
                        $player->sendForm(new PartnerAddForm());
                        break;
                    case 2:
                        $player->sendForm(new PartnerRemoveForm($player));
                        break;
                }
            }
        );
    }
}
