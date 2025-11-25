<?php

declare(strict_types=1);

namespace Biswajit\Core\Commands\Staff;

use Biswajit\Core\API;
use Biswajit\Core\Managers\RankManager;
use Biswajit\Core\Player;
use Biswajit\Core\Skyblock;
use Biswajit\Core\Utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class RankCommand extends Command
{
    public function __construct()
    {
        parent::__construct("setrank", "Set rank for a player", "/setrank <player> <rank>", []);
        $this->setPermission("staff.rank.cmd");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender)) {
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("no-permission"));
            return;
        }

        if (count($args) < 2) {
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("usage-setrank"));
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("duration-help"));
            return;
        }

        $playerName = $args[0];
        $rankName = $args[1];
        $duration = $args[2] ?? null;

        $target = Server::getInstance()->getPlayerExact($playerName);
        if ($target === null) {
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("player-not-found"));
            return;
        }

        if (!$target instanceof Player) {
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("invalid-player"));
            return;
        }

        $availableRanksLower = RankManager::getRankList();

        if (!in_array($rankName, $availableRanksLower)) {
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("rank-not-exist", ["{RANK}" => $rankName]));
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("available-ranks", ["{RANKS}" => implode(", ", $availableRanksLower)]));
            return;
        }

        $durationSeconds = 0;
        $isTemporary = false;

        if ($duration !== null) {
            $isTemporary = true;
            $durationSeconds = Utils::parseDuration($duration);
            if ($durationSeconds <= 0) {
                $sender->sendMessage(Skyblock::$prefix . API::getMessage("invalid-duration"));
                return;
            }
        }

        if ($isTemporary) {
            RankManager::setRank($rankName, $target, time() + $durationSeconds);
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("temp-rank-set-sender", ["{RANK}" => $rankName, "{PLAYER}" => $target->getName(), "{DURATION}" => $duration]));
            $target->sendMessage(Skyblock::$prefix . API::getMessage("temp-rank-set-target", ["{RANK}" => $rankName, "{DURATION}" => $duration]));
        } else {
            RankManager::setRank($rankName, $target);
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("perm-rank-set-sender", ["{RANK}" => $rankName, "{PLAYER}" => $target->getName()]));
            $target->sendMessage(Skyblock::$prefix . API::getMessage("perm-rank-set-target", ["{RANK}" => $rankName]));
        }
    }

}
