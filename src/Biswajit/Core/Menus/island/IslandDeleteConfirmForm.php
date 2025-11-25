<?php

declare(strict_types=1);

namespace Biswajit\Core\Menus\island;

use pocketmine\player\Player;
use dktapps\pmforms\ModalForm;
use Biswajit\Core\Skyblock;
use Biswajit\Core\Managers\IslandManager;

class IslandDeleteConfirmForm extends ModalForm
{
    public function __construct()
    {
        parent::__construct(
            Skyblock::$prefix . "Island Delete",
            "§bDo You Wand To Delete Your Island?",
            function (Player $player, bool $choice): void {
                switch ($choice) {
                    case true:
                        IslandManager::islandRemove($player);
                        break;
                    case false:
                        $player->sendForm(new IslandOptionsForm($player));
                        break;
                }
            },
            "§e»§3 YES§e «",
            "< Back"
        );
    }
}
