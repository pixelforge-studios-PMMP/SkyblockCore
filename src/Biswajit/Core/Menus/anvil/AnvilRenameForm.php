<?php

namespace Biswajit\Core\Menus\anvil;

use Biswajit\Core\Managers\EconomyManager;
use Biswajit\Core\Skyblock;
use pocketmine\player\Player;
use dktapps\pmforms\CustomForm;
use pocketmine\utils\TextFormat;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\CustomFormResponse;
use pocketmine\block\BlockTypeIds;

class AnvilRenameForm extends CustomForm
{
    public function __construct(Player $player)
    {
        $mymoney = EconomyManager::getMoney($player);
        parent::__construct(
            "§eRename Your Item: " . $player->getInventory()->getItemInHand()->getName(),
            [
                new Label("element0", "§eYour Money: §a" . $mymoney . "\n" . "§r§e§lRENAME\n§r§7Tap to rename\nRename Cost: 250"),
                new Input("element1", TextFormat::GOLD . "Name:", "§aYour Custom Name")
            ],
            function (Player $player, CustomFormResponse $response) use ($mymoney): void {
                $renamed = $response->getString("element1");
                $item = $player->getInventory()->getItemInHand();
                if ($item->getTypeId() == BlockTypeIds::AIR) {
                    $player->sendMessage(Skyblock::$prefix . "§r§bYou Can't Rename This Item");
                    return;
                }
                $coins = $mymoney;
                if ($coins >= 250) {
                    EconomyManager::subtractMoney($player, 250);
                    $item->setCustomName($renamed);
                    $player->getInventory()->setItemInHand($item);
                    $player->sendMessage("§aItem has been succesfully renamed " . $renamed);
                } else {
                    $player->sendMessage("§cYou don't have enough money you need 250");
                }
            }
        );
    }
}
