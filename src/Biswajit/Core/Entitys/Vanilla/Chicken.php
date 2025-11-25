<?php

declare(strict_types=1);

namespace Biswajit\Core\Entitys\Vanilla;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use Throwable;

class Chicken extends VanillaEntity
{
    private const Random_Time = 120;
    private int $lastRandomTime = 0;

    public function __construct(Location $location, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $nbt);
    }

    public function getName(): string
    {
        return "Chicken";
    }

    public function onTick(): void
    {
        try {
            if (++$this->lastRandomTime >= self::Random_Time) {
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
        return new EntitySizeInfo(0.75, 0.7);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::CHICKEN;
    }

    public function getDrops(): array
    {
        $drops = [
            VanillaItems::RAW_CHICKEN()->setCount(random_int(1, 1))
        ];

        if (random_int(0, 99) < 5) {
            $drops[] = VanillaItems::FEATHER()->setCount(random_int(0, 2));
        }

        return $drops;
    }

    public function getSpeed(): float
    {
        return 0.25;
    }
}
