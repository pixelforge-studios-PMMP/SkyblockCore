<?php

declare(strict_types=1);

namespace Biswajit\Core\Entitys\Minion\types;

use Biswajit\Core\API;
use Biswajit\Core\Entitys\Minion\MinionEntity;
use Biswajit\Core\Utils\Utils;
use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\BlockBreakParticle;

class MinerMinion extends MinionEntity
{
    private const SEARCH_RADIUS = 2;

    public function onTick(): void
    {
        if ($this->target === null) {
            $this->target = $this->getTargetingBlock();
        }

        if (!$this->validateMiningArea()) {
            return;
        }

        $this->processMining();
    }

    private function validateMiningArea(): bool
    {
        $block = $this->getBlock();
        $isValidArea = $this->target->getTypeId() === BlockTypeIds::AIR || $this->target->getTypeId() === $block->getTypeId();

        if (!$isValidArea) {
            $this->setNameTag("§cThis location isn't perfect! :(");
            $this->setNameTagAlwaysVisible(true);
            return false;
        }

        $this->setNameTag("");
        $this->setNameTagAlwaysVisible(false);
        return true;
    }

    private function processMining(): void
    {
        if ($this->target->getTypeId() === BlockTypeIds::AIR) {
            $this->placeBlock();
            return;
        }

        $this->mineBlock();
    }

    private function placeBlock(): void
    {
        $block = $this->getBlock();
        $this->getWorld()->setBlock($this->target->getPosition(), $block);
        $this->lookAt($this->target->getPosition());
        $this->target = null;
    }

    private function mineBlock(): void
    {
        $this->lookAt($this->target->getPosition());
        $this->broadcastMiningAnimation();

        $block = $this->getBlock();
        $this->getWorld()->setBlock($this->target->getPosition(), VanillaBlocks::AIR());
        $this->addItem($block->getDrops(VanillaItems::DIAMOND_PICKAXE()));
        $this->target = null;
    }

    private function broadcastMiningAnimation(): void
    {
        $animatePacket = AnimatePacket::create($this->getId(), AnimatePacket::ACTION_SWING_ARM);
        $this->getWorld()->broadcastPacketToViewers($this->getPosition(), $animatePacket);

        $this->getWorld()->addParticle($this->target->getPosition()->add(0.5, 0.5, 0.5), new BlockBreakParticle($this->target));
    }

    public function getTargetingBlock(): ?Block
    {
        $validBlocks = $this->scanSurroundingBlocks();

        if (empty($validBlocks)) {
            return null;
        }

        return $validBlocks[array_rand($validBlocks)];
    }

    private function scanSurroundingBlocks(): array
    {
        $validBlocks = [];
        $targetBlockId = $this->getTargetId();

        for ($x = -self::SEARCH_RADIUS; $x <= self::SEARCH_RADIUS; $x++) {
            for ($z = -self::SEARCH_RADIUS; $z <= self::SEARCH_RADIUS; $z++) {
                if ($x === 0 && $z === 0) {
                    continue;
                }

                $block = $this->getWorld()->getBlock($this->getPosition()->add($x, -1, $z));
                $blockId = strtolower($block->getName());

                if ($blockId === "air" || $blockId === $targetBlockId) {
                    $validBlocks[] = $block;
                    $this->canWork = true;
                } else {
                    $this->canWork = false;
                }
            }
        }

        return $validBlocks;
    }

    private function getBlock(): Block
    {
        return StringToItemParser::getInstance()->parse($this->getTargetId())->getBlock();
    }

    public function getEgg(): Item
    {
        $block = $this->getBlock();
        $items = $block->getDrops(VanillaItems::DIAMOND_PICKAXE());

        foreach ($items as $item) {
            return $this->createMinionItem($item);
        }

        return VanillaItems::AIR();
    }

    private function createMinionItem(Item $item): Item
    {
        $itemName = strtolower($item->getName());
        $name = str_replace(' ', '_', $itemName);
        $minionItem = API::getItem($name);

        $minionItem->setCustomName($this->formatMinionName($item->getName()))->setLore($this->getMinionLore());

        $minionItem->getNamedTag()->setTag("Information", $this->minionInfo);
        return $minionItem;
    }

    private function formatMinionName(string $itemName): string
    {
        return TextFormat::RESET . TextFormat::YELLOW .
             $itemName . ' Minion ' .
             Utils::getRomanNumeral($this->getLevel());
    }

    private function getMinionLore(): array
    {
        return [
          "§r§7Place this minion and it will\n§r§7start generating and mining blocks!",
          "§r§7Requires an open area to spawn\n§r§7blocks. Minions also work when you are offline!",
          "\n§r§eType: §b" . $this->getType(),
          "§r§eLevel: §b" . Utils::getRomanNumeral($this->getLevel())
        ];
    }

    public function setUp(): void
    {
        $this->getInventory()->setItemInHand(VanillaItems::WOODEN_PICKAXE());
    }
}
