<?php

declare(strict_types=1);

namespace Biswajit\Core\Listeners\Player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;

class PlayerExhaust implements Listener
{
    public function onPlayerExhaust(PlayerExhaustEvent $event)
    {
        $event->cancel();
    }
}
