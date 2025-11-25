<?php

declare(strict_types=1);

namespace Biswajit\Core\Listeners\Server;

use Biswajit\Core\Skyblock;
use pocketmine\event\Listener;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\Server;

class QueryRegenerate implements Listener
{
    public function onQuery(QueryRegenerateEvent $event)
    {
        $event->getQueryInfo()->setMaxPlayerCount(intval(count(Server::getInstance()->getOnlinePlayers()) + Skyblock::getInstance()->getConfig()->get("PLAYER-COUNT")));
    }
}
