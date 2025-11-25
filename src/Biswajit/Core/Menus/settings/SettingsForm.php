<?php

namespace Biswajit\Core\Menus\settings;

use Biswajit\Core\Menus\hide\HideForm;
use Biswajit\Core\Menus\nick\NickForm;
use Biswajit\Core\Menus\perks\PerksForm;
use Biswajit\Core\Menus\size\SizeForm;
use Biswajit\Core\Menus\vanish\VanishForm;
use Biswajit\Core\Skyblock;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use dktapps\pmforms\MenuOption;

class SettingsForm extends MenuForm
{
    public function __construct()
    {
        parent::__construct("§l§eSettings", "§bYour Personal Setting", [
            new MenuOption("§l§bNICKNAME\n§l§9»» §r§oTap to open", new FormIcon("textures/icon/settings", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§l§bHIDE PLAYERS\n§l§9»» §r§oTap to open", new FormIcon("textures/icon/settings", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§l§bSIZE\n§l§9»» §r§oTap to open", new FormIcon("textures/icon/settings", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§l§bPERKS\n§l§9»» §r§oTap to open", new FormIcon("textures/icon/settings", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§l§bVANISH\n§l§9»» §r§oTap to open", new FormIcon("textures/icon/settings", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§cExit", new FormIcon("textures/blocks/barrier", FormIcon::IMAGE_TYPE_PATH))
        ], function (Player $sender, int $selected): void {
            switch ($selected) {
                case 0:
                    if ($sender->hasPermission("nick.form")) {
                        $sender->sendForm(new NickForm());
                        return;
                    }
                    $sender->sendMessage(Skyblock::$prefix . "§cThis menu is only for §r§c and + users!");
                    break;

                case 1:
                    if ($sender->hasPermission("hide.form")) {
                        $sender->sendForm(new HideForm());
                        return;
                    }
                    $sender->sendMessage(Skyblock::$prefix . "§cThis menu is only for §r§c and + users!");
                    break;

                case 2:
                    if ($sender->hasPermission("size.form")) {
                        $sender->sendForm(new SizeForm($sender));
                        return;
                    }
                    $sender->sendMessage(Skyblock::$prefix . "§cThis menu is only for §r§c and + users!");
                    break;

                case 3:
                    if ($sender->hasPermission("perks.form")) {
                        $sender->sendForm(new PerksForm());
                        return;
                    }
                    $sender->sendMessage(Skyblock::$prefix . "§cThis menu is only for §r§c and + users!");
                    break;

                case 4:
                    if ($sender->hasPermission("vanish.form")) {
                        $sender->sendForm(new VanishForm());
                        return;
                    }
                    $sender->sendMessage(Skyblock::$prefix . "§cThis menu is only for §r§c and + users!");
                    break;

                case 5:
                    break;
            }
        });
    }
}
