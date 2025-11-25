<?php

declare(strict_types=1);

namespace Biswajit\Core\Listeners\Entity;

use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\Listener;

class EntityRegainHealth implements Listener
{
    public function onEntityRegainHealth(EntityRegainHealthEvent $event): void
    {
        if ($event->getRegainReason() === EntityRegainHealthEvent::CAUSE_SATURATION) {
            $event->cancel();
        }
    }
}
