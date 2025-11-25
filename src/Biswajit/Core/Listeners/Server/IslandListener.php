<?php

declare(strict_types=1);

namespace Biswajit\Core\Listeners\Server;

use Biswajit\Core\API;
use Biswajit\Core\Managers\IslandManager;
use Biswajit\Core\Menus\player\PlayerMenu;
use Biswajit\Core\Skyblock;
use Biswajit\Core\Utils\Utils;
use Biswajit\Core\Sessions\IslandData;
use Biswajit\Core\Player;
use pocketmine\block\BlockTypeIds;
use pocketmine\Server;
use pocketmine\world\World;
use pocketmine\event\Listener;
use pocketmine\inventory\Inventory;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\entity\EntityItemPickupEvent;

class IslandListener implements Listener
{
    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();

        if (!$player instanceof Player) {
            return;
        }

        IslandData::get($player->getName(), function (?IslandData $playerData) use ($player) {
            if (is_null($playerData)) {
                $defaultWorld = Server::getInstance()->getWorldManager()->getWorldByName(API::getHub());
                if ($defaultWorld instanceof World) {
                    $player->teleport($defaultWorld->getSafeSpawn());
                }
            } else {
                IslandManager::teleportToIsland($player);
            }
        });
    }

    public function onQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        $islandData = IslandData::getSync($player->getName());

        if ($islandData !== null) {
            $world = Server::getInstance()->getWorldManager()->getWorldByName($player->getName());
            if ($world instanceof World && Server::getInstance()->getWorldManager()->isWorldLoaded($player->getName())) {
                Server::getInstance()->getWorldManager()->unloadWorld($world);
            }

            $partnerIslands = $islandData->getPartners();
            foreach ($partnerIslands as $partner) {
                $world = Server::getInstance()->getWorldManager()->getWorldByName($partner);
                if ($world instanceof World && Server::getInstance()->getWorldManager()->isWorldLoaded($partner)) {
                    Server::getInstance()->getWorldManager()->unloadWorld($world);
                }
            }
        }
    }

    /**
     * @priority LOWEST
     * @handleCancelled
     */
    public function onInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $level = $player->getWorld()->getFolderName();

        if ($level === API::getHub()) {
            return;
        }

        $event->cancel();

        $islandData = IslandData::getSync($level);
        if ($islandData !== null) {
            if ($level === $player->getName()) {
                $event->uncancel();
                return;
            }

            if (Server::getInstance()->isOp($player->getName())) {
                $event->uncancel();
                return;
            }

            if (in_array($player->getName(), $islandData->getPartners())) {
                if (($islandData->getSettings()["interact"] ?? false) === true) {
                    $event->uncancel();
                    return;
                }

                $event->cancel();
                $player->sendPopup(Skyblock::$prefix . "cYour partner won't let you interact!");
                return;
            }
        }
    }

    /**
     * @priority LOWEST
     * @handleCancelled
     */
    public function onPlaced(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();
        $level = $player->getWorld()->getFolderName();
        $islandData = IslandData::getSync($level);

        if ($level === API::getHub()) {
            return;
        }

        $event->cancel();

        if ($islandData !== null) {
            if ($level === $player->getName()) {
                $event->uncancel();
                return;
            }

            if (Server::getInstance()->isOp($player->getName())) {
                $event->uncancel();
                return;
            }

            if (in_array($player->getName(), $islandData->getPartners())) {
                if (($islandData->getSettings()["place"] ?? false) === true) {
                    $event->uncancel();
                    return;
                }

                $event->cancel();
                $player->sendPopup(Skyblock::$prefix . "cYour partner won't let you!");
                return;
            }
        }
    }

    /**
     * @priority LOWEST
     * @handleCancelled
     */
    public function onBreak(BlockBreakEvent $event): void
    {
        $player = $event->getPlayer();
        $level = $player->getWorld()->getFolderName();
        $islandData = IslandData::getSync($level);

        if ($level === API::getHub()) {
            return;
        }

        $event->cancel();

        if ($islandData !== null) {
            if ($level === $player->getName()) {
                $event->uncancel();
                $drops = $event->getDrops();
                foreach ($drops as $key => $drop) {
                    if ($player->getInventory()->canAddItem($drop)) {
                        $player->getInventory()->addItem($drop);
                        unset($drops[$key]);
                    } else {
                        $player->sendPopup("§l§eINVENTORY FULL");
                    }
                }

                $event->setDrops($drops);
                $xpDrops = $event->getXpDropAmount();
                $player->getXpManager()->addXp($xpDrops);
                $event->setXpDropAmount(0);
                return;
            }

            if (Server::getInstance()->isOp($player->getName())) {
                $event->uncancel();
                return;
            }

            if (in_array($player->getName(), $islandData->getPartners())) {
                if (($islandData->getSettings()["break"] ?? false) === true) {
                    $event->uncancel();
                    $drops = $event->getDrops();
                    foreach ($drops as $key => $drop) {
                        if ($player->getInventory()->canAddItem($drop)) {
                            $player->getInventory()->addItem($drop);
                            unset($drops[$key]);
                        } else {
                            $player->sendPopup("§l§eINVENTORY FULL");
                        }
                    }

                    $event->setDrops($drops);
                    $xpDrops = $event->getXpDropAmount();
                    $player->getXpManager()->addXp($xpDrops);
                    $event->setXpDropAmount(0);
                    return;
                }

                $event->cancel();
                $player->sendPopup(Skyblock::$prefix . "cYour partner won't let you!");
                return;
            }
        }
    }

    public function onPickingUp(EntityItemPickupEvent $event): void
    {
        $inventory = $event->getInventory();
        if (!$inventory instanceof Inventory) {
            return;
        }

        $viewers = $inventory->getViewers();
        foreach ($viewers as $player) {
            $level = $player->getWorld();
            $levelName = $level->getFolderName();
            $islandData = IslandData::getSync($levelName);

            if ($level === API::getHub()) {
                return;
            }

            if ($islandData !== null) {
                if ($levelName === $player->getName()) {
                    $event->uncancel();
                    return;
                }

                if (Server::getInstance()->isOp($player->getName())) {
                    $event->uncancel();
                    return;
                }

                if (in_array($player->getName(), $islandData->getPartners())) {
                    if (($islandData->getSettings()["picking-up"] ?? false) === true) {
                        $event->uncancel();
                        return;
                    }

                    $event->cancel();
                    $player->sendPopup(Skyblock::$prefix . "cYour partner won't let you!");
                    return;
                }
            }
            $event->cancel();
        }
    }

    public function onMove(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();
        $level = $player->getWorld()->getFolderName();

        if ($level === API::getHub()) {
            return;
        }

        $islandData = IslandData::getSync($level);
        if ($islandData !== null) {
            if (in_array($player->getName(), $islandData->getBanneds())) {
                if (!Server::getInstance()->isOp($player->getName())) {
                    $defaultWorld = Server::getInstance()->getWorldManager()->getDefaultWorld();
                    if (!$defaultWorld instanceof World) {
                        return;
                    }

                    $player->teleport($defaultWorld->getSpawnLocation());
                    $player->sendPopup(Skyblock::$prefix . "cYou are banned on this island!");
                }
            }
        }
        if ($player->getWorld()->getBlock($player->getPosition())->getTypeId() === BlockTypeIds::NETHER_PORTAL) {
            $world = Server::getInstance()->getWorldManager()->getWorldByName(API::getHub());
            $player->teleport($world->getSafeSpawn());
            $player->sendTitle("§6Welcome To Hub", "" . Utils::getServerName());
        }
    }

    public function onDamage(EntityDamageEvent $event): void
    {
        $player = $event->getEntity();
        if ($player instanceof Player) {
            $level = $player->getWorld()->getFolderName();

            if ($level === API::getHub()) {
                return;
            }

            if ($event instanceof EntityDamageByEntityEvent) {
                $damager = $event->getDamager();
                $entity = $event->getEntity();

                if (!$damager instanceof Player) {
                    return;
                }

                $damager->sendForm(new PlayerMenu($damager, $entity));
                $event->cancel();
            }

            if ($event->getCause() === EntityDamageEvent::CAUSE_VOID) {

                $world = Server::getInstance()->getWorldManager()->getWorldByName($player->getName());

                if (!$world instanceof World) {
                    return;
                }

                $event->cancel();

                $player->teleport($world->getSpawnLocation());

                if ($level === $player->getName()) {
                    if ($player->getXpManager()->getXpLevel() >= 7) {
                        $xp = 7;
                        $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - 7);
                        $player->sendMessage(Skyblock::$prefix . API::getMessage("death-xp-loss", ["{xp}" => (string)$xp]));
                    }
                }
            }

            $cancelCauses = [
             EntityDamageEvent::CAUSE_FALL,
             EntityDamageEvent::CAUSE_FIRE,
             EntityDamageEvent::CAUSE_FIRE_TICK,
             EntityDamageEvent::CAUSE_DROWNING,
             EntityDamageEvent::CAUSE_SUFFOCATION,
             EntityDamageEvent::CAUSE_MAGIC
              ];

            if (in_array($event->getCause(), $cancelCauses, true)) {
                $event->cancel();
                if ($level === $player->getName()) {
                    $player->damagePlayer($event->getFinalDamage() * 2);
                }
            }
        }
    }
}
