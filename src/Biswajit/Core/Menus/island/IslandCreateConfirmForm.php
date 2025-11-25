<?php

declare(strict_types=1);

namespace Biswajit\Core\Menus\island;

use pocketmine\player\Player;
use dktapps\pmforms\ModalForm;
use Biswajit\Core\Skyblock;
use Biswajit\Core\Managers\IslandManager;

class IslandCreateConfirmForm extends ModalForm
{
    public function __construct(string $type)
    {
        parent::__construct(
            Skyblock::$prefix . "§bIsland Creation Confirmation",
            "\n§7How about creating an Island and embarking on a new adventure?\n\n§aIsland Type: §b" . $type . "\n",
            function (Player $player, bool $choice) use ($type): void {
                switch ($choice) {
                    case true:
                        IslandManager::islandCreate($player, $type);
                        break;
                    case false:
                        $player->sendForm(new IslandTypeForm());
                        break;
                }
            },
            "Create an Island",
            "< Back"
        );
    }
}
