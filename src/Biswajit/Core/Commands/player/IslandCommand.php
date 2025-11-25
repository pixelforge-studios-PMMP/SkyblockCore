<?php

namespace Biswajit\Core\Commands\player;

use Biswajit\Core\Menus\island\IslandOptionsForm;
use Biswajit\Core\Menus\island\NoIslandForm;
use Biswajit\Core\Sessions\IslandData;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class IslandCommand extends Command
{
    public function __construct()
    {
        parent::__construct("island", "§bOpens the island screen!", "/island", ["island", "is", "sb"]);
        $this->setPermission("island.cmd");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): mixed
    {
        if ($sender instanceof Player) {
            IslandData::get($sender->getName(), function (?IslandData $islandData) use ($sender): void {
                if ($islandData !== null) {
                    $sender->sendForm(new IslandOptionsForm($sender));
                    return;
                }
                $sender->sendForm(new NoIslandForm());
            });
            return true;
        }
        return false;
    }
}
