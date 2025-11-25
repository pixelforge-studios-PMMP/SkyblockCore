<?php

declare(strict_types=1);

namespace Biswajit\Core\Managers;

use Biswajit\Core\API;
use Biswajit\Core\Menus\island\partner\PartnerRequestForm;
use Biswajit\Core\Player;
use Biswajit\Core\Sessions\IslandData;
use Biswajit\Core\Skyblock;
use pocketmine\world\World;
use ZipArchive;

class IslandManager
{
    use ManagerBase;

    public static function loadIslands(): void
    {
        $worldPath = Skyblock::getInstance()->getDataFolder() . "island/Islands.zip";

        if (file_exists($worldPath)) {
            $zip = new ZipArchive();
            $zip->open($worldPath);
            $zip->extractTo(Skyblock::getInstance()->getDataFolder() . "island");
            $zip->close();
        }
    }

    public static function islandVisit(Player $player, string $selectedPlayer): void
    {
        $selectedPlayer = self::getServer()->getPlayerExact($selectedPlayer);
        if (!$selectedPlayer instanceof Player) {
            $player->sendMessage(Skyblock::$prefix . API::getMessage("island-visit-not-active"));
            return;
        }

        IslandData::get($selectedPlayer->getName(), function (?IslandData $islandData) use ($player, $selectedPlayer): void {
            if ($islandData === null) {
                $player->sendMessage(Skyblock::$prefix . API::getMessage("island-visit-no-island"));
                return;
            }
            $visitStatus = $islandData->getVisit();
            if (!$visitStatus) {
                $player->sendMessage(Skyblock::$prefix . API::getMessage("island-visit-locked"));
                return;
            }
            if (!self::getServer()->getWorldManager()->isWorldLoaded($selectedPlayer->getName())) {
                self::getServer()->getWorldManager()->loadWorld($selectedPlayer->getName());
            }
            $world = self::getServer()->getWorldManager()->getWorldByName($selectedPlayer->getName());
            if (!$world instanceof World) {
                return;
            }

            $player->teleport($world->getSpawnLocation());
            $player->sendMessage(Skyblock::$prefix . API::getMessage("island-visit-success"));
            $selectedPlayer->sendMessage(Skyblock::$prefix . API::getMessage("island-visit-notify", ["{player}" => $player->getName()]));
            return;
        });
    }

    public static function partnerRemove(Player $player, string $selectedPlayer): void
    {
        IslandData::get($player->getName(), function (?IslandData $playerData) use ($selectedPlayer) {
            if ($playerData !== null) {
                $playerData->removePartner($selectedPlayer);
            }
        });
        $selectedPlayerObj = self::getServer()->getPlayerExact($selectedPlayer);
        if ($selectedPlayerObj instanceof Player) {
            IslandData::get($selectedPlayerObj->getName(), function (?IslandData $selectedPlayerData) use ($player) {
                if ($selectedPlayerData !== null) {
                    $selectedPlayerData->removePartner($player->getName());
                }
            });
        } else {
            IslandData::get($selectedPlayer, function (?IslandData $islandData) use ($player) {
                if ($islandData !== null) {
                    $islandData->removePartner($player->getName());
                }
            });
        }
        $player->sendMessage(Skyblock::$prefix . API::getMessage("island-partner-remove"));
        if ($selectedPlayerObj instanceof Player) {
            $selectedPlayerObj->sendMessage(Skyblock::$prefix . API::getMessage("island-partner-removed", ["{player}" => $player->getName()]));
        }
    }

    public static function partnerRequestConfirm(Player $player, string $requestPlayer): void
    {
        $requestPlayerObj = self::getServer()->getPlayerExact($requestPlayer);
        if ($requestPlayerObj instanceof Player) {
            IslandData::get($requestPlayerObj->getName(), function (?IslandData $requestPlayerData) use ($player, $requestPlayer) {
                if ($requestPlayerData !== null) {
                    $requestPlayerData->addPartner($player->getName());
                }
            });
            IslandData::get($player->getName(), function (?IslandData $playerData) use ($requestPlayer) {
                if ($playerData !== null) {
                    $playerData->addPartner($requestPlayer);
                }
            });
            $player->sendMessage(Skyblock::$prefix . API::getMessage("island-partner-accept"));
            $requestPlayerObj->sendMessage(Skyblock::$prefix . API::getMessage("island-partner-accepted"));
            return;
        }
        $player->sendMessage(Skyblock::$prefix . API::getMessage("island-player-not-active"));
    }

