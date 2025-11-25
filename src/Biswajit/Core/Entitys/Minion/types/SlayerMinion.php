<?php

declare(strict_types=1);

namespace Biswajit\Core\Entitys\Minion\types;

use Biswajit\Core\API;
use Biswajit\Core\Entitys\Minion\MinionEntity;
use Biswajit\Core\Entitys\Vanilla\Chicken;
use Biswajit\Core\Entitys\Vanilla\Cow;
use Biswajit\Core\Entitys\Vanilla\Creeper;
use Biswajit\Core\Entitys\Vanilla\Pig;
use Biswajit\Core\Entitys\Vanilla\Sheep;
use Biswajit\Core\Entitys\Vanilla\Skeleton;
use Biswajit\Core\Entitys\Vanilla\Spider;
use Biswajit\Core\Entitys\Vanilla\Zombie;
use Biswajit\Core\Utils\Utils;
use pocketmine\block\BlockTypeIds;
use pocketmine\entity\animation\DeathAnimation;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\utils\TextFormat;

class SlayerMinion extends MinionEntity
{
    private const SEARCH_RADIUS = 2;

    private const ENTITY_CLASSES = [
        'zombie'   => Zombie::class,
        'creeper'  => Creeper::class,
        'skeleton' => Skeleton::class,
        'spider'   => Spider::class,
        'pig'      => Pig::class,
        'sheep'    => Sheep::class,
        'cow'      => Cow::class,
        'chicken'  => Chicken::class,
    ];

    public function onTick(): void
    {
        if (!$this->isAreaClear()) {
            $this->setNameTag("§cThis location isn't perfect! :(");
            $this->setNameTagAlwaysVisible(true);
            return;
        }

        $this->setNameTag("");
        $this->setNameTagAlwaysVisible(false);

        if ($this->target === null) {
            $this->target = $this->getTargetingEntity();

            if ($this->getTargetingEntity() === null) {
                $this->spawnEntity();
                return;
            }

            return;
        }

        $this->killEntity();
    }

    private function isAreaClear(): bool
    {
        $world = $this->getWorld();
        $cx = (int)floor($this->getPosition()->x);
        $cy = (int)floor($this->getPosition()->y);
        $cz = (int)floor($this->getPosition()->z);

        for ($dx = -2; $dx <= 2; $dx++) {
            for ($dz = -2; $dz <= 2; $dz++) {
                $block = $world->getBlockAt($cx + $dx, $cy, $cz + $dz);

                if ($block->getTypeId() !== BlockTypeIds::AIR) {
                    return false;
                }
            }
        }

        return true;
    }

    private function killEntity(): void
    {
        $inv = $this->getInventory();
        $entity = $this->getEntity();

        if ($entity === null) {
            $this->target = null;
            return;
        }

        $entityPos = $entity->getPosition();
        $basePos = $this->getPosition();

        $positions = [];
        $range = 2;
        for ($x = -$range; $x <= $range; $x++) {
            for ($z = -$range; $z <= $range; $z++) {
                if ($x === 0 && $z === 0) {
                    continue;
                }
                $positions[] = $basePos->add($x, 0, $z);
            }
        }

        $isInRange = false;
        foreach ($positions as $pos) {
            if ((int)floor($pos->x) === (int)floor($entityPos->x) && (int)floor($pos->z) === (int)floor($entityPos->z)) {
                $isInRange = true;
                break;
            }
        }

        if (!$isInRange) {
            return;
        }

        $this->target = null;

        $entity->broadcastAnimation(new DeathAnimation($entity));
        $entity->flagForDespawn();

        $this->broadcastMiningAnimation();
        $this->lookAt($entity->getPosition());

        $drops = $entity->getDrops(VanillaItems::DIAMOND_SWORD());
        $this->addItem($drops);
    }

    private function spawnEntity(): void
    {
        $positions = [];
        $range = 2;
        $basePos = $this->getPosition();
        for ($x = -$range; $x <= $range; $x++) {
            for ($z = -$range; $z <= $range; $z++) {
                if ($x === 0 && $z === 0) {
                    continue;
                }
                $positions[] = $basePos->add($x, 0, $z);
            }
        }

        if (empty($positions)) {
            return;
        }

        $position = $positions[array_rand($positions)];
        $location = Location::fromObject($position, $this->getWorld(), mt_rand(0, 359));

        $entityType = strtolower($this->getTargetId());
        if (!isset(self::ENTITY_CLASSES[$entityType])) {
            return;
        }

        $entityClass = self::ENTITY_CLASSES[$entityType];
        $entity = new $entityClass($location);
        $entity->spawnToAll();
    }

    private function broadcastMiningAnimation(): void
    {
        $animatePacket = AnimatePacket::create($this->getId(), AnimatePacket::ACTION_SWING_ARM);
        $this->getWorld()->broadcastPacketToViewers($this->getPosition(), $animatePacket);
    }

    public function getTargetingEntity(): ?int
    {
        $expanded = $this->getBoundingBox()->expandedCopy(self::SEARCH_RADIUS, self::SEARCH_RADIUS, self::SEARCH_RADIUS);
        $nearby = $this->getWorld()->getNearByEntities($expanded, $this);

        if (empty($nearby)) {
            return null;
        }

        $candidates = [];
        foreach ($nearby as $candidate) {
            if ($candidate instanceof Zombie || $candidate instanceof Skeleton || $candidate instanceof Creeper || $candidate instanceof Spider || $candidate instanceof Cow || $candidate instanceof Pig || $candidate instanceof Chicken || $candidate instanceof Sheep && $candidate->isAlive()) {
                $candidates[] = $candidate;
            }
        }

        if (empty($candidates)) {
            return null;
        }

        $chosen = $candidates[array_rand($candidates)];
        return $chosen->getId();
    }

    private function getEntity(): mixed
    {
        $entity = $this->getWorld()->getEntity($this->target);
        return $entity;
    }

    public function getEgg(): Item
    {
        return $this->createMinionItem($this->getTargetId());
    }

    private function createMinionItem(string $name): Item
    {
        $itemName = strtolower($name);
        $name = str_replace(' ', '_', $itemName);

        $minionItem = API::getItem($name);
        $minionItem->setCustomName($this->formatMinionName($itemName))
            ->setLore($this->getMinionLore());

        $minionItem->getNamedTag()->setTag("Information", $this->minionInfo);
        return $minionItem;
    }

    private function formatMinionName(string $itemName): string
    {
        return TextFormat::RESET . TextFormat::YELLOW
            . ucfirst($itemName) . ' Minion ' . Utils::getRomanNumeral($this->getLevel());
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
        $this->getInventory()->setItemInHand(VanillaItems::WOODEN_SWORD());
    }
}
