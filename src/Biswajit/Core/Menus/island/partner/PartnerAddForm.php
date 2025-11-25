<?php

declare(strict_types=1);

namespace Biswajit\Core\Menus\island\partner;

use pocketmine\Server;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use Biswajit\Core\Skyblock;
use dktapps\pmforms\MenuOption;
use Biswajit\Core\Managers\IslandManager;

class PartnerAddForm extends MenuForm
{
    public function __construct()
    {
        $options = [];
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            $options[] = new MenuOption($onlinePlayer->getName());
        }
        parent::__construct(
            Skyblock::$prefix . "Add Partner",
            "Select the player you want to add a partner!",
            $options,
            function (Player $player, int $option): void {
                $menuOption = $this->getOption($option);
                if (!$menuOption instanceof MenuOption) {
                    return;
                }

                $selectedPlayer = $menuOption->getText();
                IslandManager::partnerRequest($player, $selectedPlayer);
            }
        );
    }
}
