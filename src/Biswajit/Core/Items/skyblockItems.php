<?php

declare(strict_types=1);

namespace Biswajit\Core\Items;

use Biswajit\Core\Skyblock;
use pocketmine\item\Item;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;

class skyblockItems extends Item
{
    /** @var array<string[]> */
    private array $description = [];

    public function setDescription(array $description): void
    {
        $this->description[spl_object_id($this)] = $description;
        $this->updateDescription();
    }

    public function getDescription(): array
    {
        return $this->description[spl_object_id($this)] ?? [];
    }

    public function updateDescription(): void
    {
        $lore = [];

        foreach ($this->getDescription() as $line) {
            $lore[] = "§7" . $line;
        }

        $this->setLore($lore);
    }

    public function addGlow(): void
    {
        $this->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(Skyblock::FAKE_ENCH_ID)));
    }
}
