<?php

declare(strict_types=1);

namespace Biswajit\Core\Tasks;

use Biswajit\Core\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class StatsRegainTask extends Task
{
    public function onRun(): void
    {
        $players = array_filter(Server::getInstance()->getOnlinePlayers(), function ($player) {
            return $player instanceof Player && $player->isOnline();
        });

        foreach ($players as $player) {
            $this->regenerateHealth($player);
            $this->regenerateMana($player);
        }
    }

    private function regenerateHealth(Player $player): void
    {
        $maxHealth = $player->getMaxHealth();
        $currentHealth = $player->getHealth();

        if ($currentHealth < $maxHealth) {
            $gainedHealth = (($maxHealth * 0.01) + 1.5) * 1;
            $player->setHealth(min($currentHealth + $gainedHealth, $maxHealth));
        }
    }

    private function regenerateMana(Player $player): void
    {
        $maxIntelligence = $player->getMaxMana();
        $currentIntelligence = $player->getMana();

        if ($currentIntelligence < $maxIntelligence) {
            $regenIntelligence = $maxIntelligence * 0.04;
            $player->setMana(min($currentIntelligence + $regenIntelligence, $maxIntelligence));
        }
    }
}
