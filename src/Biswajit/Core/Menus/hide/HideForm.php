<?php

namespace Biswajit\Core\Menus\hide;

use pocketmine\Server;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use dktapps\pmforms\MenuOption;

class HideForm extends MenuForm
{
    public array $hidePlayer = [];

    public function __construct()
    {
        parent::__construct(
            "§l§6Player Hide",
            "§bPlayer Hide / Un Hide\n\n§bThis Feature Will Reduce Lag On Player Device\n\n§a(§cNote:§a This Function Is Still In Beta You May Can Get Error)",
            [
                new MenuOption("§bHide", new FormIcon("textures/icon/star", FormIcon::IMAGE_TYPE_URL)),
                new MenuOption("§bUnhide", new FormIcon("textures/icon/star", FormIcon::IMAGE_TYPE_URL))

            ],
            function (Player $sender, int $selected): void {
                switch ($selected) {
                    case 0:
                        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                            $sender->hidePlayer($player);
                            $sender->sendTitle("§6Done! ", "§eAll Players Are Now Hidden");
                        }
                        break;
                    case 1:
                        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                            $sender->showPlayer($player);
                            $sender->sendTitle("§6Done! ", "§eAll Players Are Now Showing");
                        }
                        break;
                }
            }
        );
    }
}
