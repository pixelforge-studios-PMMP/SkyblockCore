<?php

namespace Biswajit\Core\Tasks;

use Biswajit\Core\Player;
use Biswajit\Core\Skyblock;
use pocketmine\scheduler\Task;

class ActionbarTask extends Task
{
    public function onRun(): void
    {
        foreach (Skyblock::getInstance()->getServer()->getOnlinePlayers() as $player) {
            if ($player instanceof Player) {
                $mana = $player->getMana();
                $maxMana = $player->getMaxMana();
                $health = $player->getHealth();
                $maxhealth = $player->getMaxHealth();
                $defense = $player->getDefense();

                if ($health > $maxhealth) {
                    $player->setHealth($maxhealth);
                }

                $player->sendActionBarMessage("§c❤ {$health}§7/§c{$maxhealth}  §a {$defense}  §b {$mana}§7/§b{$maxMana}");
            }
        }
    }
}
