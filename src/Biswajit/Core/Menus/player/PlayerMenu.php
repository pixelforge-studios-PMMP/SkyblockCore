<?php

namespace Biswajit\Core\Menus\player;

use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use Biswajit\Core\Managers\EconomyManager;
use Biswajit\Core\Managers\TradeManager;
use Biswajit\Core\Menus\trade\TradeMenu;
use Biswajit\Core\Player as SkyblockPlayer;
use Biswajit\Core\Utils\Utils;
use pocketmine\Server;

class PlayerMenu extends MenuForm
{
    public function __construct(Player $damager, Player $victim)
    {
        $item = $victim->getInventory()->getItemInHand();
        $damage = $item->getAttackPoints();
        $defense = ($victim instanceof SkyblockPlayer ? $victim->getDefense() : 0) + $victim->getArmorPoints();
        $heal = $victim->getHealth();
        $maxheal = $victim->getMaxHealth();
        $name = $victim->getName();
        $coin = EconomyManager::getMoney($victim);
        $ping = $victim->getNetworkSession()->getPing();
        $gems = EconomyManager::getGems($victim);
        $device = Utils::getPlayerPlatform($victim);

        $options = [];
        if (TradeManager::hasTradeRequest($damager, $victim->getName())) {
            $options[] = new MenuOption("§l§bACCEPT TRADE\n§l§9»» §r§oTap to accept", new FormIcon("https://i.imgur.com/HNAHnLE.png", FormIcon::IMAGE_TYPE_URL));
        } else {
            $options[] = new MenuOption("§l§bREQUEST TRADE\n§l§9»» §r§oTap to request", new FormIcon("https://i.imgur.com/HNAHnLE.png", FormIcon::IMAGE_TYPE_URL));
        }

        $options[] = new MenuOption("§l§bVISIT ISLAND\n§l§9»» §r§oTap to visit", new FormIcon("https://i.imgur.com/qt15cyk.png", FormIcon::IMAGE_TYPE_URL));

        parent::__construct("§l§ePROFILE", "§bName:§e $name\n§bPing:§e $ping\n§bMoney In Purse:§e $coin\n§bGems:§e $gems\n§bDevice:§e $device\n\n§d§lSTATS:§r\n§7+ §cHealth: $heal" . "§7/§c$maxheal \n§7+ §aDefense: §a$defense \n§7+ §4Damage: $damage ", $options, function (Player $player, int $selected) use ($victim): void {
            switch ($selected) {
                case 0:
                    if (TradeManager::hasTradeRequest($player, $victim->getName())) {
                        if ($player->isOnline() && $victim->isOnline()) {
                            $player->sendMessage("§a⩋ accepted §e" . $victim->getName() . "'s §atrade request");
                            $victim->sendMessage("§e " . $player->getName() . " §aaccepted your trade request");
                            TradeMenu::TradeMenu($player, $victim);
                            TradeManager::removeTradeRequest($player, $victim->getName());
                        }
                    } else {
                        if ($player->isOnline() && $victim->isOnline()) {
                            TradeManager::addTradeRequest($victim, $player->getName());
                            $player->sendMessage("§a trade request sent to §e" . $victim->getName());
                            $player->sendMessage("§a your trade request will expire in 1 minute");
                            $victim->sendMessage("§a you recieved a trade request from §e" . $player->getName());
                        }
                    }
                    break;

                case 1:
                    $name = $victim->getName();
                    Server::getInstance()->dispatchCommand($player, "visit \"$name\"");
                    break;
            }
        });
    }
}
