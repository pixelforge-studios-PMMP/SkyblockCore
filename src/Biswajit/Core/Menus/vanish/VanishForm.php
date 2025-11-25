<?php

namespace Biswajit\Core\Menus\vanish;

use Biswajit\Core\API;
use Biswajit\Core\Skyblock;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use dktapps\pmforms\MenuOption;

class VanishForm extends MenuForm
{
    public function __construct()
    {
        parent::__construct("§l§6Vanish", "§6Please Select The Next Menu", [
            new MenuOption("§eVanish \ Unvanish", new FormIcon("textures/icon/settings", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§cExit", new FormIcon("textures/blocks/barrier", FormIcon::IMAGE_TYPE_PATH))
        ], function (Player $sender, int $selected): void {
            switch ($selected) {
                case 0:
                    if (!isset(API::$vanish[$sender->getName()])) {
                        $sender->sendMessage(Skyblock::$prefix . "You are now vanished.");
                        $sender->setInvisible(true);
                        $sender->setSilent(true);
                        API::$vanish[$sender->getName()] = 1;
                        return;
                    }

                    $sender->sendMessage(Skyblock::$prefix . "You are now un vanished.");
                    $sender->setInvisible(false);
                    $sender->setSilent(false);
                    unset(API::$vanish[$sender->getName()]);
                    break;
            }
        });
    }
}
