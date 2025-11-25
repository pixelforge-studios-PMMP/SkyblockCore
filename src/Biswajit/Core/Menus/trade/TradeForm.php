<?php

namespace Biswajit\Core\Menus\trade;

use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use dktapps\pmforms\MenuOption;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;

class TradeForm extends MenuForm
{
    public function __construct()
    {
        parent::__construct("§etrade menu", "§bHere You Can Trade With Specific Items", [
            new MenuOption("§e4x Dirt = §b1x Grass", new FormIcon("textures/icon/grass", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§e2x CobbleStone = §b1x Stone", new FormIcon("textures/icon/rocks", FormIcon::IMAGE_TYPE_PATH))
        ], function (Player $sender, int $selected): void {
            switch ($selected) {
                case 0:
                    $this->trade($sender, VanillaBlocks::DIRT()->asItem(), VanillaBlocks::GRASS()->asItem()->setCount(1), 4);
                    break;
                case 1:
                    $this->trade($sender, VanillaBlocks::COBBLESTONE()->asItem(), VanillaBlocks::STONE()->asItem()->setCount(1), 2);
                    break;
            }
        });
    }

    public function getItemCount(Player $player, Item $id): int
    {
        $count = 0;
        foreach ($player->getInventory()->getContents() as $item) {
            if ($item->getStateId() === $id->getStateId()) {
                $count = $count + $item->getCount();
            }
        }
        return $count;
    }

    public function trade(Player $player, Item $item1, Item $item2, int $quantity1): void
    {
        if ($this->getItemCount($player, $item1) < $quantity1) {
            $player->sendMessage(" §cYou Don't Have Enough Items To Trade!");
            return;
        }
        $inv = $player->getInventory();
        $inv->removeItem($item1->setCount($quantity1));
        $inv->addItem($item2);
    }
}
