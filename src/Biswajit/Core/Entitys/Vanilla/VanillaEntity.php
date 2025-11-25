<?php

namespace Biswajit\Core\Entitys\Vanilla;

use Biswajit\Core\Pathfinder\AsyncPathfinder;
use IvanCraft623\Pathfinder\evaluator\WalkNodeEvaluator;
use pocketmine\block\BlockTypeIds;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

abstract class VanillaEntity extends Living
{
    public AsyncPathfinder $pathfinder;

    private const MOVE_UPDATE_INTERVAL = 1;
    private int $moveTick = 0;

    public function __construct(Location $location, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $nbt);

        $evaluator = new WalkNodeEvaluator();
        $this->pathfinder = new AsyncPathfinder($this, $evaluator);
    }

    public function getName(): string
    {
        return "VanillaEntity";
    }

    public function onUpdate(int $currentTick): bool
    {
        $update = parent::onUpdate($currentTick);

        $this->onTick();

        if (++$this->moveTick >= self::MOVE_UPDATE_INTERVAL) {
            $this->pathfinder->updateMovement();
            $this->moveTick = 0;
        }


        return $update;
    }

    public function generateRandomPosition(): ?Vector3
    {
        $ValueSubtract = -16;
        $ValueAdd = 16;
        $xx = mt_rand($ValueSubtract, $ValueAdd);
        $zz = mt_rand($ValueSubtract, $ValueAdd);

        $world = $this->getWorld();
        $startX = (int) floor($this->getPosition()->getX() + $xx);
        $startZ = (int) floor($this->getPosition()->getZ() + $zz);
        $startY = (int) floor($this->getPosition()->getY());

        for ($y = $startY; $y >= $startY - 10; $y--) {
            $block = $world->getBlockAt($startX, $y, $startZ);
            $below = $world->getBlockAt($startX, $y - 1, $startZ);

            if ($block->getTypeId() === BlockTypeIds::AIR && $below->getTypeId() !== BlockTypeIds::AIR) {
                return new Vector3($startX, $y, $startZ);
            }
        }

        for ($y = $startY + 1; $y <= $startY + 10; $y++) {
            $block = $world->getBlockAt($startX, $y, $startZ);
            $below = $world->getBlockAt($startX, $y - 1, $startZ);

            if ($block->getTypeId() === BlockTypeIds::AIR && $below->getTypeId() !== BlockTypeIds::AIR) {
                return new Vector3($startX, $y, $startZ);
            }
        }

        return null;
    }


    public function attack(EntityDamageEvent $source): void
    {
        if ($source instanceof EntityDamageByEntityEvent) {
        }
        parent::attack($source);
    }

    protected function wanderRandomly(): void
    {
        $currentPath = $this->pathfinder->getCurrentPath();

        if ($currentPath === null) {
            $randomPos = $this->generateRandomPosition();
            if ($randomPos !== null && !$this->pathfinder->isCalculating()) {
                $this->pathfinder->findPathAsync($randomPos, function ($path) {
                    // Path found callback
                });
            }
        }
    }

    abstract public function onTick(): void;
    abstract public function getSpeed(): float;
}
