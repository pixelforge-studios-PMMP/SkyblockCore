<?php

declare(strict_types=1);

namespace Biswajit\Core\Tasks;

use Biswajit\Core\Managers\RankManager;
use Biswajit\Core\Skyblock;
use pocketmine\scheduler\Task;
use Biswajit\Core\Player;

class RankTask extends Task
{
    /** @var Skyblock */
    private Skyblock $source;

    public function __construct(Skyblock $source)
    {
        $this->source = $source;
    }

    public function onRun(): void
    {
        $this->checkAndExpireTempRanks();
    }

    private function checkAndExpireTempRanks(): void
    {
        foreach ($this->source->getServer()->getOnlinePlayers() as $player) {
            if ($player instanceof Player) {
                RankManager::checkAndExpireTempRank($player);
            }
        }
    }
}
