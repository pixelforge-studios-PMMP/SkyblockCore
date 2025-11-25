<?php

namespace Biswajit\Core\Menus\trade;

use Biswajit\Core\API;
use Biswajit\Core\Player;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemTypeIds;

class TradeMenu
{
    /** @var bool */
    private static $ItemsReturned;

    /** @var bool */
    private static $TradeAccepted;

    public static function TradeMenu(Player $player_1, Player $player_2): void
    {
        self::$ItemsReturned = false;
        self::$TradeAccepted = false;

        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->setName("§3Trading");
        $menu->setListener(
            function (InvMenuTransaction $transaction) use ($player_1, $player_2): InvMenuTransactionResult {
                $item = $transaction->getItemClicked();
                $player = $transaction->getPlayer();
                $slot = $transaction->getAction()->getSlot();
                $inv = $transaction->getAction()->getInventory();

                $array_1 = array(0, 1, 2, 3, 9, 10, 11, 12, 18, 19, 20, 21, 27, 28, 29, 30, 36, 37, 38, 39);
                $array_2 = array(5, 6, 7, 8, 14, 15, 16, 17, 23, 24, 25, 26, 32, 33, 34, 35, 41, 42, 43, 44);
                $array_3 = array(46, 47, 48, 49, 50, 51, 52);
                if ($slot === 45) {
                    if ($player->getName() === $player_1->getName()) {
                        if ($item->getTypeId() === ItemTypeIds::fromBlockTypeId(VanillaBlocks::EMERALD()->getTypeId())) {
                            $inv->setItem(45, vanillaBlocks::REDSTONE()->asItem()->setCustomName("§caccept"));
                        } elseif ($item->getTypeId() === ItemTypeIds::fromBlockTypeId(VanillaBlocks::REDSTONE()->getTypeId())) {
                            $inv->setItem(45, vanillaBlocks::EMERALD()->asItem()->setCustomName("§aaccepted"));
                        }
                    }
                } elseif ($slot === 53) {
                    if ($player->getName() === $player_2->getName()) {
                        if ($item->getTypeId() === ItemTypeIds::fromBlockTypeId(VanillaBlocks::EMERALD()->getTypeId())) {
                            $inv->setItem(45, vanillaBlocks::REDSTONE()->asItem()->setCustomName("§caccept"));
                        } elseif ($item->getTypeId() === ItemTypeIds::fromBlockTypeId(VanillaBlocks::REDSTONE()->getTypeId())) {
                            $inv->setItem(53, vanillaBlocks::EMERALD()->asItem()->setCustomName("§aaccepted"));
                        }
                    }
                } elseif (in_array($slot, $array_1)) {
                    if ($player->getName() === $player_1->getName()) {
                        if ($inv->getItem(45)->getTypeId() === ItemTypeIds::fromBlockTypeId(VanillaBlocks::REDSTONE()->getTypeId())) {
                            if (!self::$TradeAccepted) {
                                return $transaction->continue();
                            }
                        }
                    }
                } elseif (in_array($slot, $array_2)) {
                    if ($player->getName() === $player_2->getName()) {
                        if ($inv->getItem(53)->getTypeId() === ItemTypeIds::fromBlockTypeId(VanillaBlocks::REDSTONE()->getTypeId())) {
                            if (!self::$TradeAccepted) {
                                return $transaction->continue();
                            }
                        }
                    }
                } elseif (in_array($slot, $array_3)) {
                    $player->removeCurrentWindow();
                }

                if ($inv->getItem(45)->getTypeId() === ItemTypeIds::fromBlockTypeId(VanillaBlocks::EMERALD()->getTypeId()) && $inv->getItem(53)->getTypeId() === ItemTypeIds::fromBlockTypeId(VanillaBlocks::EMERALD()->getTypeId())) {
                    self::$TradeAccepted = true;
                    if (!self::$ItemsReturned) {
                        foreach ($array_1 as $slot) {
                            $item = $inv->getItem($slot);
                            if ($item->getTypeId() !== ItemTypeIds::fromBlockTypeId(VanillaBlocks::AIR()->getTypeId())) {
                                if ($player_2->getInventory()->canAddItem($item)) {
                                    $player_2->getInventory()->addItem($item);
                                } else {
                                    $world = $player_2->getWorld();
                                    $world->dropItem($player_2->getPosition(), $item);
                                }
                            }
                        }
                        foreach ($array_2 as $slot) {
                            $item = $inv->getItem($slot);
                            if ($item->getTypeId() !== ItemTypeIds::fromBlockTypeId(VanillaBlocks::AIR()->getTypeId())) {
                                if ($player_1->getInventory()->canAddItem($item)) {
                                    $player_1->getInventory()->addItem($item);
                                } else {
                                    $world = $player_1->getWorld();
                                    $world->dropItem($player_1->getPosition(), $item);
                                }
                            }
                        }
                        self::$ItemsReturned = true;
                        $player->removeCurrentWindow();
                        $player_1->sendMessage("§atrade successful");
                        $player_2->sendMessage("§atrade successful");
                    }
                }

                return $transaction->discard();
            }
        );
        $menu->setInventoryCloseListener(
            function (Player $player, $inv) use ($player_1, $player_2): void {
                if (!self::$ItemsReturned) {
                    $array_1 = array(0, 1, 2, 3, 9, 10, 11, 12, 18, 19, 20, 21, 27, 28, 29, 30, 36, 37, 38, 39);
                    $array_2 = array(5, 6, 7, 8, 14, 15, 16, 17, 23, 24, 25, 26, 32, 33, 34, 35, 41, 42, 43, 44);
                    foreach ($array_1 as $slot) {
                        $item = $inv->getItem($slot);
                        if ($item->getTypeId() !== ItemTypeIds::fromBlockTypeId(VanillaBlocks::AIR()->getTypeId())) {
                            if ($player_1->isOnline()) {
                                if ($player_1->getInventory()->canAddItem($item)) {
                                    $player_1->getInventory()->addItem($item);
                                } else {
                                    $world = $player_1->getWorld();
                                    $world->dropItem($player_1->getPosition(), $item);
                                }
                            }
                        }
                    }
                    foreach ($array_2 as $slot) {
                        $item = $inv->getItem($slot);
                        if ($item->getTypeId() !== ItemTypeIds::fromBlockTypeId(VanillaBlocks::AIR()->getTypeId())) {
                            if ($player_2->isOnline()) {
                                if ($player_2->getInventory()->canAddItem($item)) {
                                    $player_2->getInventory()->addItem($item);
                                } else {
                                    $world = $player_2->getWorld();
                                    $world->dropItem($player_2->getPosition(), $item);
                                }
                            }
                        }
                    }
                    self::$ItemsReturned = true;
                    if ($player_1->isOnline()) {
                        $player_1->sendMessage("§ctrade cancelled");
                    }
                    if ($player_2->isOnline()) {
                        $player_2->sendMessage("§ctrade cancelled");
                    }
                }

                foreach ($inv->getViewers() as $hash => $viewer) {
                    if ($viewer->getCurrentWindow() instanceof $inv) {
                        $viewer->removeCurrentWindow();
                    }
                }
            }
        );
        $inv = $menu->getInventory();
        $inv->setItem(4, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(13, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(22, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(31, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(40, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(45, vanillaBlocks::REDSTONE()->asItem()->setCustomName("§aaccept"));
        $inv->setItem(46, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(47, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(48, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(49, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(50, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(51, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(52, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(53, vanillaBlocks::REDSTONE()->asItem()->setCustomName("§aaccept"));
        $menu->send($player_1);
        $menu->send($player_2);
        if (count($inv->getViewers()) < 2) {
            foreach ($inv->getViewers() as $hash => $viewer) {
                if ($viewer->getCurrentWindow() instanceof $inv) {
                    $viewer->removeCurrentWindow();
                    $player_1->sendMessage("§ctrade cancelled");
                    $player_2->sendMessage("§ctrade cancelled");
                }
            }
        }
    }
}
