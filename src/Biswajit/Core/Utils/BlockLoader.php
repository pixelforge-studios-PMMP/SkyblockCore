<?php

declare(strict_types=1);

namespace Biswajit\Core\Utils;

use Biswajit\Core\Blocks\CustomFarmLand;
use Biswajit\Core\Blocks\EndPortal;
use Biswajit\Core\Blocks\Grindstone;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\BlockTypeInfo;
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\block\BlockTypeNames;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\StringToItemParser;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use ReflectionClass;

class BlockLoader
{
    public static function initialize(): void
    {
        self::registerBlocks();
        $pool = Server::getInstance()->getAsyncPool();
        $pool->addWorkerStartHook(function (int $worker) use ($pool): void {
            $pool->submitTaskToWorker(new class () extends AsyncTask {
                public function onRun(): void
                {
                    BlockLoader::registerBlocks();
                }
            }, $worker);
        });

        $oldFarmLand = VanillaBlocks::FARMLAND();
        $newFarmLand = new CustomFarmLand(
            new BlockIdentifier($oldFarmLand->getTypeId()),
            $oldFarmLand->getName(),
            new BlockTypeInfo($oldFarmLand->getBreakInfo(), $oldFarmLand->getTypeTags())
        );

        /**
         * Overwriting the entry in the RuntimeBlockStateRegistry by calling our custom version of its
         * @see RuntimeBlockStateRegistry::register() method without prohibiting the overwriting of existing entries
         */
        (function (CustomFarmLand $block): void {
            $typeId = $block->getTypeId();
            RuntimeBlockStateRegistry::getInstance()->typeIndex[$typeId] = clone $block;
            foreach ($block->generateStatePermutations() as $v) {
                RuntimeBlockStateRegistry::getInstance()->fillStaticArrays($v->getStateId(), $v);
            }
        })->call(RuntimeBlockStateRegistry::getInstance(), $newFarmLand);

        $reflection = new ReflectionClass(VanillaBlocks::class);
        /** @var array<string, Block> $blocks */
        $blocks = $reflection->getStaticPropertyValue("members");
        $blocks[mb_strtoupper("farmland")] = clone $newFarmLand;
        $reflection->setStaticPropertyValue("members", $blocks);

    }

    public static function registerBlocks(): void
    {
        self::registerSimpleBlock(BlockTypeNames::END_PORTAL, EndPortal::END_PORTAL(), ["end_portal"]);
        self::Grindstone();
    }

    /**
     * @param string[] $stringToItemParserNames
     * @noinspection PhpDeprecationInspection
     */
    private static function registerSimpleBlock(string $id, Block $block, array $stringToItemParserNames): void
    {
        RuntimeBlockStateRegistry::getInstance()->register($block);

        GlobalBlockStateHandlers::getDeserializer()->mapSimple($id, fn () => clone $block);
        GlobalBlockStateHandlers::getSerializer()->mapSimple($block, $id);

        foreach ($stringToItemParserNames as $name) {
            StringToItemParser::getInstance()->registerBlock($name, fn () => clone $block);
        }
    }

    public static function Grindstone(): void
    {
        $id = BlockTypeNames::GRINDSTONE;
        $block = new Grindstone(new BlockIdentifier(BlockTypeIds::newId()), "minecraft:grindstone", new BlockTypeInfo(BlockBreakInfo::instant()));
        self::register($block, [$id]);

        GlobalBlockStateHandlers::getDeserializer()->map(
            $id,
            fn (BlockStateReader $reader): Grindstone => (clone $block)
                ->setFacing($reader->readLegacyHorizontalFacing())
                ->setAttachmentType($reader->readBellAttachmentType())
        );
        GlobalBlockStateHandlers::getSerializer()->map(
            $block,
            fn (Grindstone $block) => BlockStateWriter::create($id)
                ->writeLegacyHorizontalFacing($block->getFacing())
                ->writeBellAttachmentType($block->getAttachmentType())
        );
    }

    private static function register(Block $block, array $stringToItemParserNames, bool $addToCreative = true): void
    {
        RuntimeBlockStateRegistry::getInstance()->register($block);
        foreach ($stringToItemParserNames as $name) {
            StringToItemParser::getInstance()->registerBlock($name, fn () => clone $block);
        }
        if ($addToCreative) {
            CreativeInventory::getInstance()->add($block->asItem());
        }
    }

}
