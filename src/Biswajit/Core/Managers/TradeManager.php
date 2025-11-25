<?php

declare(strict_types=1);

namespace Biswajit\Core\Managers;

use Biswajit\Core\Player;
use Biswajit\Core\Skyblock;
use Biswajit\Core\Utils\Utils;
use pocketmine\scheduler\ClosureTask;

class TradeManager
{
    use ManagerBase;

    private static array $trades = [];

    public static function addTradeRequest(Player $player, string $victimName): void
    {
        $playerName = $player->getName();

        if ($playerName === $victimName || self::hasTradeRequest($player, $victimName)) {
            return;
        }

        $requests = self::getTradeRequests($player);
        $requests[] = $victimName;
        self::$trades[$playerName] = $requests;

        self::getPlugin()->getScheduler()->scheduleDelayedTask(new ClosureTask(
            function () use ($player, $victimName): void {
                if (self::hasTradeRequest($player, $victimName)) {
                    self::removeTradeRequest($player, $victimName);
                    $victim = self::getServer()->getPlayerExact($victimName);
                    if ($victim instanceof Player) {
                        $victim->sendMessage(Skyblock::$prefix . "§cYour trade request to §e{$player->getName()} §chas expired");
                    }
                }
            }
        ), 20 * 60);
    }

    public static function hasTradeRequest(Player $player, string $victimName): bool
    {
        $playerName = $player->getName();
        return isset(self::$trades[$playerName]) && in_array($victimName, self::$trades[$playerName], true);
    }

    public static function getTradeRequests(Player $player): array
    {
        $playerName = $player->getName();
        return self::$trades[$playerName] ?? [];
    }

    public static function removeTradeRequest(Player $player, string $victimName): void
    {
        $playerName = $player->getName();

        if (!self::hasTradeRequest($player, $victimName)) {
            return;
        }

        self::$trades[$playerName] = Utils::removeKeyFromArray(self::$trades[$playerName], $victimName);
    }
}
