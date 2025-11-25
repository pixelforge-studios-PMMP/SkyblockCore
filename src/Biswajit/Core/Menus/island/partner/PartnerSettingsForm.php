<?php

declare(strict_types=1);

namespace Biswajit\Core\Menus\island\partner;

use pocketmine\player\Player;
use Biswajit\Core\Skyblock;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\element\Toggle;
use dktapps\pmforms\CustomFormResponse;
use Biswajit\Core\Managers\IslandManager;
use Biswajit\Core\Sessions\IslandData;

class PartnerSettingsForm extends CustomForm
{
    public function __construct(Player $player)
    {
        $islandData = IslandData::getSync($player->getName());
        $data = $islandData ? $islandData->getSettings() : [];

        parent::__construct(
            Skyblock::$prefix . "Partner Settings",
            [
                new Toggle("interact", "§d§l»§r §bInteract", $data["interact"] ?? false),
                new Toggle("place", "§d§l»§r §bPlace", $data["place"] ?? false),
                new Toggle("break", "§d§l»§r §bBreak", $data["break"] ?? false),
                new Toggle("picking-up", "§d§l»§r §bReceiving Items from the Ground", $data["picking-up"] ?? false),
                new Toggle("de-active-teleport", "§d§l»§r §bTeleport While Inactive", $data["de-active-teleport"] ?? false)
            ],
            function (Player $player, CustomFormResponse $response): void {
                $interact = $response->getBool("interact");
                $place = $response->getBool("place");
                $break = $response->getBool("break");
                $pickingUp = $response->getBool("picking-up");
                $deActiveTeleport = $response->getBool("de-active-teleport");
                IslandManager::changePartnerSettings($player, $interact, $place, $break, $pickingUp, $deActiveTeleport);
            }
        );
    }
}
