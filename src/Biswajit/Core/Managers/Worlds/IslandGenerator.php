<?php

declare(strict_types=1);

namespace Biswajit\Core\Managers\Worlds;

use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\world\generator\Generator;

// Does absolutely nothing, on generation and population, hence it's all void
class IslandGenerator extends Generator
{
    public function __construct(int $seed, string $preset)
    {
        parent::__construct($seed, $preset);
    }

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
        /** @phpstan-var Chunk $chunk */
        $chunk = $world->getChunk($chunkX, $chunkZ);
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
    }

}