    public static function partnerRequest(Player $player, string $selectedPlayer): void
    {
        $selectedPlayerObj = self::getServer()->getPlayerExact($selectedPlayer);
        if ($selectedPlayerObj instanceof Player) {
            if ($selectedPlayerObj->getName() === $player->getName()) {
                $player->sendMessage(Skyblock::$prefix . API::getMessage("island-partner-self"));
                return;
            }
            IslandData::get($player->getName(), function (?IslandData $playerData) use ($selectedPlayerObj, $player) {
                $partners = $playerData ? $playerData->getPartners() : [];
                if (in_array($selectedPlayerObj->getName(), $partners)) {
                    $player->sendMessage(Skyblock::$prefix . API::getMessage("island-partner-already"));
                    return;
                }
                $selectedPlayerObj->sendForm(new PartnerRequestForm($player));
                $player->sendMessage(Skyblock::$prefix . API::getMessage("island-partner-request-sent", ["{player}" => $selectedPlayerObj->getName()]));
            });
            return;
        }
        $player->sendMessage(Skyblock::$prefix . API::getMessage("island-player-not-active"));
    }

    public static function islandUnBanPlayer(Player $player, string $selectedPlayer): void
    {
        IslandData::get($player->getName(), function (?IslandData $islandData) use ($selectedPlayer) {
            if ($islandData !== null) {
                $islandData->removeBanned($selectedPlayer);
            }
        });
        $player->sendMessage(Skyblock::$prefix . API::getMessage("island-unban"));
    }

    public static function islandBanPlayer(Player $player, string $selectedPlayer): void
    {
        $selectedPlayerObj = self::getServer()->getPlayerExact($selectedPlayer);
        if ($selectedPlayerObj instanceof Player) {
            $defaultWorld = self::getServer()->getWorldManager()->getDefaultWorld();
            if (!$defaultWorld instanceof World) {
                return;
            }
            IslandData::get($player->getName(), function (?IslandData $islandData) use ($selectedPlayerObj, $player, $defaultWorld) {
                if ($islandData !== null) {
                    $islandData->addBanned($selectedPlayerObj->getName());
                }
                $selectedPlayerObj->teleport($defaultWorld->getSpawnLocation());
                $selectedPlayerObj->sendMessage(Skyblock::$prefix . API::getMessage("island-ban-notify"));
                $player->sendMessage(Skyblock::$prefix . API::getMessage("island-ban"));
            });
            return;
        }
        $player->sendMessage(Skyblock::$prefix . API::getMessage("island-player-not-active"));
    }

    public static function islandKickPlayer(Player $player, string $selectedPlayer): void
    {
        $selectedPlayer = self::getServer()->getPlayerExact($selectedPlayer);
        if ($selectedPlayer instanceof Player) {
            if ($selectedPlayer->getName() === $player->getName()) {
                $player->sendMessage(Skyblock::$prefix . API::getMessage("island-kick-self"));
                return;
            }
            $defaultWorld = self::getServer()->getWorldManager()->getDefaultWorld();
            if (!$defaultWorld instanceof World) {
                return;
            }

            $selectedPlayer->teleport($defaultWorld->getSpawnLocation());
            $selectedPlayer->sendMessage(Skyblock::$prefix . API::getMessage("island-kick-notify"));
            $player->sendMessage(Skyblock::$prefix . API::getMessage("island-kick"));
        } else {
            $player->sendMessage(Skyblock::$prefix . API::getMessage("island-player-not-active"));
        }
    }

