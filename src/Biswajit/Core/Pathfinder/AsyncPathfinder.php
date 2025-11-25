<?php

declare(strict_types=1);

namespace Biswajit\Core\Pathfinder;

use Biswajit\Core\Entitys\Vanilla\Spider;
use Biswajit\Core\Entitys\Vanilla\VanillaEntity;
use IvanCraft623\Pathfinder\Path;
use IvanCraft623\Pathfinder\PathFinder;
use IvanCraft623\Pathfinder\evaluator\WalkNodeEvaluator;
use pocketmine\math\Vector3;
use Closure;

use function sqrt;

class AsyncPathfinder
{
    private ?Path $currentPath = null;
    private bool $isCalculating = false;
    private int $jumpTicks = 0;

    public function __construct(
        private VanillaEntity $entity,
        private WalkNodeEvaluator $evaluator
    ) {
        $this->evaluator->setEntitySize($entity->getSize());
        $this->evaluator->setEntityBoundingBox($entity->getBoundingBox());
        $this->evaluator->setEntityOnGround($entity->isOnGround());
        $this->evaluator->setMaxUpStep(1.0);
        $this->evaluator->setMaxFallDistance(3);
    }

    public function findPathAsync(Vector3 $target, Closure $onComplete): void
    {
        if ($this->isCalculating) {
            return;
        }

        $this->isCalculating = true;

        PathFinder::findPathAsync(
            function (Path $path) use ($onComplete) {
                $this->currentPath = $path;
                $this->isCalculating = false;
                $onComplete($path);
            },
            $this->evaluator,
            $this->entity->getWorld(),
            $this->entity->getPosition(),
            $target,
            1000,
            50.0,
            1
        );
    }

    public function getCurrentPath(): ?Path
    {
        return $this->currentPath;
    }

    public function isCalculating(): bool
    {
        return $this->isCalculating;
    }

    public function updateMovement(): void
    {
        if ($this->currentPath === null || $this->currentPath->isDone()) {
            return;
        }

        $nextPos = $this->currentPath->getNextEntityPosition($this->entity);
        $location = $this->entity->getLocation();
        $speed = $this->entity->getSpeed();

        if ($this->entity->isOnGround() || $this->jumpTicks === 0) {
            $motion = $this->entity->getMotion();
            if ($this->jumpTicks <= 0) {
                $xDist = $nextPos->x - $location->x;
                $zDist = $nextPos->z - $location->z;
                $yaw = atan2($zDist, $xDist) / M_PI * 180 - 90;
                if ($yaw < 0) {
                    $yaw += 360.0;
                }

                $this->entity->setRotation($yaw, 0);

                $x = -1 * sin(deg2rad($yaw));
                $z = cos(deg2rad($yaw));
                $directionVector = (new Vector3($x, 0, $z))->normalize()->multiply($speed);

                $motion->x = $directionVector->x;
                $motion->z = $directionVector->z;

                $facing = $this->entity->getHorizontalFacing();
                $frontBlock = $location->getWorld()->getBlock($location->add(0, 0.5, 0)->getSide($facing));
                if (!$frontBlock->canBeFlowedInto()) {
                    if ($this->entity instanceof Spider) {
                        $motion->y = 0.42 + $this->entity->getGravity();
                        $this->jumpTicks = 5;
                    } else {
                        $aboveBlock = $location->getWorld()->getBlock($location->add(0, 1.5, 0)->getSide($facing));
                        if ($aboveBlock->canBeFlowedInto()) {
                            $motion->y = 0.42 + $this->entity->getGravity();
                            $this->jumpTicks = 5;
                        }
                    }
                }

                $this->entity->setMotion($motion);
            }
        }

        if ($this->jumpTicks > 0) {
            $this->jumpTicks--;
        }

        $dx = $nextPos->x - $this->entity->getPosition()->x;
        $dz = $nextPos->z - $this->entity->getPosition()->z;
        $distance = sqrt($dx * $dx + $dz * $dz);

        if ($distance <= 0.1) {
            $this->currentPath->advance();
        }
    }

    public function resetPath(): void
    {
        $this->currentPath = null;
    }

}
