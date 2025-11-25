<?php


declare(strict_types=1);

namespace Biswajit\Core\Menus\minion;

use Biswajit\Core\API;
use Biswajit\Core\Entitys\Minion\MinionEntity;
use Biswajit\Core\Managers\EconomyManager;
use Biswajit\Core\Player;
use Biswajit\Core\Skyblock;
use Biswajit\Core\Utils\Utils;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\SimpleInventory;
use pocketmine\item\VanillaItems;

class MinionMenu
{
    public static function MinionMenu(Player $player, MinionEntity $minion): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setName("Minion Inventory");
        $menu->setListener(
            function (InvMenuTransaction $transaction) use ($minion, $player): InvMenuTransactionResult {
                $itemOut = $transaction->getOut();
                $inv = $transaction->getAction()->getInventory();
                $itemOutName = $transaction->getOut()->getCustomName();
                if ($itemOutName === "§r §eCollect-All §r") {
                    foreach (array_reverse($minion->getMinionInventory()->getContents(), true) as $slot => $items) {
                        Utils::giveItems($player, $items);
                        $minion->getMinionInventory()->removeItem($items);
                        $inv->removeItem($items);
                    }
                } elseif ($itemOutName === "§r §cRemove §r") {
                    $minion->flagForDespawn();
                    $player->removeCurrentWindow();
                    $pos = $minion->getPosition();
                    foreach (array_reverse($minion->getMinionInventory()->getContents(), true) as $slot => $items) {
                        $minion->getWorld()->dropItem($pos, $items);
                        $minion->getMinionInventory()->removeItem($items);
                    }
                    $minion->getWorld()->setBlock($pos, VanillaBlocks::AIR());
                    Utils::giveItems($player, $minion->getEgg());

                } elseif ($transaction->getAction()->getSlot() === 52) {
                    $New_Level = $minion->getLevel() + 1;
                    if (5 >= $New_Level) {
                        if (EconomyManager::getMoney($player) >= 60000 * $minion->getLevel()) {
                            EconomyManager::subtractMoney($player, $minion->getLevel() * 60000);
                            $minion->setLevel($New_Level);
                            $player->removeCurrentWindow();
                            $player->sendMessage(Skyblock::$prefix . API::getMessage("minion-upgraded", ["{level}" => (string)$New_Level]));
                            $contents = $minion->getMinionInventory()->getContents();
                            unset($minion->minionInv);
                            $size = $minion->getInvSize($New_Level);
                            $minion->minionInv = new SimpleInventory($size);
                            $minion->minionInv->setContents($contents);
                            $minion->setInventorySize($size);
                        } else {
                            $player->sendMessage(Skyblock::$prefix . API::getMessage("no_money"));
                            $player->removeCurrentWindow();
                        }
                    }
                }

                $array_1 = array(12, 13, 14, 15, 16, 21, 22, 23, 24, 25, 30, 31, 32, 33, 34);
                // array for minion updates
                $array_2 = array(10, 19, 28);

                $slot = $transaction->getAction()->getSlot();
                if (in_array($slot, $array_1)) {
                    if ($itemOutName !== "§r §7 §r") {
                        $inv->removeItem($itemOut);
                        $minion->getMinionInventory()->removeItem($itemOut);
                        if ($player->getInventory()->canAddItem($itemOut)) {
                            $player->getInventory()->addItem($itemOut);
                        } else {
                            $world = $player->getWorld();
                            $world->dropItem($player->getPosition(), $itemOut);
                        }
                    }
                }
                //Todo Minion Updates

                return $transaction->discard();
            }
        );
        $inv = $menu->getInventory();
        if (5 > $minion->getLevel()) {
            $price = 60000 * $minion->getLevel();
            $speed = $minion->getSpeedInSeconds($minion->getLevel() + 1) . "s";
        } else {
            $price = "Max";
            $speed = $minion->getSpeedInSeconds($minion->getLevel()) . "s";
        }
        $inv->setItem(0, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(1, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(2, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(3, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(4, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(5, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(6, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(7, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(8, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(9, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(10, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(11, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(12, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(13, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(14, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(15, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(16, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(17, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(18, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(19, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(20, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(21, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(22, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(23, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(24, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(25, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(26, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(27, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(28, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(29, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(30, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(31, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(32, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(33, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(34, API::getItem("glass")->setCustomName("§r §7 §r"));
        $inv->setItem(35, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(36, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(37, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(38, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(39, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(40, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(41, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(42, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(43, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(44, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(45, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(46, API::getItem("close")->setCustomName("§r §cRemove §r"));
        $inv->setItem(47, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(48, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(49, API::getItem("collect_all")->setCustomName("§r §eCollect-All §r"));
        $inv->setItem(50, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(51, API::getItem("glass2")->setCustomName("§r §7 §r"));
        $inv->setItem(52, API::getItem("upgrade_slot")->setCustomName("§r§aUpgrade §r\n§r§ePrice §7$price\n§r§eSpeed §7$speed"));
        $inv->setItem(53, API::getItem("glass2")->setCustomName("§r §7 §r"));
        for ($i = 1; $i <= $minion->getInventorySize(); $i++) {
            if (5 >= $i) {
                $slot = $i + 11;
                $inv->setItem($slot, VanillaItems::AIR());
            } elseif (10 >= $i) {
                $slot = $i + 15;
                $inv->setItem($slot, VanillaItems::AIR());
            } elseif (15 >= $i) {
                $slot = $i + 19;
                $inv->setItem($slot, VanillaItems::AIR());
            }
        }
        foreach (array_reverse($minion->getMinionInventory()->getContents(), true) as $slot => $items) {
            $inv->addItem($items);
        }

        $inv->setItem(10, VanillaItems::AIR());
        $inv->setItem(19, VanillaItems::AIR());
        $inv->setItem(28, VanillaItems::AIR());
        $Upgrades = $minion->getUpgrades();
        // todo add Update Items!
        $menu->send($player);
    }
}
