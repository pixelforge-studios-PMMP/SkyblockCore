<?php

namespace Biswajit\Core\Tasks;

use Biswajit\Core\API;
use Biswajit\Core\Skyblock;
use pocketmine\Server;
use pocketmine\entity\Human;
use pocketmine\scheduler\Task;

class ClearLagTask extends Task
{
    /** @var int */
    private int $Seconds;

    public function __construct()
    {
        $this->Seconds = 300;
    }

    public function onRun(): void
    {
        $Server = Server::getInstance();

        $Message = "";
        switch ($this->Seconds) {
            case 300:
                $Message = Skyblock::$prefix . API::getMessage("clearlag-5min");
                break;
            case 120:
                $Message = Skyblock::$prefix . API::getMessage("clearlag-2min");
                break;
            case 60:
                $Message = Skyblock::$prefix . API::getMessage("clearlag-1min");
                break;
            case 10:
                $Message = Skyblock::$prefix . API::getMessage("clearlag-10sec");
                break;
            case 3:
                $Message = Skyblock::$prefix . API::getMessage("clearlag-3sec");
                break;
            case 2:
                $Message = Skyblock::$prefix . API::getMessage("clearlag-2sec");
                break;
            case 1:
                $Message = Skyblock::$prefix . API::getMessage("clearlag-1sec");
                break;
            case 0:
                $WorldManager = $Server->getWorldManager();
                $Worlds = $WorldManager->getWorlds();
                foreach ($Worlds as $World) {
                    $Entities = $World->getEntities();
                    foreach ($Entities as $Entity) {
                        if ($Entity instanceof Human) {
                            continue;
                        }
                        $Entity->close();
                    }
                }
                $Message = Skyblock::$prefix . API::getMessage("clearlag-despawned");
                break;
        }

        $Onlines = $Server->getOnlinePlayers();
        foreach ($Onlines as $Online) {
            if ($Message !== "") {
                $Online->sendMessage($Message);
            }
        }

        if ($this->Seconds >= 1) {
            $this->Seconds--;
        } else {
            $this->Seconds = 300;
        }
    }
}
