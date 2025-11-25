<?php

declare(strict_types=1);

namespace Biswajit\Core\Entitys\Vanilla;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use Throwable;

class Pig extends VanillaEntity
{
    private const RANDOM_TIME = 120;
    private int $lastRandomTime = 0;

    public function __construct(Location $location, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $nbt);
    }

    public function getName(): string
    {
        return "Pig";
    }

    public function onTick(): void
    {
        try {
            if (++$this->lastRandomTime >= self::RANDOM_TIME) {
                $this->lastRandomTime = 0;
                $this->pathfinder->resetPath();
                $this->wanderRandomly();
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
        return EntityIds::PIG;
    }

    public function getDrops(): array
    {
        $drops = [
            VanillaItems::RAW_PORKCHOP()->setCount(random_int(1, 3))
        ];

        return $drops;
    }

    public function getSpeed(): float
    {
        return 0.25;
    }
}
