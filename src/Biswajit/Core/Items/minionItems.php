<?php

declare(strict_types=1);

namespace Biswajit\Core\Items;

use customiesdevs\customies\item\ItemComponentsTrait;

class minionItems extends skyblockItems
{
    use ItemComponentsTrait;

    public function isFireProof(): bool
    {
        return true;
    }

    public function getMaxStackSize(): int
    {
        return 1;
    }
}
