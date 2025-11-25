<?php

declare(strict_types=1);

namespace Biswajit\Core\Listeners\Inventory;

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\transaction\action\DropItemAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;

class InventoryTransaction implements Listener
{
    public function onInventoryTransaction(InventoryTransactionEvent $event): void
    {
        $trans = $event->getTransaction();
        $p = $trans->getSource();


        foreach ($trans->getInventories() as $inventory) {
            if (!$inventory instanceof PlayerInventory) {
                continue;
            }

            foreach ($trans->getActions() as $action) {
                if ($action instanceof SlotChangeAction) {
                    if ($action->getSlot() === 8) {
                        $event->cancel();
                        return;
                    }
                }

                if ($action instanceof DropItemAction) {
                    if ($p->getInventory()->getHotbarSlotItem(8)->equals($action->getTargetItem())) {
                        $event->cancel();
                        return;
                    }
                }
            }
        }
    }
}
