<?php

declare(strict_types=1);

namespace Biswajit\Core\Menus\island;

use pocketmine\Server;
use pocketmine\world\World;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use Biswajit\Core\Skyblock;
use dktapps\pmforms\MenuOption;

class IslandPlayersForm extends MenuForm
{
    public function __construct(Player $player)
    {
        $options = [];
        $world = Server::getInstance()->getWorldManager()->getWorldByName($player->getName());
        if ($world instanceof World) {
            foreach ($world->getPlayers() as $worldPlayer) {
                $options[] = new MenuOption($worldPlayer->getNameTag());
            }
        }
        parent::__construct(Skyblock::$prefix . "Players on the Island", "\n", $options, function (Player $player, int $option): void {
        });
    }
}
