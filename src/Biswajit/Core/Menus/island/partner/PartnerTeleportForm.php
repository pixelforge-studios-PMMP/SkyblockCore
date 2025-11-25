<?php

declare(strict_types=1);

namespace Biswajit\Core\Menus\island\partner;

use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use Biswajit\Core\Skyblock;
use dktapps\pmforms\MenuOption;
use Biswajit\Core\Managers\IslandManager;
use Biswajit\Core\Sessions\IslandData;

class PartnerTeleportForm extends MenuForm
{
    public function __construct(Player $player)
    {
        $options = [];
        IslandData::get($player->getName(), function (?IslandData $islandData) use ($options): void {
            if ($islandData !== null) {
                $partners = $islandData->getPartners();
                if (!empty($partners)) {
                    foreach ($partners as $partner) {
                        $options[] = new MenuOption($partner);
                    }
                }
            }
            parent::__construct(
                Skyblock::$prefix . "Teleport to Partner Island",
                "§7Choose the partner you want to teleport to!",
                $options,
                function (Player $player, int $option): void {
                    $menuOption = $this->getOption($option);
                    if (!$menuOption instanceof MenuOption) {
                        return;
                    }

                    $selectedPlayer = $menuOption->getText();
                    IslandManager::teleportPartnerIsland($player, $selectedPlayer);
                }
            );
        });
    }
}
