<?php

declare(strict_types=1);

namespace Biswajit\Core\Menus\island\partner;

use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use Biswajit\Core\Skyblock;
use dktapps\pmforms\MenuOption;
use Biswajit\Core\Managers\IslandManager;
use Biswajit\Core\Sessions\IslandData;

class PartnerRemoveForm extends MenuForm
{
    public function __construct(Player $player)
    {
        $options = [];
        $islandData = IslandData::getSync($player->getName());
        $partners = $islandData ? $islandData->getPartners() : [];
        if (!empty($partners)) {
            foreach ($partners as $partner) {
                $options[] = new MenuOption($partner);
            }
        }
        parent::__construct(
            Skyblock::$prefix . "Remove Partner",
            "\n",
            $options,
            function (Player $player, int $option): void {
                $menuOption = $this->getOption($option);
                if (!$menuOption instanceof MenuOption) {
                    return;
                }

                $selectedPlayer = $menuOption->getText();
                IslandManager::partnerRemove($player, $selectedPlayer);
            }
        );
    }
}
