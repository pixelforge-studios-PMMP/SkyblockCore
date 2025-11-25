<?php

declare(strict_types=1);

namespace Biswajit\Core\Menus\island;

use pocketmine\Server;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use Biswajit\Core\Skyblock;
use dktapps\pmforms\MenuOption;
use Biswajit\Core\Managers\IslandManager;
use Biswajit\Core\Sessions\IslandData;

class IslandVisitAllOpenForm extends MenuForm
{
    public function __construct()
    {
        $options = [];
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $islandData = IslandData::getSync($player->getName());
            $value = $islandData ? $islandData->getVisit() : false;
            if ($value) {
                $options[] = new MenuOption($player->getName());
            }
        }

        parent::__construct(
            Skyblock::$prefix . "Players Open to Visit",
            "\n",
            $options,
            function (Player $player, int $option): void {
                $menuOption = $this->getOption($option);
                if (!$menuOption instanceof MenuOption) {
                    return;
                }

                $selectedPlayer = $menuOption->getText();
                IslandManager::islandVisit($player, $selectedPlayer);
            }
        );
    }
}
