<?php

namespace Biswajit\Core\Commands\player;

use Biswajit\Core\Managers\IslandManager;
use Biswajit\Core\Sessions\IslandData;
use Biswajit\Core\Skyblock;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class JoinCommand extends Command
{
    public function __construct()
    {
        parent::__construct("join", "§bJoin Your Skyblock Island!", "/join", [""]);
        $this->setPermission("island.cmd");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): mixed
    {
        if ($sender instanceof Player) {
            IslandData::get($sender->getName(), function (?IslandData $islandData) use ($sender): void {
                if ($islandData !== null) {
                    IslandManager::teleportToIsland($sender);
                } else {
                    $sender->sendMessage(Skyblock::$prefix . "bYou Don't Have An Island, §eCreate An Island With /is");
                }
            });
            return true;
        }
        return false;
    }
}
