<?php

declare(strict_types=1);

namespace Biswajit\Core\Listeners\Player;

use Biswajit\Core\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;

class PlayerCreation implements Listener
{
    public function onCreation(PlayerCreationEvent $event)
    {
        $event->setPlayerClass(Player::class);
    }
}
