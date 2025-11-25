<?php

declare(strict_types=1);

namespace Biswajit\Core\Blocks;

use pocketmine\block\Farmland;

class CustomFarmLand extends Farmland
{
    public function onRandomTick(): void
    {
        //farmland will be usable without water
    }

}
