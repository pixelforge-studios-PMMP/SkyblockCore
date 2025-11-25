<?php

declare(strict_types=1);

namespace Biswajit\Core\Commands\player;

use Biswajit\Core\API;
use Biswajit\Core\Utils\Utils;
use pocketmine\Server;
use pocketmine\world\World;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class HubCommand extends Command
{
    public function __construct()
    {
        parent::__construct("hub", "§eTeleport To Hub");
        $this->setPermission("player.hub.cmd");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): mixed
    {
        if ($sender instanceof Player) {
            $defaultWorld = Server::getInstance()->getWorldManager()->getWorldByName(API::getHub());
            if (!$defaultWorld instanceof World) {
                return false;
            }

            $sender->teleport($defaultWorld->getSafeSpawn());
            $sender->sendTitle("§6Welcome To Hub", "" . Utils::getServerName());
            return true;
        }
        $sender->sendMessage("Use this command in-game");
        return false;
    }
}
