<?php

declare(strict_types=1);

namespace Biswajit\Core\Items\items;

use customiesdevs\customies\item\ItemComponentsTrait;
use customiesdevs\customies\item\ItemComponents;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;

class guiItems extends Item implements ItemComponents
{
    use ItemComponentsTrait;

    public function __construct(string $texture)
    {
        parent::__construct(new ItemIdentifier(ItemTypeIds::newId()), "guiItems");
        $this->initComponent($texture);
    }

    public function isFireProof(): bool
    {
        return true;
    }
}
