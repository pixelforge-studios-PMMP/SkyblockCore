<?php

declare(strict_types=1);

namespace Biswajit\Core\Listeners\Entity;

use Biswajit\Core\Player;
use pocketmine\event\entity\EntityTrampleFarmlandEvent;
use pocketmine\event\Listener;

class EntityTrampleFarmland implements Listener
{
    public function onPlayerTrample(EntityTrampleFarmlandEvent $event): void
    {
        $player = $event->getEntity();
        $worldName = $player->getWorld()->getFolderName();

        if ($player instanceof Player && $worldName !== $player->getName()) {
            $event->cancel();
            return;
        }

        $event->cancel();
    }
}
