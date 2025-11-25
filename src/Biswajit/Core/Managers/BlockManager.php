<?php

declare(strict_types=1);

namespace Biswajit\Core\Managers;

use Biswajit\Core\Skyblock;
use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\Crops;
use pocketmine\item\StringToItemParser;
use pocketmine\math\Vector3;
use pocketmine\promise\Promise;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\format\Chunk;
use pocketmine\world\Position;
use pocketmine\world\World;

class BlockManager
{
    use ManagerBase;

    private static array $blockStates = [];
    private static int $blockIterator = 0;

    public static array $forest = [
       BlockTypeIds::OAK_WOOD,
       BlockTypeIds::SPRUCE_WOOD,
       BlockTypeIds::JUNGLE_WOOD,
       BlockTypeIds::ACACIA_WOOD,
       BlockTypeIds::BIRCH_WOOD,
       BlockTypeIds::DARK_OAK_WOOD,
       BlockTypeIds::OAK_LOG,
       BlockTypeIds::SPRUCE_LOG,
       BlockTypeIds::JUNGLE_LOG,
       BlockTypeIds::ACACIA_LOG,
       BlockTypeIds::BIRCH_LOG,
       BlockTypeIds::DARK_OAK_LOG
    ];

    public static array $farming = [
        BlockTypeIds::WHEAT,
        BlockTypeIds::BEETROOTS,
        BlockTypeIds::LIT_PUMPKIN,
        BlockTypeIds::PUMPKIN,
        BlockTypeIds::MELON_STEM,
        BlockTypeIds::MELON,
        BlockTypeIds::CARROTS,
        BlockTypeIds::POTATOES,
        BlockTypeIds::SUGARCANE
    ];

    public static array $mineBlocks = [
        BlockTypeIds::STONE,
        BlockTypeIds::DIAMOND_ORE,
        BlockTypeIds::GOLD_ORE,
        BlockTypeIds::REDSTONE_ORE,
        BlockTypeIds::IRON_ORE,
        BlockTypeIds::COAL_ORE,
        BlockTypeIds::EMERALD_ORE,
        BlockTypeIds::OBSIDIAN,
        BlockTypeIds::COBBLESTONE,
        BlockTypeIds::LAPIS_LAZULI_ORE
    ];

    public function onEnable(): void
    {

        $file = $this->getDataFolder() . "data.json";
        if (is_file($file)) {
            $contents = file_get_contents($file);
            if (!is_string($contents)) {
                return;
            }

            foreach (json_decode($contents, true) as $blockData) {
                $x = $blockData["x"];
                $y = $blockData["y"];
                $z = $blockData["z"];
                $world = $this->getServer()->getWorldManager()->getWorldByName($blockData["world"]);
                $name = $blockData["name"];

                // this will force the server to wait for the results so that it doesn't crash when chunk is unloaded
                /** @phpstan-ignore-next-line */
                if ($world instanceof World && $world->requestChunkPopulation($x >> Chunk::COORD_BIT_SIZE, $z >> Chunk::COORD_BIT_SIZE, null) instanceof Promise) {
                    $block = StringToItemParser::getInstance()->parse($name)->getBlock();
                    $world->setBlock(new Position($x, $y, $z, $world), $block, false);

                    self::getPlugin()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($world, $x, $y, $z, $block): void {
                        $block = $world->getBlockAt($x, $y, $z);
                        if ($block instanceof Crops) {
                            $block->setAge($block->getMaxAge());
                        }
                    }), 20);
                }
            }
            @unlink($file);
        }
    }

    public static function Disable(): void
    {
        file_put_contents(Skyblock::getInstance()->getDataFolder() . "data.json", json_encode(self::$blockStates));
    }

    public static function mineBlockRespawn(Block $block, $pos): void
    {
        $world = $pos->getWorld();
        $x = $pos->x;
        $y = $pos->y;
        $z = $pos->z;

        $i = self::$blockIterator++;
        self::$blockStates[$i] = [
            "x" => $x,
            "y" => $y,
            "z" => $z,
            "world" => $world->getFolderName(),
            "name" => $block->getName()
        ];

        Skyblock::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($world, $x, $y, $z, $block, $i): void {
            $world->setBlock(new Vector3($x, $y, $z), $block);
            if (isset(self::$blockStates[$i])) {
                unset(self::$blockStates[$i]);
            }
        }), Skyblock::getInstance()->getConfig()->get("RESPAWN-TIME") * 20);
    }
}
