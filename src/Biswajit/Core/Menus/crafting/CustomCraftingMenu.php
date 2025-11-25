<?php

declare(strict_types=1);

namespace Biswajit\Core\Menus\crafting;

use Biswajit\Core\API;
use Biswajit\Core\Managers\CraftingManager;
use Biswajit\Core\Player;
use Biswajit\Core\Skyblock;
use pocketmine\item\ItemTypeIds;
use pocketmine\block\VanillaBlocks;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\VanillaItems;
use pocketmine\scheduler\ClosureTask;

class CustomCraftingMenu
{
    public static function CraftingMenu(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->setName("§eCraftingTable");

        $menu->setListener(function (InvMenuTransaction $transaction) use ($menu): InvMenuTransactionResult {
            $itemOut = $transaction->getOut();
            $player = $transaction->getPlayer();
            $itemTakenOut = $transaction->getItemClicked();
            $inv = $transaction->getAction()->getInventory();
            $slot = $transaction->getAction()->getSlot();

            if (!$player instanceof Player) {
                return $transaction->discard();
            }

            if ($itemTakenOut->getName() === "§r §7 §r") {
                return $transaction->discard();
            }

            if ($slot === 49) {
                $player->removeCurrentWindow();
                return $transaction->discard();
            }

            Skyblock::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($menu): void {
                $inv = $menu->getInventory();
                foreach (CraftingManager::getAllRecipes() as $recipeFile) {
                    if (!is_dir(Skyblock::getInstance()->getDataFolder() . "recipes/" . $recipeFile)) {
                        $file = Skyblock::getInstance()->getRecipeFile(str_replace(".yml", "", $recipeFile));
                        $key = $file->get("Recipe");
                        $ingredients = array_map(fn ($k) => CraftingManager::safeDecode($k), array_slice($key, 0, 9));
                        $result = CraftingManager::safeDecode($key[9]);
                        $slots = [10, 11, 12, 19, 20, 21, 28, 29, 30];

                        $matched = true;
                        foreach ($slots as $i => $slot) {
                            if (!CraftingManager::matchItemsBasic($ingredients[$i], $inv->getItem($slot))) {
                                $matched = false;
                                break;
                            }
                        }

                        if ($matched) {
                            $inv->setItem(25, $result);
                            break;
                        } else {
                            $inv->setItem(25, VanillaBlocks::AIR()->asItem());
                        }
                    }
                }
            }), 1);

            if ($slot === 25) {
                $craftingSlots = [10, 11, 12, 19, 20, 21, 28, 29, 30];
                $gridItems = array_map(fn ($s) => $inv->getItem($s), $craftingSlots);
                $matchedRecipe = null;

                foreach (CraftingManager::getAllRecipes() as $recipeFile) {
                    $path = Skyblock::getInstance()->getDataFolder() . "recipes/" . $recipeFile;
                    if (is_dir($path)) {
                        continue;
                    }

                    $file = Skyblock::getInstance()->getRecipeFile(str_replace(".yml", "", $recipeFile));
                    $key = $file->get("Recipe");
                    $ingredients = array_map(fn ($k) => CraftingManager::safeDecode($k), array_slice($key, 0, 9));

                    $matched = true;
                    foreach ($ingredients as $i => $ingredient) {
                        if (!CraftingManager::matchItemsBasic($ingredient, $gridItems[$i])) {
                            $matched = false;
                            break;
                        }
                    }

                    if ($matched) {
                        $matchedRecipe = $ingredients;
                        break;
                    }
                }

                $inv->setItem(25, VanillaItems::AIR());
                if ($matchedRecipe !== null && $player->getInventory()->canAddItem($itemOut)) {
                    foreach ($matchedRecipe as $i => $ingredient) {
                        CraftingManager::subtractItemCount($inv, $craftingSlots[$i], $ingredient);
                    }

                    $player->getInventory()->addItem($itemOut);
                }

                return $transaction->discard();
            }

            return $transaction->continue();
        });

        $menu->setInventoryCloseListener(function (Player $player, $inv): void {
            $slots = [10, 11, 12, 19, 20, 21, 28, 29, 30];
            foreach ($slots as $slot) {
                $item = $inv->getItem($slot);
                $inv->setItem($slot, VanillaBlocks::AIR()->asItem());
                CraftingManager::giveOrDropItem($player, $item);
            }
        });

        $slots = [
                0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 13,
                14, 15, 16, 17, 18, 22,
                23, 24, 26, 27, 31,
                32, 33, 34, 35, 36, 37, 38, 39, 40,
                41, 42, 43, 44
        ];

        foreach ($slots as $slot) {
            $menu->getInventory()->setItem($slot, API::getItem("glass2")->setCustomName("§r §7 §r"));
        }

        $slots2 = [
         45, 46, 47, 48, 50, 51, 52, 53
        ];

        foreach ($slots2 as $slot2) {
            $menu->getInventory()->setItem($slot2, API::getItem("glass")->setCustomName("§r §7 §r"));
        }

        $menu->getInventory()->setItem(49, API::getItem("close")->setCustomName("§r §cClose §r"));

        $menu->send($player);
    }

    public static function createRecipe(Player $player, string $recipeName): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->setName("§eCreateRecipe");
        $menu->setListener(
            function (InvMenuTransaction $transaction) use ($recipeName): InvMenuTransactionResult {
                $player = $transaction->getPlayer();
                $itemTakenOut = $transaction->getItemClicked();
                $inv = $transaction->getAction()->getInventory();

                if ($itemTakenOut->getName() === "§r §7") {
                    return $transaction->discard();
                }

                if ($itemTakenOut->getName() === "§r §7 §r") {
                    if ($itemTakenOut->getTypeId() === ItemTypeIds::fromBlockTypeId(VanillaBlocks::MOB_HEAD()->getTypeId())) {
                        $A = CraftingManager::safeEncode($inv->getItem(11));
                        $B = CraftingManager::safeEncode($inv->getItem(12));
                        $C = CraftingManager::safeEncode($inv->getItem(13));
                        $D = CraftingManager::safeEncode($inv->getItem(20));
                        $E = CraftingManager::safeEncode($inv->getItem(21));
                        $F = CraftingManager::safeEncode($inv->getItem(22));
                        $G = CraftingManager::safeEncode($inv->getItem(29));
                        $H = CraftingManager::safeEncode($inv->getItem(30));
                        $I = CraftingManager::safeEncode($inv->getItem(31));
                        $R = CraftingManager::safeEncode($inv->getItem(24));
                        $N = $recipeName;

                        $array = [$A, $B, $C, $D, $E, $F, $G, $H, $I, $R, $N];
                        $number = count(CraftingManager::getAllRecipes()) + 1;
                        $name = "Recipe-$number";
                        $recipeFile = Skyblock::getInstance()->getRecipeFile($name);
                        $recipeFile->setNested("Recipe", $array);
                        $recipeFile->save();
                        $player->removeCurrentWindow();
                    }
                    return $transaction->discard();
                }

                return $transaction->continue();
            }
        );

        $inv = $menu->getInventory();
        $slots = [
                0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
                14, 15, 16, 17, 18, 19,
                23, 25, 26, 27, 28,
                32, 33, 34, 35, 36, 37, 38, 39, 40,
                41, 42, 43, 44, 45, 46, 47, 48, 49,
                50, 51, 52
        ];

        foreach ($slots as $slot) {
            $inv->setItem($slot, API::getItem("glass2")->setCustomName("§r §7"));
        }
        $inv->setItem(53, VanillaBlocks::MOB_HEAD()->asItem()->setCustomName("§r §7 §r"));
        $menu->send($player);
    }

    public static function Recipe(Player $player, string $recipeName): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->setName("§eRecipe");
        $menu->setListener(
            function (InvMenuTransaction $transaction) use ($menu): InvMenuTransactionResult {
                return $transaction->discard();
            }
        );

        $inv = $menu->getInventory();
        $blankItem = VanillaBlocks::GLASS()->asItem()->setCustomName("§r §7 §r");

        $slots = [
                0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
                14, 15, 16, 17, 18, 19, 23, 25, 26, 27, 28,
                32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44,
                45, 46, 47, 48, 49, 50, 51, 52, 53
        ];

        foreach ($slots as $slot) {
            $inv->setItem($slot, clone $blankItem);
        }

        $recipe = null;
        foreach (CraftingManager::getAllRecipes() as $recipeFile) {
            $file = Skyblock::getInstance()->getRecipeFile(str_replace(".yml", "", $recipeFile));
            $key = $file->get("Recipe");
            if (((string)$key[10]) === ((string)$recipeName)) {
                $recipe = $key;
            }
        }

        if (!is_null($recipe)) {
            $A = CraftingManager::safeDecode($recipe[0]);
            $B = CraftingManager::safeDecode($recipe[1]);
            $C = CraftingManager::safeDecode($recipe[2]);
            $D = CraftingManager::safeDecode($recipe[3]);
            $E = CraftingManager::safeDecode($recipe[4]);
            $F = CraftingManager::safeDecode($recipe[5]);
            $G = CraftingManager::safeDecode($recipe[6]);
            $H = CraftingManager::safeDecode($recipe[7]);
            $I = CraftingManager::safeDecode($recipe[8]);
            $R = CraftingManager::safeDecode($recipe[9]);
            $AItem = $A !== "null" ? $A : VanillaItems::AIR();
            $BItem = $B !== "null" ? $B : VanillaItems::AIR();
            $CItem = $C !== "null" ? $C : VanillaItems::AIR();
            $DItem = $D !== "null" ? $D : VanillaItems::AIR();
            $EItem = $E !== "null" ? $E : VanillaItems::AIR();
            $FItem = $F !== "null" ? $F : VanillaItems::AIR();
            $GItem = $G !== "null" ? $G : VanillaItems::AIR();
            $HItem = $H !== "null" ? $H : VanillaItems::AIR();
            $IItem = $I !== "null" ? $I : VanillaItems::AIR();
            $RItem = $R !== "null" ? $R : VanillaItems::AIR();
            $inv->setItem(11, $AItem);
            $inv->setItem(12, $BItem);
            $inv->setItem(13, $CItem);
            $inv->setItem(20, $DItem);
            $inv->setItem(21, $EItem);
            $inv->setItem(22, $FItem);
            $inv->setItem(29, $GItem);
            $inv->setItem(30, $HItem);
            $inv->setItem(31, $IItem);
            $inv->setItem(24, $RItem);
        }

        $menu->send($player);
    }
}
