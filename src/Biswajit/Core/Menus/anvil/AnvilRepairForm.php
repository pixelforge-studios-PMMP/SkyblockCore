<?php

namespace Biswajit\Core\Menus\anvil;

use pocketmine\item\Tool;
use pocketmine\item\Armor;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use dktapps\pmforms\MenuOption;

class AnvilRepairForm extends MenuForm
{
    public function __construct(Player $player)
    {
        $xp = $player->getXpManager()->getXpLevel();
        parent::__construct(
            "§2§l« §r§aREPAIR §2§l»§r",
            "§aRepair Your Item\n§aRepair Cost: 20\n§eYour Xp: §a " . $xp,
            [
                new MenuOption("§a§lREPAIR\n§r§8Tap to repair"),
                new MenuOption("§e§lBACK\n§r§8Tap to go back")
            ],
            function (Player $player, int $selected) use ($xp): void {
                switch ($selected) {
                    case 0:
                        if ($xp >= 20) {
                            $item = $player->getInventory()->getItemInHand();
                            if ($item instanceof Armor or $item instanceof Tool) {
                                $player->getInventory()->removeItem($item->setCount(1));
                                $newitem = clone $item->setCount(1);
                                if ($item->hasCustomName()) {
                                    $newitem->setCustomName($item->getCustomName());
                                }
                                if ($item->hasEnchantments()) {
                                    foreach ($item->getEnchantments() as $enchants) {
                                        $newitem->addEnchantment($enchants);
                                    }
                                }
                                $player->getInventory()->addItem($newitem);
                                $player->sendMessage("§aItem has been successfully fixed");
                                $player->getXpManager()->subtractXp(20);
                            } else {
                                $player->sendMessage("§cHold the item in your hand!");
                            }
                        } else {
                            $player->sendMessage("§cYou don't have enough xp you need 20");
                        }
                        break;
                    case 1:
                        $player->sendForm(new AnvilMainForm());
                        break;
                }
            }
        );
    }
}
