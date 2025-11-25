<?php

namespace Biswajit\Core\Entitys\Minion;

use pocketmine\inventory\SimpleInventory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\Tag;

trait MinionHandler
{
    /** @var Tag|CompoundTag */
    public Tag|CompoundTag $minionInfo;

    /** @var Int */
    private int $invSize;

    /** @var Int */
    private int $level;

    /** @var ?SimpleInventory */
    public ?SimpleInventory $minionInv = null;

    /** @var array */
    private array $Upgrades;

    private int $currentTick = 0;

    protected bool $canWork = true;

    /** @var mixed */
    public mixed $target = null;

    public const ARMOR = array(
      "cobblestone" => array(255, 255, 255),
      "coal ore" => array(0, 0, 0),
      "gold ore" => array(255, 238, 88),
      "iron ore" => array(224, 224, 224),
      "lapis lazulie ore" => array(13, 71, 161),
      "diamond ore" => array(102, 255, 255),
      "emerald ore" => array(102, 255, 102),
      "redstone ore" => array(244, 67, 54),
      "wheat" => array(255, 224, 130),
      "pumpkin" => array(255, 167, 38),
      "melon" => array(76, 175, 80),
      "carrot" => array(161, 137, 79),
      "potato" => array(165, 155, 62),
      "oak log" => array(88, 6, 52),
      "dark oak log" => array(6, 8, 8),
      "spruce log" => array(30, 136, 229),
      "birch log" => array(152, 12, 92),
      "jungle log" => array(6, 188, 0),
      "acacia log" => array(148, 87, 146),
      "zombie" => array(22, 120, 82),
      "skeleton" => array(224, 224, 224),
      "chicken" => array(255, 255, 255),
      "creeper" => array(0, 255, 9),
      "cow" => array(43, 28, 10),
      "pig" => array(185, 109, 148),
      "spider" => array(185, 109, 148),
      "sheep" => array(209, 252, 247)
      );

    public function getInvSize(int $level): int
    {
        if ($level === 1) {
            $size = 3;
        }
        if ($level === 2) {
            $size = 5;
        }
        if ($level === 3) {
            $size = 8;
        }
        if ($level === 4) {
            $size = 11;
        }
        if ($level === 5) {
            $size = 15;
        }
        return $size;
    }

    public function getTargetId(): string
    {
        return $this->minionInfo->getString("TargetId");
    }

    public function getType(): string
    {
        return $this->minionInfo->getString("Type");
    }


    public function getUpgrades(): array
    {
        return $this->Upgrades;
    }

    public function getMinionInventory(): SimpleInventory
    {
        return $this->minionInv;
    }

    private function setUpgrade(string $upgrade, int $num): void
    {
        $Upgrades = $this->getUpgrades();
        if ($num === 1) {
            $this->Upgrades = array($upgrade, $Upgrades[1], $Upgrades[2]);
        } elseif ($num === 2) {
            $this->Upgrades = array($Upgrades[0], $upgrade, $Upgrades[2]);
        } elseif ($num === 3) {
            $this->Upgrades = array($Upgrades[0], $Upgrades[1], $upgrade);
        }
    }

    private function hasUpgrade(string $upgrade): bool
    {
        $Upgrades = $this->getUpgrades();
        if (in_array($upgrade, $Upgrades)) {
            return true;
        } else {
            return false;
        }
    }

    public function getInventorySize(): int
    {
        return $this->invSize;
    }

    public function setInventorySize(int $size): void
    {
        $this->invSize = $size;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function getSpeedInTicks(): int
    {
        return $this->getSpeedInSeconds($this->getLevel()) * 20;
    }

    public function getSpeedInSeconds(int $level): int
    {
        if ($level === 1) {
            $speed = 30;
        }
        if ($level === 2) {
            $speed = 25;
        }
        if ($level === 3) {
            $speed = 20;
        }
        if ($level === 4) {
            $speed = 15;
        }
        if ($level === 5) {
            $speed = 10;
        }
        return $speed;
    }

}
