<?php

declare(strict_types=1);

namespace Biswajit\Core\Items\items;

use Biswajit\Core\Items\skyblockItems;
use Biswajit\Core\Menus\items\SkyblockMenu;
use customiesdevs\customies\item\ItemComponentsTrait;
use customiesdevs\customies\item\ItemComponents;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\ItemUseResult;
use pocketmine\item\ItemIdentifier;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class menuItem extends skyblockItems implements ItemComponents
{
    use ItemComponentsTrait;

    public function __construct()
    {
        parent::__construct(new ItemIdentifier(ItemTypeIds::newId()), "skyblockMenu");
        $this->initComponent("skyblock_menu");
        $this->setCustomName("§r§aSkyblock Menu §7( Right Click )§r");
        $this->addGlow();
        $this->setDescription(["§r§7View All Of Your Skyblock Progress Including Your Skills,\n§7Collections, Recipes And More!\n\n§r§eClick To Open!"]);
    }

    public function isFireProof(): bool
    {
        return true;
    }

    public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems): ItemUseResult
    {
        $player->sendForm(new SkyblockMenu($player));
        return ItemUseResult::SUCCESS();
    }
}