    public static function teleportPartnerIsland(Player $player, string $selectedPlayer): void
    {
        IslandData::get($selectedPlayer, function (?IslandData $islandData) use ($player, $selectedPlayer): void {
            if ($islandData === null) {
                $player->sendMessage(Skyblock::$prefix . API::getMessage("island-teleport-deleted"));
                return;
            }
            $settings = $islandData->getSettings();
            $status = $settings['de-active-teleport'] ?? false;
            if ($status) {
                if (!self::getServer()->getWorldManager()->isWorldLoaded($selectedPlayer)) {
                    self::getServer()->getWorldManager()->loadWorld($selectedPlayer);
                }
                $world = self::getServer()->getWorldManager()->getWorldByName($selectedPlayer);
                if (!$world instanceof World) {
                    return;
                }

                $player->teleport($world->getSpawnLocation());
                $player->sendMessage(Skyblock::$prefix . API::getMessage("island-teleport-partner"));
            } else {
                $player->sendMessage(Skyblock::$prefix . API::getMessage("island-teleport-inactive"));
            }
        });
    }

    public static function changePartnerSettings(Player $player, bool $interact, bool $place, bool $break, bool $pickingUp, bool $deActiveTeleport): void
    {
        IslandData::get($player->getName(), function (?IslandData $islandData) use ($interact, $place, $break, $pickingUp, $deActiveTeleport, $player) {
            if ($islandData !== null) {
                $settings = $islandData->getSettings();
                $settings['interact'] = $interact;
                $settings['place'] = $place;
                $settings['break'] = $break;
                $settings['picking-up'] = $pickingUp;
                $settings['de-active-teleport'] = $deActiveTeleport;
                $islandData->setSettings($settings);
            }
            $player->sendMessage(Skyblock::$prefix . API::getMessage("island-settings-saved"));
        });
    }

    public static function teleportToIsland(Player $player): void
    {
        if (!self::getServer()->getWorldManager()->isWorldLoaded($player->getName())) {
            self::getServer()->getWorldManager()->loadWorld($player->getName());
        }
        $world = self::getServer()->getWorldManager()->getWorldByName($player->getName());
        if (!$world instanceof World) {
            return;
        }

        $player->teleport($world->getSpawnLocation());
        $player->sendMessage(Skyblock::$prefix . API::getMessage("island-teleport-own"));
    }

    public static function setIslandSpawnLocation(Player $player): void
    {
        if ($player->getWorld()->getFolderName() === $player->getName()) {
            $player->getWorld()->setSpawnLocation($player->getPosition()->asVector3());
            $player->sendMessage(Skyblock::$prefix . API::getMessage("island-spawn-set"));
            return;
        }
        $player->sendMessage(Skyblock::$prefix . API::getMessage("island-spawn-not-own"));
    }

    public static function changeIslandVisit(Player $player): void
    {
        IslandData::get($player->getName(), function (?IslandData $islandData) use ($player): void {
            if ($islandData !== null) {
                if (!$islandData->getVisit()) {
                    $player->sendMessage(Skyblock::$prefix . API::getMessage("island-visit-open"));
                    $islandData->setVisit(true);
                } else {
                    $player->sendMessage(Skyblock::$prefix . API::getMessage("island-visit-closed"));
                    $islandData->setVisit(false);
                }
            } else {
                $player->sendMessage(Skyblock::$prefix . API::getMessage("island-error"));
            }
        });
    }

    public static function islandCreate(Player $player, string $islandType): void
    {
        $playerName = $player->getName();
        $worldPath = Skyblock::getInstance()->getDataFolder() . "island/$islandType.zip";

        $zip = new ZipArchive();
        $zip->open($worldPath);
        mkdir(self::getServer()->getDataPath() . "worlds/$playerName");
        $zip->extractTo(self::getServer()->getDataPath() . "worlds/$playerName");
        $zip->close();

        self::getServer()->getWorldManager()->loadWorld($playerName);

        IslandData::get($player->getName(), function (?IslandData $existingIsland) use ($player, $playerName): void {
            $deleteTime = $existingIsland ? $existingIsland->getSettings()['delete-time'] ?? null : null;
            $partners = $existingIsland ? $existingIsland->getPartners() : [];
            $initialData = [
                "settings" => [
                    "interact" => false,
                    "place" => false,
                    "break" => false,
                    "picking-up" => false,
                    "de-active-teleport" => false,
                    "delete-time" => $deleteTime
                ],
                "banneds" => [],
                "partners" => $partners,
                "members" => [],
                "visit" => false
            ];
            IslandData::create($player->getName(), $initialData);

            //Teleporting
            self::getServer()->getWorldManager()->loadWorld($player->getName());
            $world = self::getServer()->getWorldManager()->getWorldByName($player->getName());
            if (!$world instanceof World) {
                return;
            }

            $player->teleport($world->getSpawnLocation());
            $player->getWorld()->requestChunkPopulation($player->getPosition()->getFloorX() >> 4, $player->getPosition()->getFloorZ() >> 4, null);
            $player->sendMessage(Skyblock::$prefix . API::getMessage("island-create"));
        });
    }

