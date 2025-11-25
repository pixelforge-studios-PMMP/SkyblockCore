<?php

declare(strict_types=1);

namespace Biswajit\Core\Listeners\Entity;

use Biswajit\Core\Events\Entity\EntityAttackPlayer;
use pocketmine\event\Listener;
use pocketmine\network\mcpe\protocol\AnimatePacket;

class EntityAttackEvent implements Listener
{
    public function onAttack(EntityAttackPlayer $event): void
    {
        $player = $event->getPlayer();
        $entity = $event->getEntity();
        $damage = $event->getFinalDamage();

        $defense = $player->getDefense();

        $reduceDamage = $defense / ($defense + 100);
        $newDamage = max(0, $damage * (1 - $reduceDamage));

        $player->damagePlayer($newDamage);

        $deltaX = $player->getPosition()->x - $entity->getPosition()->x;
        $deltaZ = $player->getPosition()->z - $entity->getPosition()->z;
        $player->knockBack($deltaX, $deltaZ, $event->getKnockback());

        $animatePacket = AnimatePacket::create($entity->getId(), AnimatePacket::ACTION_SWING_ARM);
        $entity->getWorld()->broadcastPacketToViewers($entity->getPosition(), $animatePacket);
    }
}
