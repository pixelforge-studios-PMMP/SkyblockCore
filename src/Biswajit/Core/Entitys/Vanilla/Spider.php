<?php

declare(strict_types=1);

namespace Biswajit\Core\Entitys\Vanilla;

use Biswajit\Core\Events\Entity\EntityAttackPlayer;
use Biswajit\Core\Player;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use Throwable;

class Spider extends VanillaEntity
{
    private const SEARCH_RADIUS = 16.0;

    private const MIN_Y = 1;
    private const ATTACK_RANGE = 1.5;
    private const ATTACK_RANGE_SQ = self::ATTACK_RANGE * self::ATTACK_RANGE;
    private const ATTACK_COOLDOWN = 1.0; // seconds

    private const RANDOM_TIME = 120;
    private int $lastRandomTime = 0;

    private float $lastAttackTime = 0.0;

    public function __construct(Location $location, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $nbt);
    }

    public function getName(): string
    {
        return "Spider";
    }

    public function onTick(): void
    {
        try {
            if ($this->getTargetEntityId() === null) {
                $players = [];
                foreach ($this->getWorld()->getNearbyEntities($this->getBoundingBox()->expandedCopy(self::SEARCH_RADIUS, self::SEARCH_RADIUS, self::SEARCH_RADIUS), $this) as $entity) {
                    if ($entity instanceof Player && $entity->isSurvival()) {
                        $players[] = $entity;
                    }
                }

                if (!empty($players)) {
                    $this->setTargetEntity($players[array_rand($players)]);
                } else {
                    if (++$this->lastRandomTime >= self::RANDOM_TIME) {
                        $this->lastRandomTime = 0;
                        $this->pathfinder->resetPath();
                        $this->wanderRandomly();
                    }
                }
            } else {
                $playerId = $this->getTargetEntityId();
                $player = $this->getWorld()->getEntity($playerId);
                if ($player !== null) {
                    $this->lookAt($player->getPosition()->add(0, 1, 0));
                    $currentPath = $this->pathfinder->getCurrentPath();
                    if ($currentPath === null || $currentPath->isDone() || $currentPath->getTarget()->distanceSquared($player->getPosition()) > 1.0) {
                        $this->pathfinder->findPathAsync($player->getPosition(), function ($path) {
                            // Path found callback
                        });
                    }
                    $this->attackPlayer($player);
                } else {
                    $this->setTargetEntity(null);
                    if (++$this->lastRandomTime >= self::RANDOM_TIME) {
                        $this->lastRandomTime = 0;
                        $this->pathfinder->resetPath();
                        $this->wanderRandomly();
                    }
                }
            }
        } catch (Throwable $e) {
            $this->flagForDespawn();
        }
    }


    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(1.4, 0.9);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::SPIDER;
    }

    private function attackPlayer(Player $player): void
    {
        if ($this->getLocation()->getY() <= self::MIN_Y) {
            $this->flagForDespawn();
            return;
        }

        $pos = $player->getPosition();
        $dx = $pos->x - $this->getLocation()->getX();
        $dz = $pos->z - $this->getLocation()->getZ();

        $distSq = $dx * $dx + $dz * $dz;

        if ($distSq < self::ATTACK_RANGE_SQ) {
            if (!$player->isSurvival()) {
                $this->setTargetEntity(null);
            }
            if (is_null($this->getTargetEntity())) {
                return;
            }

            $currentTime = microtime(true);
            if ($currentTime - $this->lastAttackTime >= self::ATTACK_COOLDOWN) {
                $ev = new EntityAttackPlayer($this, $player, $this->getAttackDamage());
                $ev->call();
                $this->lastAttackTime = $currentTime;
            }
        }
    }

    public function getAttackDamage(): int
    {
        return 8;
    }

    public function getDrops(): array
    {
        $drops = [
            VanillaItems::STRING()->setCount(random_int(0, 2))
        ];

        if (random_int(0, 199) < 5) {
            $drops[] = VanillaItems::SPIDER_EYE();
        }

        return $drops;
    }

    public function getSpeed(): float
    {
        return 0.20;
    }
}
