<?php

declare(strict_types=1);

namespace Biswajit\Core\Entitys\Minion\types;

use Biswajit\Core\API;
use Biswajit\Core\Entitys\Minion\MinionEntity;
use Biswajit\Core\Skyblock;
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
use pocketmine\math\Vector3;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\World;

class ForagingMinion extends MinionEntity
{
    private const LOG_HEIGHT = 4;
    private const LEAF_DISTANCE = 4;

    public function onTick(): void
    {
        if ($this->target === null) {
            $this->target = $this->getTargetingBlock();
        }

        if ($this->target === null || !$this->validateMiningArea()) {
            return;
        }

        $this->processMining();
    }

    private function validateMiningArea(): bool
    {
        $block = $this->getBlock();
        if ($this->target === null) {
            $this->setNameTag("§cNo valid target!");
            $this->setNameTagAlwaysVisible(true);
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
            $this->placeTree();
        } else {
            $this->mineTree();
        }
    }

    /**
     * Places a tree (logs and leaves) at the target position.
     */
    private function placeTree(): void
    {
        $world = $this->getWorld();
        $pos = $this->target->getPosition();
        $logBlock = $this->getSapling();

        $world->setBlock($pos, $logBlock);
        $this->growSapling($logBlock, $pos, $world);

        $this->target = null;
    }

    /**
     * Mines a tree (logs and leaves) at the target position.
     */
    private function mineTree(): void
    {
        $this->lookAt($this->target->getPosition());
        $this->broadcastMiningAnimation();
        $pos = $this->target->getPosition();
        $world = $this->getWorld();
        $logTypeId = $this->getBlock()->getTypeId();

        for ($y = 0; $y < self::LOG_HEIGHT; $y++) {
            $block = $world->getBlock($pos->add(0, $y, 0));
            if ($block->getTypeId() === $logTypeId) {
                $world->setBlock($pos->add(0, $y, 0), VanillaBlocks::AIR());
            }
        }

        $this->placeOrRemoveLeaves($pos->add(0, self::LOG_HEIGHT, 0), false);

        $drops = $this->getBlock()->getDrops(VanillaItems::DIAMOND_AXE());

        foreach ($drops as $drop) {
            $drop->setCount($drop->getCount() + 3);
        }

        $this->addItem($drop);
        $this->target = null;
    }

    /**
     * Places or removes leaves in a spherical pattern.
     * @param Vector3 $center
     * @param bool $place
     */
    private function placeOrRemoveLeaves(Vector3 $center, bool $place): void
    {
        $world = $this->getWorld();
        for ($y = 0; $y < self::LEAF_DISTANCE; ++$y) {
            $size = ($y > 0 && $y < self::LEAF_DISTANCE - 1) ? 3.0 : 2.0;
            $node_distance = (int)(0.618 + $size);
            for ($x = -$node_distance; $x <= $node_distance; ++$x) {
                for ($z = -$node_distance; $z <= $node_distance; ++$z) {
                    $size_x = abs($x) + 0.5;
                    $size_z = abs($z) + 0.5;
                    if ($size_x * $size_x + $size_z * $size_z <= $size * $size) {
                        $blockPos = $center->add($x, $y, $z);
                        if ($place) {
                            $world->setBlock($blockPos, VanillaBlocks::OAK_LEAVES());
                        } else {
                            $block = $world->getBlock($blockPos);
                            if ($block->getTypeId() === VanillaBlocks::OAK_LEAVES()->getTypeId()) {
                                $world->setBlock($blockPos, VanillaBlocks::AIR());
                            }
                        }
                    }
                }
            }
        }
    }

    private function growSapling(Block $logBlock, Vector3 $pos, World $world): void
    {
        Skyblock::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($logBlock, $pos, $world): void {

            $log = $this->getBlock();
            for ($y = 0; $y < self::LOG_HEIGHT; $y++) {
                $world->setBlock($pos->add(0, $y, 0), $log);
            }

            $this->placeOrRemoveLeaves($pos->add(0, self::LOG_HEIGHT, 0), true);
        }), 60);
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

    /**
     * @return Block[]
     */
    private function scanSurroundingBlocks(): array
    {
        $validBlocks = [];
        $targetBlockId = $this->getTargetId();

        $positions = [
          [-2, -2], [-2, 0], [-2, 2], [0, 2],
          [2, 2], [2, 0], [2, -2], [0, -2]
        ];

        foreach ($positions as [$x, $z]) {
            $block = $this->getWorld()->getBlock($this->getPosition()->add($x, 0, $z));
            $blockId = strtolower($block->getName());

            if ($blockId === "air" || $blockId === $targetBlockId) {
                $validBlocks[] = $block;
            }
        }
        return $validBlocks;
    }

    private function getBlock(): Block
    {
        $targetId = $this->getTargetId();
        return StringToItemParser::getInstance()->parse($targetId)->getBlock();
    }

    public function getEgg(): Item
    {
        return $this->createMinionItem($this->getBlock()->getName());
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

    private function getSapling(): Block
    {
        $targetId = $this->getTargetId();
        return match(strtolower($targetId)) {
            "acacia log" => VanillaBlocks::ACACIA_SAPLING(),
            "birch log" => VanillaBlocks::BIRCH_SAPLING(),
            "dark oak log" => VanillaBlocks::DARK_OAK_SAPLING(),
            "jungle log" => VanillaBlocks::JUNGLE_SAPLING(),
            "oak log" => VanillaBlocks::OAK_SAPLING(),
            "spruce log" => VanillaBlocks::SPRUCE_SAPLING(),
            default => StringToItemParser::getInstance()->parse($targetId)->getBlock()
        };
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
        $this->getInventory()->setItemInHand(VanillaItems::WOODEN_AXE());
    }
}
