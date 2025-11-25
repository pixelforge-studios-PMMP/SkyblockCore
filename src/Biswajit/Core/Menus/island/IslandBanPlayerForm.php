<?php

declare(strict_types=1);

namespace Biswajit\Core\Menus\island;

use Biswajit\Core\Managers\IslandManager;
use Biswajit\Core\Skyblock;
use pocketmine\Server;
use pocketmine\world\World;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use dktapps\pmforms\MenuOption;

class IslandBanPlayerForm extends MenuForm
{
    public function __construct(Player $player)
    {
        $options = [];
        $world = Server::getInstance()->getWorldManager()->getWorldByName($player->getName());
        if ($world instanceof World) {
            foreach ($world->getPlayers() as $worldPlayer) {
                $options[] = new MenuOption($worldPlayer->getName());
            }
        }
        parent::__construct(
            Skyblock::$prefix . "Ban Players From Your Island",
            "\n",
            $options,
            function (Player $player, int $option): void {
                $menuOption = $this->getOption($option);
                if (!$menuOption instanceof MenuOption) {
                    return;
                }

                $selectedPlayer = $menuOption->getText();
                IslandManager::islandBanPlayer($player, $selectedPlayer);
            }
        );
    }
}
