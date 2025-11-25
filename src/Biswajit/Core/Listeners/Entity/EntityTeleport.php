<?php

declare(strict_types=1);

namespace Biswajit\Core\Listeners\Entity;

use Biswajit\Core\API;
use Biswajit\Core\Player;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\Server;

class EntityTeleport implements Listener
{
    public function onEntityTeleport(EntityTeleportEvent $event): void
    {
        $entity = $event->getEntity();
        $to = $event->getTo();
        $world = $to->getWorld();

        if (!$entity instanceof Player) {
            return;
        }

        if ($world->getFolderName() !== API::getHub()) {
            return;
        }

        if (!$entity->isFlying() && Server::getInstance()->isOp($entity->getName())) {
            return;
        }

        $entity->setFlying(false);
        $entity->sendMessage(" §eYour Fly Disabled Now!");
    }
}
