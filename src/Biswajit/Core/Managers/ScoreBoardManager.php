<?php

declare(strict_types=1);

namespace Biswajit\Core\Managers;

use Biswajit\Core\Player;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;

class ScoreBoardManager
{
    use ManagerBase;

    private static string $ScoreBoard;

    public static function setScoreboard(string $scoreboard): void
    {
        self::$ScoreBoard = $scoreboard;
    }

    public static function getScoreboard(): string
    {
        return self::$ScoreBoard;
    }

    public static function setScoreboardEntry(Player $player, int $score, string $msg, string $objName): void
    {
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $objName;
        $entry->type = 3;
        $entry->customName = "$msg";
        $entry->score = $score;
        $entry->scoreboardId = $score;
        $playerk = new SetScorePacket();
        $playerk->type = 0;
        $playerk->entries[$score] = $entry;
        $player->getNetworkSession()->sendDataPacket($playerk);
    }

    public static function createScoreboard(Player $player, string $title, string $objName, string $slot = "sidebar", $order = 0): void
    {
        $playerk = new SetDisplayObjectivePacket();
        $playerk->displaySlot = $slot;
        $playerk->objectiveName = $objName;
        $playerk->displayName = $title;
        $playerk->criteriaName = "dummy";
        $playerk->sortOrder = $order;
        $player->getNetworkSession()->sendDataPacket($playerk);
    }

    public static function removeScoreboard(Player $player, string $objName): void
    {
        $playerk = new RemoveObjectivePacket();
        $playerk->objectiveName = $objName;
        $player->getNetworkSession()->sendDataPacket($playerk);
    }

}
