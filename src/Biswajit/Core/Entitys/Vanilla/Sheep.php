<?php

declare(strict_types=1);

namespace Biswajit\Core\Entitys\Vanilla;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use Throwable;

class Sheep extends VanillaEntity
{
    private const RANDOM_TIME = 120;
    private int $lastRandomTime = 0;

    public function __construct(Location $location, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $nbt);
    }

    public function getName(): string
    {
        return "Sheep";
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
        return new EntitySizeInfo(1.3, 0.9);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::SHEEP;
    }

    public function getDrops(): array
    {
        $drops = [
            VanillaBlocks::WOOL()->asItem()->setCount(random_int(1, 3))
        ];

        if (random_int(0, 99) < 50) {
            $drops[] = VanillaItems::RAW_MUTTON()->setCount(random_int(1, 2));
        }

        return $drops;
    }

    public function getSpeed(): float
    {
        return 0.23;
    }
}
