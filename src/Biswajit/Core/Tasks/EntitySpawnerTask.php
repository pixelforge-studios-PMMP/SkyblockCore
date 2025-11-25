<?php

declare(strict_types=1);

namespace Biswajit\Core\Tasks;

use Biswajit\Core\API;
use Biswajit\Core\Entitys\Vanilla\Chicken;
use Biswajit\Core\Entitys\Vanilla\Cow;
use Biswajit\Core\Entitys\Vanilla\Creeper;
use Biswajit\Core\Entitys\Vanilla\Pig;
use Biswajit\Core\Entitys\Vanilla\Sheep;
use Biswajit\Core\Entitys\Vanilla\Skeleton;
use Biswajit\Core\Entitys\Vanilla\Spider;
use Biswajit\Core\Entitys\Vanilla\Zombie;
use Biswajit\Core\Skyblock;
use pocketmine\entity\Location;
use pocketmine\math\AxisAlignedBB;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Config;

class EntitySpawnerTask extends Task
{
    private const ENTITY_CLASSES = [
        'zombie' => Zombie::class,
        'creeper' => Creeper::class,
        'skeleton' => Skeleton::class,
        'spider' => Spider::class,
        'pig' => Pig::class,
        'sheep' => Sheep::class,
        'cow' => Cow::class,
        'chicken' => Chicken::class
    ];

    public function onRun(): void
    {
        $server = Server::getInstance();

        if ($server->getTicksPerSecond() < 16) {
            return;
        }

        $config = new Config(Skyblock::getInstance()->getDataFolder() . "entity.yml", Config::YAML, []);
        $entities = $config->getAll();

        foreach ($entities as $entityType => $locations) {
            if (!isset(self::ENTITY_CLASSES[$entityType])) {
                continue;
            }

            if (!is_array($locations) || !isset($locations[0])) {
                $locations = [$locations];
            }

            foreach ($locations as $data) {
                $world = $server->getWorldManager()->getWorldByName($data['world']);
                if ($world === null) {
                    continue;
                }

                $location = new Location($data['x'], $data['y'], $data['z'], $world, 0, 0);

                $boundingBox = new AxisAlignedBB(
                    $data['x'] - 2,
                    $data['y'] - 2,
                    $data['z'] - 2,
                    $data['x'] + 2,
                    $data['y'] + 2,
                    $data['z'] + 2
                );
                $existingEntities = $world->getNearbyEntities($boundingBox);
                $entityExists = false;
                foreach ($existingEntities as $entity) {
                    if (get_class($entity) === self::ENTITY_CLASSES[$entityType]) {
                        $entityExists = true;
                        break;
                    }
                }

                $entityClass = self::ENTITY_CLASSES[$entityType];

                if (!$entityExists) {
                    $entity = new $entityClass($location);
                    $entity->spawnToAll();
                }
            }
        }
    }
}
