<?php

declare(strict_types=1);

namespace Biswajit\Core\Listeners\Player;

use Biswajit\Core\API;
use Biswajit\Core\Managers\BankManager;
use Biswajit\Core\Managers\RankManager;
use Biswajit\Core\Player;
use Biswajit\Core\Skyblock;
use Biswajit\Core\Tasks\InterestTask;
use Biswajit\Core\Utils\Utils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class PlayerJoin implements Listener
{
    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $name = $player->getName();

        if (!$player instanceof Player) {
            return;
        }

        $event->setJoinMessage(API::getMessage("Join", ["{player}" => $name]));

        $player->getInventory()->setItem(8, API::getItem("menu"));
        $servername = Utils::getServerName();
        $player->sendMessage(API::getMessage("Join-Message", ["{player}" => $name, "{servername}" => $servername, "{vote}" => Skyblock::getInstance()->getConfig()->get("VOTE-WEBSITE"), "{discord}" => Skyblock::getInstance()->getConfig()->get("DISCORD-LINK")]));

        $format = RankManager::getNameFormat(RankManager::getRankOfPlayer($player));
        $finalFormat = str_replace(["&", "{player_name}"], ["§", $player->getName()], $format);
        $player->setNameTag($finalFormat);
        RankManager::addPermissionsForPlayer($player);

        if (BankManager::getBankMoney($player) > 0) {
            if (!array_key_exists($player->getName(), BankManager::$interest)) {
                BankManager::$interest[$player->getName()] =  Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new InterestTask(Skyblock::getInstance(), $player), 72000);
            }
        }
    }
}
