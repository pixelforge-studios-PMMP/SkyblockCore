<?php

declare(strict_types=1);

namespace Biswajit\Core\Entitys\Minion\types;

use Biswajit\Core\API;
use Biswajit\Core\Entitys\Minion\MinionEntity;
use Biswajit\Core\Skyblock;
use Biswajit\Core\Utils\Utils;
use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\Crops;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\BlockBreakParticle;

class FarmerMinion extends MinionEntity
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
        if ($this->target === null) {
            return false;
        }
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
        $targetPos = $this->target->getPosition()->subtract(0, 1, 0);

        $DirtId = $this->getWorld()->getBlock($targetPos)->getTypeId();
        if ($DirtId !== BlockTypeIds::FARMLAND) {
            $this->getWorld()->setBlock($targetPos, VanillaBlocks::FARMLAND());
        }

        $this->getWorld()->setBlock($this->target->getPosition(), $block);
        $this->lookAt($this->target->getPosition());
        $this->growCrop($block, $this->target->getPosition());
        $this->target = null;
    }

    private function mineBlock(): void
    {
        $this->lookAt($this->target->getPosition());
        $this->broadcastMiningAnimation();

        $block = $this->getBlock();
        $this->getWorld()->setBlock($this->target->getPosition(), VanillaBlocks::AIR());
        $this->addItem($block->getDrops(VanillaItems::DIAMOND_HOE()));
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
        $targetBlockId = $this->getBlock()->getName();

        for ($x = -self::SEARCH_RADIUS; $x <= self::SEARCH_RADIUS; $x++) {
            for ($z = -self::SEARCH_RADIUS; $z <= self::SEARCH_RADIUS; $z++) {
                if ($x === 0 && $z === 0) {
                    continue;
                }

                $block = $this->getWorld()->getBlock($this->getPosition()->add($x, 0, $z));
                $blockId = $block->getName();


                if ($blockId === "Air" || $blockId === $targetBlockId) {
                    $validBlocks[] = $block;
                }
            }
        }

        return $validBlocks;
    }

    private function growCrop(Block $block, Vector3 $position): void
    {
        Skyblock::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($block, $position): void {
            if ($block instanceof Crops && $block->getAge() < $block->getMaxAge()) {
                $newBlock = clone $block;
                $newBlock->setAge($block::MAX_AGE);
                $this->getWorld()->setBlock($position, $newBlock, true);
            }
        }), 60);
    }

    private function getBlock(): Block
    {
        $targetId = $this->getTargetId();
        return match(strtolower($targetId)) {
            "wheat" => VanillaBlocks::WHEAT(),
            "melon" => VanillaBlocks::MELON(),
            "pumpkin" => VanillaBlocks::PUMPKIN(),
            "carrot" => VanillaBlocks::CARROTS(),
            "potato" => VanillaBlocks::POTATOES(),
            default => StringToItemParser::getInstance()->parse($targetId)->getBlock()
        };
    }

    public function getEgg(): Item
    {
        return $this->createMinionItem(ucfirst($this->getTargetId()));
    }

    private function createMinionItem(string $item): Item
    {
        $itemName = strtolower($item);
        $name = str_replace(' ', '_', $itemName);
        $minionItem = API::getItem($name);

        $minionItem->setCustomName($this->formatMinionName($item))->setLore($this->getMinionLore());

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
        $this->getInventory()->setItemInHand(VanillaItems::WOODEN_HOE());
    }
}
