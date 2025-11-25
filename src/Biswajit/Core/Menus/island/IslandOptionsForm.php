<?php

declare(strict_types=1);

namespace Biswajit\Core\Menus\island;

use Exception;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use Biswajit\Core\Skyblock;
use dktapps\pmforms\MenuOption;
use Biswajit\Core\Managers\IslandManager;
use Biswajit\Core\Menus\island\partner\PartnerOptionsForm;
use Biswajit\Core\Menus\island\partner\PartnerTeleportForm;
use Biswajit\Core\Sessions\IslandData;

class IslandOptionsForm extends MenuForm
{
    public function __construct(Player $player)
    {
        IslandData::get($player->getName(), function (?IslandData $islandData): void {
            $visitStatus = $islandData ? $islandData->getVisit() : false;

            parent::__construct(
                Skyblock::$prefix . "Island",
                "",
                [
                    new MenuOption("§bTeleport To Island\n§d§l»§r Tap to select!", new FormIcon('https://cdn-icons-png.flaticon.com/128/619/619005.png', FormIcon::IMAGE_TYPE_URL)),
                    new MenuOption("§bTeleport to Partner Island\n§d§l»§r Tap to select!", new FormIcon('https://cdn-icons-png.flaticon.com/128/2010/2010261.png', FormIcon::IMAGE_TYPE_URL)),
                    new MenuOption("§bPartner Options\n§d§l»§r Tap to select!", new FormIcon('https://cdn-icons-png.flaticon.com/128/3315/3315183.png', FormIcon::IMAGE_TYPE_URL)),
                    new MenuOption("§bSet Island Spawn\n§d§l»§r Tap to select!", new FormIcon('https://cdn-icons-png.flaticon.com/128/5569/5569268.png', FormIcon::IMAGE_TYPE_URL)),
                    new MenuOption("§bIsland Visit: " . ($visitStatus === true ? "§l§2OPEN" : "§l§4CLOSED") . "\n§d§l»§r Tap to select!", new FormIcon('https://cdn-icons-png.flaticon.com/128/1541/1541400.png', FormIcon::IMAGE_TYPE_URL)),
                    new MenuOption("§bVisitable Islands\n§d§l»§r Tap to select!", new FormIcon('https://cdn-icons-png.flaticon.com/128/854/854878.png', FormIcon::IMAGE_TYPE_URL)),
                    new MenuOption("§bPlayers on the Island\n§d§l»§r Tap to select!", new FormIcon('https://cdn-icons-png.flaticon.com/128/166/166344.png', FormIcon::IMAGE_TYPE_URL)),
                    new MenuOption("§bKick Player From Your Island\n§d§l»§r Tap to select!", new FormIcon('https://cdn-icons-png.flaticon.com/128/4578/4578073.png', FormIcon::IMAGE_TYPE_URL)),
                    new MenuOption("§bBan Players From Your Island\n§d§l»§r Tap to select!", new FormIcon('https://cdn-icons-png.flaticon.com/128/1595/1595649.png', FormIcon::IMAGE_TYPE_URL)),
                    new MenuOption("§bUnban Banned Player\n§d§l»§r Tap to select!", new FormIcon('https://cdn-icons-png.flaticon.com/128/3699/3699516.png', FormIcon::IMAGE_TYPE_URL)),
                    new MenuOption("§bTeleport Jerry\n§d§l»§r Tap to select!", new FormIcon('https://i.pinimg.com/originals/6d/71/eb/6d71eb4e2987eee7b11718ddf97bf297.jpg', FormIcon::IMAGE_TYPE_URL)),
                    new MenuOption("§bDelete Your Island\n§d§l»§r Tap to select!", new FormIcon('https://cdn-icons-png.flaticon.com/128/3496/3496416.png', FormIcon::IMAGE_TYPE_URL))
                ],
                function (Player $player, int $option): void {
                    switch ($option) {
                        case 0:
                            IslandManager::teleportToIsland($player);
                            break;
                        case 1:
                            $player->sendForm(new PartnerTeleportForm($player));
                            break;
                        case 2:
                            $player->sendForm(new PartnerOptionsForm());
                            break;
                        case 3:
                            IslandManager::setIslandSpawnLocation($player);
                            break;
                        case 4:
                            IslandManager::changeIslandVisit($player);
                            break;
                        case 5:
                            $player->sendForm(new IslandVisitAllOpenForm());
                            break;
                        case 6:
                            $player->sendForm(new IslandPlayersForm($player));
                            break;
                        case 7:
                            $player->sendForm(new IslandKickPlayerForm($player));
                            break;
                        case 8:
                            $player->sendForm(new IslandBanPlayerForm($player));
                            break;
                        case 9:
                            $player->sendForm(new IslandUnBanPlayerForm($player));
                            break;
                        case 10:
                            if ($player->getWorld()->getFolderName() === $player->getName()) {
                                foreach ($player->getWorld()->getEntities() as $entity) {
                                    if (str_contains($entity->getNameTag(), "Jerry") && !$entity instanceof Player) {
                                        $entity->teleport($player->getPosition());
                                        break;
                                    }
                                }
                                $player->sendMessage(Skyblock::$prefix . "aSuccessfully teleported Jerry to you!");
                                break;
                            }
                            $player->sendMessage(Skyblock::$prefix . "cYou must be on your island to do this!");
                            break;
                        case 11:
                            $player->sendForm(new IslandDeleteConfirmForm());
                            break;
                        default:
                            throw new Exception('Unexpected value');
                    }
                }
            );
        });
    }
}
