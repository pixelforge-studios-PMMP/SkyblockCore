<?php

declare(strict_types=1);

namespace Biswajit\Core\Managers;

use Biswajit\Core\Player;
use Biswajit\Core\Utils\Utils;
use muqsit\invmenu\InvMenu;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\math\Vector3;

class CraftingManager
{
    use ManagerBase;

    public static function WORKBENCH(): InvMenu
    {
        return InvMenu::create(self::getPlugin()::INV_MENU_TYPE_WORKBENCH);
    }

    public static function giveOrDropItem(Player $player, Item $item): void
    {
        if ($item->getName() !== "Air") {
            if ($player->getInventory()->canAddItem($item)) {
                $player->getInventory()->addItem($item);
            } else {
                $loc = $player->getLocation();
                $dropPos = new Vector3(floor($loc->x), floor($loc->y), floor($loc->z));
                $player->getWorld()->dropItem($dropPos, $item);
            }
        }
    }

    public static function getAllRecipes(): array
    {
        $array = [];
        foreach (scandir(self::getDataFolder() . "recipes") as $key => $recipeFile) {
            if (is_file(self::getDataFolder() . "recipes/$recipeFile")) {
                $array[] = $recipeFile;
            }
        }
        return $array;
    }

    public static function subtractItemCount($inv, int $slot, $recipeItem): void
    {
        if ($recipeItem !== "null") {
            $current = $inv->getItem($slot);
            $newCount = $current->getCount() - $recipeItem->getCount();
            $inv->setItem($slot, $current->setCount(max(0, $newCount)));
        }
    }

    public static function matchItemsBasic($itemA, ?Item $itemB): bool
    {
        if ($itemB->isNull()) {
            $item = "null";
        } else {
            $item = $itemB;
        }

        $isEmptyA = $itemA === "null";
        $isEmptyB = $item === "null";

        if ($isEmptyA && $isEmptyB) {
            return true;
        }

        if ($isEmptyA || $isEmptyB) {
            return false;
        }

        return $itemA->getName() === $itemB->getName() && $itemA->getStateId() === $itemB->getStateId() && $itemA->getCount() <= $itemB->getCount();
    }

    public static function safeEncode(Item $item): ?string
    {
        if ($item->isNull() || $item->getTypeId() === VanillaBlocks::AIR()->asItem()->getTypeId()) {
            return "null";
        }

        return Utils::encodeSingleItemToB64($item);
    }

    public static function safeDecode($decodeItem)
    {
        if ($decodeItem === "null" || $decodeItem === "Null") {
            return "null";
        }
        return Utils::decodeSingleItemFromB64($decodeItem);
    }
}
