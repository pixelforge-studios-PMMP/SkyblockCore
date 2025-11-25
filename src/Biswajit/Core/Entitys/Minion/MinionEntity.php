<?php

declare(strict_types=1);

namespace Biswajit\Core\Entitys\Minion;

use Biswajit\Core\Menus\minion\MinionMenu;
use pocketmine\color\Color;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\inventory\SimpleInventory;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;

abstract class MinionEntity extends Human
{
    use MinionHandler;

    public function __construct(Location $location, Skin $skin, CompoundTag $nbt)
    {
        parent::__construct($location, $skin, $nbt);
        $this->minionInfo = $nbt->getTag("Information");
        $this->invSize = $this->minionInfo->getInt("InvSize");
        $this->level = $this->minionInfo->getInt("Level");
        $size = $this->getInvSize($this->level);
        $this->minionInv = new SimpleInventory($size);

        if (!is_null($nbt->getTag("Information"))) {
            if (!is_null($this->minionInfo->getTag("Resources"))) {
                $resourcesTag = $this->minionInfo->getTag("Resources");
                if ($resourcesTag->getType() === NBT::TAG_List) {
                    $resources = [];
                    foreach ($resourcesTag as $item) {
                        $resources[] = Item::nbtDeserialize($item);
                    }
                    $this->minionInv->setContents($resources);
                }
            }

            if (!is_null($this->minionInfo->getTag("Upgrades"))) {
                $this->Upgrades = explode(", ", $this->minionInfo->getString("Upgrades"));
            } else {
                $this->Upgrades = array("Null", "Null", "Null");
            }
        }

        $colour = self::ARMOR[$this->getTargetId()];
        $this->getArmorInventory()->setChestplate(VanillaItems::LEATHER_TUNIC()->setCustomColor(new Color($colour[0], $colour[1], $colour[2])));
        $this->getArmorInventory()->setLeggings(VanillaItems::LEATHER_PANTS()->setCustomColor(new Color($colour[0], $colour[1], $colour[2])));
        $this->getArmorInventory()->setBoots(VanillaItems::LEATHER_BOOTS()->setCustomColor(new Color($colour[0], $colour[1], $colour[2])));
        $this->setUp();
        $this->setNameTag("");
        $this->setNameTagAlwaysVisible(true);
        $this->setScale(0.6);
    }

    //Todo: Offline Calculation Plan On Future!!

    public function onUpdate(int $currentTick): bool
    {
        $update = parent::onUpdate($currentTick);

        if (++$this->currentTick >= $this->getSpeedInTicks()) {
            $this->currentTick = 0;
            $this->onTick();
        }

        return $update;
    }

    public function attack(EntityDamageEvent $source): void
    {
        $source->cancel();
        if ($source instanceof EntityDamageByEntityEvent) {
            MinionMenu::MinionMenu($source->getDamager(), $this);
        }
    }

    public function addItem(Item|array $items): bool
    {
        if (!is_array($items)) {
            $items = [$items];
        }

        foreach ($items as $item) {
            if (!$this->minionInv->canAddItem($item)) {
                $this->setNameTag(" §a§lInventory Full! ");
                return false;
            }
        }

        foreach ($items as $item) {
            $this->minionInv->addItem($item);
        }

        return true;
    }

    public function lookAt(Vector3 $target): void
    {
        $xDiff = $target->x - $this->getPosition()->x;
        $zDiff = $target->z - $this->getPosition()->z;
        $yaw = atan2($zDiff, $xDiff) / M_PI * 180 - 90;
        $this->setRotation($yaw, 0);
    }

    public function getName(): string
    {
        return "Minion";
    }

    public function move(float $dx, float $dy, float $dz): void
    {
        //noop
    }

    public function saveNBT(): CompoundTag
    {
        $nbt = parent::saveNBT();

        $Upgrades = implode(", ", $this->getUpgrades());
        $informationTag = CompoundTag::create()
            ->setString("Upgrades", $Upgrades)
            ->setInt("Level", $this->getLevel())
            ->setString("Type", $this->getType())
            ->setString("TargetId", $this->getTargetId())
            ->setInt("InvSize", $this->getInventorySize());
        if (isset($this->Inv)) {
            $resourcesList = new ListTag(
                array_map(
                    fn (Item $item) => $item->nbtSerialize(),
                    $this->minionInv->getContents()
                ),
                NBT::TAG_Compound
            );
            $informationTag->setTag('Resources', $resourcesList);
        }
        $nbt->setTag("Information", $informationTag);

        return $nbt;
    }

    abstract public function onTick(): void;
    abstract public function setUp(): void;
    abstract public function getEgg(): Item;
}