    public static function islandRemove(Player $player): void
    {
        IslandData::get($player->getName(), function (?IslandData $islandData) use ($player): void {
            if ($islandData === null) {
                $player->sendMessage(Skyblock::$prefix . API::getMessage("island-error"));
                return;
            }
            $deleteTime = $islandData->getSettings()['delete-time'] ?? null;

            if ($deleteTime === null || time() > (int)$deleteTime) {
                self::islandDataDelete($player, function () use ($player) {
                    $player->sendMessage(Skyblock::$prefix . API::getMessage("island-delete-success"));
                });
                return;
            }
            $deleteTime = $deleteTime - time();
            $day = floor($deleteTime / 86400);
            $hourSecond = $deleteTime % 86400;
            $hour = floor($hourSecond / 3600);
            $minuteHour = $hourSecond % 3600;
            $minute = floor($minuteHour / 60);
            $player->sendMessage(Skyblock::$prefix . "fYou have to wait §6" . $day . " §fday, §6" . $hour . " §fhour, §6" . $minute . " §fTo be able to delete your island!");
        });
    }

    public static function islandDataDelete(Player $player, callable $callback = null): void
    {
        $world = self::getServer()->getWorldManager()->getWorldByName($player->getName());
        if (!$world instanceof World) {
            if ($callback) {
                $callback();
            }
            return;
        }

        foreach ($world->getPlayers() as $islandPlayer) {
            $defaultWorld = self::getServer()->getWorldManager()->getWorldByName(API::getHub());
            if (!$defaultWorld instanceof World) {
                if ($callback) {
                    $callback();
                }
                return;
            }

            $islandPlayer->teleport($defaultWorld->getSpawnLocation());
            $islandPlayer->sendMessage(Skyblock::$prefix . API::getMessage("island-delete-notify"));
        }

        IslandData::get($player->getName(), function (?IslandData $islandData) use ($player, $world, $callback): void {
            if ($islandData === null) {
                if ($callback) {
                    $callback();
                }
                return;
            }

            $partners = $islandData->getPartners();
            $partnerCount = count($partners);
            $processed = 0;

            if ($partnerCount == 0) {
                $worldName = Skyblock::getInstance()->getServer()->getDataPath() . "/worlds/" . $player->getName();
                self::getServer()->getWorldManager()->unloadWorld($world);
                self::worldDelete($worldName);
                $islandData->delete();
                if ($callback) {
                    $callback();
                }
            } else {
                foreach ($partners as $partner) {
                    IslandData::get($partner, function (?IslandData $partnerData) use ($player, &$processed, $partnerCount, $world, $islandData, $callback): void {
                        if ($partnerData !== null) {
                            $partnerData->removePartner($player->getName());
                        }
                        $processed++;
                        if ($processed == $partnerCount) {
                            $worldName = Skyblock::getInstance()->getServer()->getDataPath() . "/worlds/" . $player->getName();
                            self::getServer()->getWorldManager()->unloadWorld($world);
                            self::worldDelete($worldName);
                            $islandData->delete();
                            if ($callback) {
                                $callback();
                            }
                        }
                    });
                }
            }
        });
    }

    public static function worldDelete(string $world): int
    {
        $file = 1;
        if (basename($world) == "." || basename($world) == "..") {
            return 0;
        }
        $scanDir = scandir($world);
        if (!$scanDir) {
            return 0;
        }

        foreach ($scanDir as $item) {
            if ($item != "."/* || $item != ".."*/) {
                if (is_dir($world . DIRECTORY_SEPARATOR . $item)) {
                    $file += self::worldDelete($world . DIRECTORY_SEPARATOR . $item);
                }
                if (is_file($world . DIRECTORY_SEPARATOR . $item)) {
                    $file += unlink($world . DIRECTORY_SEPARATOR . $item);
                }
            }
        }
        rmdir($world);
        return $file;
    }
}
