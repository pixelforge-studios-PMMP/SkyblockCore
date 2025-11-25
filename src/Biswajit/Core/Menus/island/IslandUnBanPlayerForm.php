<?php

declare(strict_types=1);

namespace Biswajit\Core\Menus\island;

use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use Biswajit\Core\Skyblock;
use dktapps\pmforms\MenuOption;
use Biswajit\Core\Managers\IslandManager;
use Biswajit\Core\Sessions\IslandData;

class IslandUnBanPlayerForm extends MenuForm
{
    public function __construct(Player $player)
    {
        $options = [];
        $islandData = IslandData::getSync($player->getName());
        $bannedIslands = $islandData ? $islandData->getBanneds() : [];

        foreach ($bannedIslands as $item => $value) {
            $options[] = new MenuOption($value);
        }

        parent::__construct(
            Skyblock::$prefix . "Remove Player Ban",
            "\n",
            $options,
            function (Player $player, int $option): void {
                $menuOption = $this->getOption($option);
                if (!$menuOption instanceof MenuOption) {
                    return;
                }

                $selectedPlayer = $menuOption->getText();
                IslandManager::islandUnBanPlayer($player, $selectedPlayer);
            }
        );
    }
}
