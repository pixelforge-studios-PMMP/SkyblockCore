<?php

declare(strict_types=1);

namespace Biswajit\Core\Managers;

use Biswajit\Core\Player;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Server;

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

        // Compatibility: prefer static factory if available, otherwise fall back to manual packet
        $type = defined(SetScorePacket::class . '::TYPE_CHANGE') ? SetScorePacket::TYPE_CHANGE : 0;

        if (method_exists(SetScorePacket::class, "create")) {
            try {
                $pk = SetScorePacket::create($type, [$entry]);
            } catch (\Throwable $e) {
                // fallback manual
                $pk = new SetScorePacket();
                if (property_exists($pk, "type")) {
                    $pk->type = $type;
                }
                $pk->entries = [$entry];
            }
        } else {
            $pk = new SetScorePacket();
            if (property_exists($pk, "type")) {
                $pk->type = $type;
            }
            $pk->entries = [$entry];
        }

        try {
            $player->getNetworkSession()->sendDataPacket($pk);
        } catch (\Throwable $e) {
            Server::getInstance()->getLogger()->debug("SkyblockCore: Failed sending scoreboard packet to {$player->getName()}: " . $e->getMessage());
        }
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
