<?php

declare(strict_types=1);

namespace Biswajit\Core\Managers;

use Biswajit\Core\API;
use Biswajit\Core\Player;
use Biswajit\Core\Skyblock;
use pocketmine\permission\PermissionManager;
use pocketmine\utils\Config;

class RankManager
{
    use ManagerBase;

    private static array $attachments = [];

    public static function setRank(string $rankName, Player $player, $durationSeconds = "Never"): void
    {
        if (!empty(self::getRanksConfig()->get($rankName))) {
            $player->setRank("Rank", $rankName);
            $player->setRank("expiry", $durationSeconds);
            self::removeAttach($player);
            self::addPermissionsForPlayer($player);
            $format = self::getNameFormat(self::getRankOfPlayer($player));
            $finalFormat = str_replace(["&", "{player_name}"], ["§", $player->getName()], $format);
            $player->setNameTag($finalFormat);
        }
    }

    public static function getRankOfPlayer(Player $player): ?string
    {
        return $player->getRank("Rank");
    }

    public static function checkAndExpireTempRank(Player $player): void
    {
        $expiry = $player->getRank("expiry");
        if ($expiry !== "Never" && is_numeric($expiry) && (int)$expiry <= time()) {
            self::setRank("Default", $player);
            $player->sendMessage(Skyblock::$prefix . API::getMessage("temp_rank_expired"));
        }
    }

    public static function addPermissionsForPlayer(Player $player): void
    {
        $rankName = self::getRankOfPlayer($player);
        $perms = [];
        if (RankManager::getPermissionsOfRank($rankName) !== []) {
            foreach (RankManager::getPermissionsOfRank($rankName) as $permission) {
                if ($permission === "*") {
                    foreach (PermissionManager::getInstance()->getPermissions() as $tmp) {
                        $perms[$tmp->getName()] = true;
                    }
                } else {
                    $perms[$permission] = true;
                }
                self::$attachments[$player->getName()] = $player->addAttachment(Skyblock::getInstance());
                self::$attachments[$player->getName()]->clearPermissions();
                self::$attachments[$player->getName()]->setPermissions($perms);
            }
        }
    }

    public static function removeAttach(Player $player): void
    {
        if (isset(self::$attachments[$player->getName()])) {
            $player->removeAttachment(self::$attachments[$player->getName()]);
        }
    }

    public static function getRanksConfig(): Config
    {
        return new Config(self::getDataFolder() . "ranks.yml", Config::YAML, array());
    }

    public static function getRanks(): ?array
    {
        if (!empty(self::getRanksConfig()->getAll())) {
            $data = self::getRanksConfig()->getAll();
            return $data;
        } else {
            return null;
        }
    }

    public static function getRankList(): array
    {
        $list = [];
        foreach (self::getRanks() as $rank) {
            $list[] = $rank["Alisa"];
        }
        return $list;
    }

    public static function getPermissionsOfRank(string $rankName): ?array
    {
        if (!empty(self::getRanksConfig()->get($rankName))) {
            $data = self::getRanksConfig()->getNested("$rankName.Permissions");
            return $data;
        }
        return null;
    }

    public static function getChatFormat(string $rankName): ?string
    {
        if (!empty(self::getRanksConfig()->get($rankName))) {
            $data = self::getRanksConfig()->getNested("$rankName.ChatFormat");
            return $data;
        }
        return null;
    }

    public static function getNameFormat(string $rankName): ?string
    {
        if (!empty(self::getRanksConfig()->get($rankName))) {
            $data = self::getRanksConfig()->getNested("$rankName.NameFormat");
            return $data;
        }
        return null;
    }
}
