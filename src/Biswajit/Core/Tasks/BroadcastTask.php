<?php

namespace Biswajit\Core\Tasks;

use Biswajit\Core\API;
use Biswajit\Core\Skyblock;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class BroadcastTask extends Task
{
    /** @var Skyblock */
    private Skyblock $source;

    public function __construct(Skyblock $source)
    {
        $this->source = $source;
    }

    public function onRun(): void
    {
        try {
            $messages = API::getMessage("broadcast-messages");
            if (is_array($messages) && !empty($messages)) {
                $randomMessage = $messages[array_rand($messages)];
                foreach (Skyblock::getInstance()->getServer()->getOnlinePlayers() as $player) {
                    $player->sendMessage(Skyblock::$prefix . $randomMessage);
                }
            }
        } catch (\Throwable $e) {
            // Don't let scheduled exceptions kill the server tick; log and continue
            Server::getInstance()->getLogger()->error("SkyblockCore BroadcastTask error: " . $e->getMessage());
        }
    }
}
