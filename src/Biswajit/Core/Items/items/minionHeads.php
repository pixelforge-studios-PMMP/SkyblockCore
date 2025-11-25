<?php

namespace Biswajit\Core\Items\items;

use Biswajit\Core\Items\minionItems;
use customiesdevs\customies\item\ItemComponents;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;

class minionHeads extends minionItems implements ItemComponents
{
    private string $type;

    public function __construct(string $texture, string $name, string $type)
    {
        parent::__construct(new ItemIdentifier(ItemTypeIds::newId()), $name);
        $this->initComponent($texture);
        $this->addGlow();
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

}
